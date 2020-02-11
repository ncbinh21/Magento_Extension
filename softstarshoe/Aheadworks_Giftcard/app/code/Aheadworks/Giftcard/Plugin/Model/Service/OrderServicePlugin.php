<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model\Service;

use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\OrderInterface as GiftcardOrderInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\Giftcard\Model\Statistics;
use Magento\Sales\Model\Service\OrderService;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Aheadworks\Giftcard\Model\Source\History\Comment\Action as SourceHistoryCommentAction;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Magento\Framework\EntityManager\EntityManager;

/**
 * Class OrderServicePlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model\Service
 */
class OrderServicePlugin
{
    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var Statistics
     */
    private $statistics;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var HistoryEntityInterfaceFactory
     */
    private $historyEntityFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param Statistics $statistics
     * @param OrderRepositoryInterface $orderRepository
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param HistoryEntityInterfaceFactory $historyEntityFactory
     * @param LoggerInterface $logger
     * @param EntityManager $entityManager
     */
    public function __construct(
        GiftcardRepositoryInterface $giftcardRepository,
        Statistics $statistics,
        OrderRepositoryInterface $orderRepository,
        HistoryActionInterfaceFactory $historyActionFactory,
        HistoryEntityInterfaceFactory $historyEntityFactory,
        LoggerInterface $logger,
        EntityManager $entityManager
    ) {
        $this->giftcardRepository = $giftcardRepository;
        $this->statistics = $statistics;
        $this->orderRepository = $orderRepository;
        $this->historyActionFactory = $historyActionFactory;
        $this->historyEntityFactory = $historyEntityFactory;
        $this->logger = $logger;
        $this->entityManager = $entityManager;
    }

    /**
     * Change Gift Card code balance after place order
     *
     * @param OrderService $subject
     * @param OrderInterface $order
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterPlace(OrderService $subject, OrderInterface $order)
    {
        if ($order->getExtensionAttributes() && $order->getExtensionAttributes()->getAwGiftcardCodes()) {
            $orderGiftcards = $order->getExtensionAttributes()->getAwGiftcardCodes();
            /** @var GiftcardOrderInterface $orderGiftcard */
            foreach ($orderGiftcards as $orderGiftcard) {
                try {
                    $giftcardCode = $this->giftcardRepository->get($orderGiftcard->getGiftcardId());
                    $giftcardCode->setBalance($giftcardCode->getBalance() - $orderGiftcard->getBaseGiftcardAmount());

                    /** @var HistoryEntityInterface $orderHistoryEntityObject */
                    $orderHistoryEntityObject = $this->historyEntityFactory->create();
                    $orderHistoryEntityObject
                        ->setEntityType(SourceHistoryEntityType::ORDER_ID)
                        ->setEntityId($order->getEntityId())
                        ->setEntityLabel($order->getIncrementId());

                    /** @var HistoryActionInterface $historyObject */
                    $historyObject = $this->historyActionFactory->create();
                    $historyObject
                        ->setActionType(SourceHistoryCommentAction::APPLIED_TO_ORDER)
                        ->setEntities([$orderHistoryEntityObject]);

                    $giftcardCode->setCurrentHistoryAction($historyObject);
                    $this->giftcardRepository->save($giftcardCode);

                    // Save Gift Card data to gift card order table
                    $orderGiftcard->setOrderId($order->getEntityId());
                    $this->entityManager->save($orderGiftcard);
                    // Update Gift Card Statistics
                    if ($giftcardCode->getProductId()) {
                        $this->statistics->updateStatistics(
                            $giftcardCode->getProductId(),
                            $order->getStoreId(),
                            ['used_amount' => $orderGiftcard->getBaseGiftcardAmount()]
                        );
                    }
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }
        return $order;
    }

    /**
     * Cancel Gift Card code amount
     *
     * @param OrderService $subject
     * @param \Closure $proceed
     * @param int $orderId
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundCancel($subject, \Closure $proceed, $orderId)
    {
        $result = $proceed($orderId);
        if ($result) {
            $order = $this->orderRepository->get($orderId);
            if ($order->getExtensionAttributes() && $order->getExtensionAttributes()->getAwGiftcardCodes()) {
                $giftcards = $order->getExtensionAttributes()->getAwGiftcardCodes();
                /** @var GiftcardOrderInterface $giftcard */
                foreach ($giftcards as $giftcard) {
                    try {
                        $giftcardCode = $this->giftcardRepository->get($giftcard->getGiftcardId());

                        if ($giftcardCode->getProductId()) {
                            $this->statistics->updateStatistics(
                                $giftcardCode->getProductId(),
                                $order->getStoreId(),
                                ['used_amount' => -$giftcard->getBaseGiftcardAmount()]
                            );
                        }

                        /** @var HistoryEntityInterface $orderHistoryEntityObject */
                        $orderHistoryEntityObject = $this->historyEntityFactory->create();
                        $orderHistoryEntityObject
                            ->setEntityType(SourceHistoryEntityType::ORDER_ID)
                            ->setEntityId($order->getEntityId())
                            ->setEntityLabel($order->getIncrementId());

                        /** @var HistoryActionInterface $historyObject */
                        $historyObject = $this->historyActionFactory->create();
                        $historyObject
                            ->setActionType(SourceHistoryCommentAction::REIMBURSED_FOR_CANCELLED_ORDER)
                            ->setEntities([$orderHistoryEntityObject]);

                        $giftcardCode->setCurrentHistoryAction($historyObject);
                        $giftcardCode->setBalance($giftcardCode->getBalance() + $giftcard->getBaseGiftcardAmount());
                        $this->giftcardRepository->save($giftcardCode);
                    } catch (\Exception $e) {
                        $this->logger->critical($e);
                    }
                }
            }
        }

        return $result;
    }
}
