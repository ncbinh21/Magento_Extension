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
 * @package   mirasvit/module-email-report
 * @version   1.0.5
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\EmailReport\Observer;


use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;
use Mirasvit\EmailReport\Api\Repository\ClickRepositoryInterface;
use Mirasvit\EmailReport\Api\Service\StorageServiceInterface;

class ReportClick implements ObserverInterface
{
    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;
    /**
     * @var ClickRepositoryInterface
     */
    private $clickRepository;
    /**
     * @var StorageServiceInterface
     */
    private $storageService;

    /**
     * ReportClick constructor.
     *
     * @param QueueRepositoryInterface $queueRepository
     * @param SessionManagerInterface     $sessionManager
     * @param ClickRepositoryInterface    $clickRepository
     * @param StorageServiceInterface    $storageService
     */
    public function __construct(
        QueueRepositoryInterface $queueRepository,
        SessionManagerInterface $sessionManager,
        ClickRepositoryInterface $clickRepository,
        StorageServiceInterface $storageService
    ) {
        $this->queueRepository = $queueRepository;
        $this->sessionManager = $sessionManager;
        $this->clickRepository = $clickRepository;
        $this->storageService = $storageService;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var $request \Magento\Framework\App\RequestInterface */
        $request = $observer->getData('request');

        if (
            ($uniqueHash = $request->getParam(StorageServiceInterface::QUEUE_PARAM_NAME))
            // do not treat "open request" as click
            && !($request->getModuleName() == 'emailreport' && $request->getActionName() == 'open')
        ) {
            $queue = $this->queueRepository->getByUniqueHash($uniqueHash);

            if ($queue->getId()
                && $queue->getTrigger()
                && $queue->getTrigger()->getId()
            ) {
                $click = $this->clickRepository->create()
                    ->setTriggerId($queue->getTriggerId())
                    ->setQueueId($queue->getId())
                    ->setSessionId($this->sessionManager->getSessionId());

                $this->clickRepository->saveIfNotExist($click);
                $this->storageService->persistQueueId($queue->getId());
            }
        }
    }
}
