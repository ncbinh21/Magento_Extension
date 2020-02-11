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



namespace Mirasvit\EmailReport\Controller\Report;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use \Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Session\SessionManagerInterface;
use Mirasvit\Email\Api\Repository\QueueRepositoryInterface;
use Mirasvit\EmailReport\Api\Repository\OpenRepositoryInterface;
use Mirasvit\EmailReport\Api\Service\StorageServiceInterface;

class Open extends Action
{
    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;
    /**
     * @var OpenRepositoryInterface
     */
    private $openRepository;
    /**
     * @var QueueRepositoryInterface
     */
    private $queueRepository;
    /**
     * @var StorageServiceInterface
     */
    private $storageService;

    /**
     * Open constructor.
     *
     * @param OpenRepositoryInterface  $openRepository
     * @param SessionManagerInterface  $sessionManager
     * @param QueueRepositoryInterface $queueRepository
     * @param StorageServiceInterface  $storageService
     * @param Context                  $context
     */
    public function __construct(
        OpenRepositoryInterface $openRepository,
        SessionManagerInterface $sessionManager,
        QueueRepositoryInterface $queueRepository,
        StorageServiceInterface $storageService,
        Context $context
    ) {
        $this->openRepository = $openRepository;
        $this->sessionManager = $sessionManager;
        $this->queueRepository = $queueRepository;
        $this->storageService = $storageService;

        parent::__construct($context);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Raw $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_RAW);

        if ($uniqueHash = $this->getRequest()->getParam(StorageServiceInterface::QUEUE_PARAM_NAME)) {
            $queue = $this->queueRepository->getByUniqueHash($uniqueHash);
            if ($queue->getId()
                && $queue->getTrigger()
                && $queue->getTrigger()->getId()
            ) {
                $open = $this->openRepository->create()
                    ->setTriggerId($queue->getTriggerId())
                    ->setQueueId($queue->getId())
                    ->setSessionId($this->sessionManager->getSessionId());

                $this->openRepository->saveIfNotExist($open);
                $this->storageService->persistQueueId($queue->getId());
            }
        }

        $pixel = base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');

        return $resultPage
            ->setHeader('Content-Type', 'image/gif')
            ->setHeader('Content-Length', strlen($pixel))
            ->setContents($pixel);
    }
}
