<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model\Service;

use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Service\CreditmemoService;
use Magento\Sales\Model\Order\Creditmemo;
use Aheadworks\Giftcard\Api\Data\Giftcard\CreditmemoInterface as GiftcardCreditmemoInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Psr\Log\LoggerInterface;
use Aheadworks\Giftcard\Model\Statistics;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterfaceFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterface as HistoryEntityInterface;
use Aheadworks\Giftcard\Api\Data\Giftcard\History\EntityInterfaceFactory as HistoryEntityInterfaceFactory;
use Aheadworks\Giftcard\Model\Source\History\Comment\Action as SourceHistoryCommentAction;
use Aheadworks\Giftcard\Model\Source\History\EntityType as SourceHistoryEntityType;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Aheadworks\Giftcard\Api\Data\OptionInterfaceFactory;

/**
 * Class CreditmemoServicePlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model\Service
 */
class CreditmemoServicePlugin
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
     * @var HistoryActionInterfaceFactory
     */
    private $historyActionFactory;

    /**
     * @var HistoryEntityInterfaceFactory
     */
    private $historyEntityFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param Statistics $statistics
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param HistoryEntityInterfaceFactory $historyEntityFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param OptionInterfaceFactory $optionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        GiftcardRepositoryInterface $giftcardRepository,
        Statistics $statistics,
        HistoryActionInterfaceFactory $historyActionFactory,
        HistoryEntityInterfaceFactory $historyEntityFactory,
        OptionInterfaceFactory $optionFactory,
        DataObjectHelper $dataObjectHelper,
        LoggerInterface $logger
    ) {
        $this->giftcardRepository = $giftcardRepository;
        $this->statistics = $statistics;
        $this->historyActionFactory = $historyActionFactory;
        $this->historyEntityFactory = $historyEntityFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->optionFactory = $optionFactory;
        $this->logger = $logger;
    }

    /**
     * Refund Gift Card product and Gift Card code amount
     *
     * @param CreditmemoService $creditmemoService
     * @param Creditmemo $creditmemo
     * @return Creditmemo
     */
    public function afterRefund(CreditmemoService $creditmemoService, Creditmemo $creditmemo)
    {
        if ($creditmemo->getExtensionAttributes() && $creditmemo->getExtensionAttributes()->getAwGiftcardCodes()) {
            $giftcards = $creditmemo->getExtensionAttributes()->getAwGiftcardCodes();
            /** @var GiftcardCreditmemoInterface $giftcard */
            foreach ($giftcards as $giftcard) {
                try {
                    $giftcardCode = $this->giftcardRepository->get($giftcard->getGiftcardId());

                    if ($giftcardCode->getProductId()) {
                        $this->statistics->updateStatistics(
                            $giftcardCode->getProductId(),
                            $creditmemo->getStoreId(),
                            ['used_amount' => -$giftcard->getBaseGiftcardAmount()]
                        );
                    }
                    $historyObject = $this->createHistoryObject(
                        $creditmemo,
                        SourceHistoryCommentAction::REIMBURSED_FOR_REFUNDED_ORDER
                    );
                    $giftcardCode
                        ->setCurrentHistoryAction($historyObject)
                        ->setBalance($giftcardCode->getBalance() + $giftcard->getBaseGiftcardAmount());
                    $this->giftcardRepository->save($giftcardCode);
                } catch (NoSuchEntityException $e) {
                } catch (\Exception $e) {
                    $this->logger->critical($e);
                }
            }
        }

        foreach ($creditmemo->getAllItems() as $item) {
            if ($item->getOrderItem()->getProductType() != ProductGiftcard::TYPE_CODE) {
                continue;
            }
            try {
                $qty = $item->getQty();
                $productOptionCodes = $item->getOrderItem()->getProductOptionByCode(OptionInterface::GIFTCARD_CODES);
                if (!is_array($productOptionCodes)) {
                    continue;
                }
                $count = 0;
                foreach ($productOptionCodes as $productOptionCode) {
                    if ($count == $qty) {
                        break;
                    }
                    try {
                        $giftcardCode = $this->giftcardRepository->getByCode(
                            $productOptionCode,
                            $creditmemo->getOrder()->getStore()->getWebsiteId()
                        );
                        if ($giftcardCode->getState() == Status::ACTIVE) {
                            $historyObject = $this->createHistoryObject(
                                $creditmemo,
                                SourceHistoryCommentAction::REFUND_GIFTCARD
                            );
                            $giftcardCode
                                ->setCurrentHistoryAction($historyObject)
                                ->setState(Status::DEACTIVATED);
                            $this->giftcardRepository->save($giftcardCode);
                            $count++;
                        }
                    } catch (NoSuchEntityException $e) {
                    }
                }
                $this->statistics->updateStatistics(
                    $item->getOrderItem()->getProductId(),
                    $creditmemo->getStoreId(),
                    [
                        'purchased_qty' => -$qty,
                        'purchased_amount' => -($qty * $item->getBasePrice())
                    ]
                );
            } catch (\Exception $e) {
                $this->logger->critical($e);
            }
        }
        return $creditmemo;
    }

    /**
     * Create History object
     *
     * @param Creditmemo $creditmemo
     * @param int $status
     * @return HistoryActionInterface
     */
    private function createHistoryObject($creditmemo, $status)
    {
        /** @var HistoryEntityInterface $orderHistoryEntityObject */
        $orderHistoryEntityObject = $this->historyEntityFactory->create();
        $orderHistoryEntityObject
            ->setEntityType(SourceHistoryEntityType::ORDER_ID)
            ->setEntityId($creditmemo->getOrder()->getEntityId())
            ->setEntityLabel($creditmemo->getOrder()->getIncrementId());

        /** @var HistoryEntityInterface $creditmemoHistoryEntityObject */
        $creditmemoHistoryEntityObject = $this->historyEntityFactory->create();
        $creditmemoHistoryEntityObject
            ->setEntityType(SourceHistoryEntityType::CREDIT_MEMO_ID)
            ->setEntityId($creditmemo->getEntityId())
            ->setEntityLabel($creditmemo->getIncrementId());

        /** @var HistoryActionInterface $historyObject */
        $historyObject = $this->historyActionFactory->create();
        $historyObject
            ->setActionType($status)
            ->setEntities([$orderHistoryEntityObject, $creditmemoHistoryEntityObject]);
        return $historyObject;
    }
}
