<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Controller\Sales\Order;

use Aheadworks\Giftcard\Api\Data\OptionInterface;
use Aheadworks\Giftcard\Model\Product\Type\Giftcard as ProductGiftcard;
use Aheadworks\Giftcard\Model\Source\Giftcard\Status;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Controller\Adminhtml\Order\Creditmemo\NewAction;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;

/**
 * Class NewCreditmemoPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Controller\Sales\Order
 */
class NewCreditmemoPlugin
{
    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var GiftcardRepositoryInterface
     */
    private $giftcardRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Status
     */
    private $sourceGiftcardStatus;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ManagerInterface $messageManager
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param Status $sourceGiftcardStatus
     * @param LoggerInterface $logger
     */
    public function __construct(
        ManagerInterface $messageManager,
        GiftcardRepositoryInterface $giftcardRepository,
        OrderRepositoryInterface $orderRepository,
        Status $sourceGiftcardStatus,
        LoggerInterface $logger
    ) {
        $this->messageManager = $messageManager;
        $this->giftcardRepository = $giftcardRepository;
        $this->orderRepository = $orderRepository;
        $this->sourceGiftcardStatus = $sourceGiftcardStatus;
        $this->logger = $logger;
    }

    /**
     * @param NewAction $subject
     * @return void
     */
    public function beforeExecute($subject)
    {
        try {
            $orderId = $subject->getRequest()->getParam('order_id');
            /** @var Order $order */
            $order = $this->orderRepository->get($orderId);

            $codes = [];
            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() != ProductGiftcard::TYPE_CODE) {
                    continue;
                }
                $productOptionCodes = $item->getProductOptionByCode(OptionInterface::GIFTCARD_CODES);
                if (!is_array($productOptionCodes)) {
                    continue;
                }
                foreach ($productOptionCodes as $productOptionCode) {
                    try {
                        $giftcardCode = $this->giftcardRepository->getByCode(
                            $productOptionCode,
                            $order->getStore()->getWebsiteId()
                        );
                        if ($giftcardCode->getState() != Status::ACTIVE) {
                            $codes[] = $giftcardCode->getCode()
                                . ' (' . $this->sourceGiftcardStatus->getOptionByValue($giftcardCode->getState()) . ')';
                        }
                    } catch (NoSuchEntityException $e) {
                        $codes[] = $productOptionCode . ' (Deleted)';
                    }
                }
            }
            if ($codes) {
                $this->messageManager->addWarningMessage(
                    __('The following Gift Card codes has changed: %1', implode(', ', $codes))
                );
            }
        } catch (\Exception $e) {
            $this->logger->error($e);
        }
    }
}
