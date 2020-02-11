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
 * @package   mirasvit/module-credit
 * @version   1.0.41
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Credit\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Mirasvit\Credit\Model\Config;
use Magento\Framework\App\Area;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\Context;

class Email extends AbstractHelper
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var PricingHelper
     */
    protected $pricingHelper;

    /**
     * @var TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @param Config           $config
     * @param PricingHelper    $pricingHelper
     * @param TransportBuilder $transportBuilder
     * @param Context          $context
     */
    public function __construct(
        Config $config,
        PricingHelper $pricingHelper,
        TransportBuilder $transportBuilder,
        Context $context
    ) {
        $this->config = $config;
        $this->pricingHelper = $pricingHelper;
        $this->transportBuilder = $transportBuilder;

        parent::__construct($context);
    }

    /**
     * @param \Mirasvit\Credit\Model\Transaction $transaction
     * @param bool                               $force
     * @return bool
     */
    public function sendBalanceUpdateEmail($transaction, $force = false)
    {
        $balance = $transaction->getBalance();

        if (!$balance->getIsSubscribed() || !$this->config->isSendBalanceUpdate()) {
            if (!$force) {
                return false;
            }
        }

        $customer = $balance->getCustomer();
        $recipientEmail = $customer->getEmail();
        $recipientName = $customer->getName();
        $storeId = $customer->getStore()->getId();

        $variables = [
            'customer'            => $customer,
            'store'               => $customer->getStore(),
            'transaction'         => $transaction,
            'balance'             => $balance,
            'transaction_amount'  => $this->pricingHelper->currency($transaction->getBalanceDelta(), true, false),
            'balance_amount'      => $this->pricingHelper->currency($balance->getAmount(), true, false),
            'transaction_message' => $transaction->getEmailMessage(),
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($this->config->getEmailBalanceUpdateTemplate($storeId))
            ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars($variables)
            ->setFrom($this->config->getEmailSender($storeId))
            ->addTo($recipientEmail, $recipientName)
            ->getTransport();

        $transport->sendMessage();

        return true;
    }
}
