<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Email\Model\Queue;

use Magento\Framework\Mail\MessageFactory as MailMessageFactory;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mirasvit\Email\Api\Data\QueueInterface;
use Mirasvit\Email\Model\Config;
use Mirasvit\Email\Model\Queue;
use Mirasvit\Email\Helper\Data as Helper;
use Mirasvit\Email\Model\ResourceModel\Queue\CollectionFactory as QueueCollectionFactory;
use Mirasvit\Email\Model\Unsubscription;
use Magento\Store\Model\App\Emulation;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\App\Area;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\EmailReport\Api\Service\PreparerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sender
{
    /**
     * @var Unsubscription
     */
    protected $unsubscription;

    /**
     * @var QueueCollectionFactory
     */
    protected $queueCollectionFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var MailMessageFactory
     */
    protected $mailMessageFactory;

    /**
     * @var Emulation
     */
    protected $appEmulation;

    /**
     * @var AppState
     */
    protected $appState;

    /**
     * @var \Magento\Framework\Mail\TransportInterfaceFactory
     */
    protected $mailTransportFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Helper
     */
    protected $helper;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;
    /**
     * @var PreparerInterface
     */
    private $preparer;

    /**
     * @SuppressWarnings(PHPMD)
     * @param TransportBuilder          $transportBuilder
     * @param Unsubscription            $unsubscription
     * @param QueueCollectionFactory    $queueCollectionFactory
     * @param Config                    $config
     * @param DateTime                  $date
     * @param MailMessageFactory        $mailMessageFactory
     * @param Emulation                 $appEmulation
     * @param AppState                  $appState
     * @param TransportInterfaceFactory $mailTransportFactory
     * @param StoreManagerInterface     $storeManager
     * @param Helper                    $helper
     * @param PreparerInterface         $preparer
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        Unsubscription $unsubscription,
        QueueCollectionFactory $queueCollectionFactory,
        Config $config,
        DateTime $date,
        MailMessageFactory $mailMessageFactory,
        Emulation $appEmulation,
        AppState $appState,
        TransportInterfaceFactory $mailTransportFactory,
        StoreManagerInterface $storeManager,
        Helper $helper,
        PreparerInterface $preparer
    ) {
        $this->unsubscription = $unsubscription;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->config = $config;
        $this->date = $date;
        $this->mailMessageFactory = $mailMessageFactory;

        $this->appEmulation = $appEmulation;
        $this->appState = $appState;
        $this->mailTransportFactory = $mailTransportFactory;
        $this->storeManager = $storeManager;
        $this->helper = $helper;
        $this->transportBuilder = $transportBuilder;
        $this->preparer = $preparer;
    }

    /**
     * Send mail
     *
     * @param Queue $queue
     * @param bool  $force
     *
     * @return bool
     */
    public function send($queue, $force = false)
    {
        if (!$this->canSend($queue) && !$force) {
            return false;
        }

        $this->appEmulation->startEnvironmentEmulation($queue->getArgs('store_id'), Area::AREA_FRONTEND, true);
        $subject = $queue->getMailSubject();
        $this->appEmulation->stopEnvironmentEmulation();

        $this->appEmulation->startEnvironmentEmulation($queue->getArgs('store_id'), Area::AREA_FRONTEND, true);
        $body = $queue->getMailContent();
        $body = $this->preparer->prepare($queue, $body);

        $this->appEmulation->stopEnvironmentEmulation();

        $this->appEmulation->startEnvironmentEmulation($queue->getArgs('store_id'), Area::AREA_FRONTEND, true);

        $recipients = explode(',', $queue->getRecipientEmail());
        if ($this->config->isSandbox() && !$queue->getArg('force')) {
            $recipients = explode(',', $this->config->getSandboxEmail());
        }

        foreach ($recipients as $index => $email) {
            $name = $queue->getRecipientName();
            if (count($recipients) > 1) {
                $name .= ' - ' . ($index + 1);
            }
            unset($recipients[$index]);
            $recipients[$name] = $email;
        }

        $copyTo = array_filter(explode(',', $queue->getTrigger()->getCopyEmail()));
        foreach ($copyTo as $bcc) {
            $this->transportBuilder->addBcc($bcc);
        }

        $this->transportBuilder
            ->setMessageType(MessageInterface::TYPE_HTML)
            ->setSubject($subject)
            ->setBody($body)
            ->setFrom($queue->getSenderEmail(), $queue->getSenderName())
            ->setReplyTo($queue->getSenderEmail(), $queue->getSenderName())
            ->addTo($recipients);

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();

        $queue->delivery();

        $this->appEmulation->stopEnvironmentEmulation();

        return true;
    }

    /**
     * Check rules and other conditions
     *
     * @param Queue $queue
     * @return bool
     */
    protected function canSend($queue)
    {
        $args = $queue->getArgs();
        if ($queue->getArg('force')) {
            return true;
        }

        if (time() - strtotime($queue->getScheduledAt()) > 2 * 24 * 60 * 60) {
            $queue->miss(__('Scheduled at %1, attempt to send after 2 days', $queue->getScheduledAt()));
            return false;
        }

        // check unsubscription
        if ($this->unsubscription->isUnsubscribed($queue->getRecipientEmail(), $queue->getTriggerId())) {
            $queue->unsubscribe(__('Customer %1 is unsubscribed', $queue->getRecipientEmail()));
            return false;
        }

        // check rules
        if (!$queue->getTrigger()->validateRules($args)) {
            $queue->cancel(__('Canceled by trigger rules'));
            return false;
        }

        // check limitation
        if (!$this->isValidByLimit($args)) {
            $queue->cancel(__('Canceled by global limitation settings'));
            return false;
        }

        if (!$queue->getTemplate()) {
            $queue->cancel(__('Missed Template'));
            return false;
        }

        return true;
    }

    /**
     * Is valid by limit
     *
     * @param array $args
     * @return bool
     */
    protected function isValidByLimit($args)
    {
        $result = true;
        $emailLimit = $this->config->getEmailLimit();
        $hourLimit = $this->config->getEmailLimitPeriod() * 60 * 60;
        if (in_array(0, [$emailLimit, $hourLimit])) {
            return $result;
        }

        $gmtTimestampMinusLimit = $this->date->timestamp() - $hourLimit;
        $filterDateFrom = $this->date->gmtDate(null, $gmtTimestampMinusLimit);

        $queues = $this->queueCollectionFactory->create()
            ->addFieldToFilter('recipient_email', $args['customer_email'])
            ->addFieldToFilter('status', QueueInterface::STATUS_SENT)
            ->addFieldToFilter('updated_at', ['gt' => $filterDateFrom]);

        if ($queues->count() >= $emailLimit) {
            $result = false;
        }

        return $result;
    }
}
