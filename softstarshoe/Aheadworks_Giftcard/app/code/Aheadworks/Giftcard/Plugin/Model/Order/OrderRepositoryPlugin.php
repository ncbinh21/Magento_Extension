<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Model\Order;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Order\CollectionFactory as GiftcardOrderCollectionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Invoice\CollectionFactory as GiftcardInvoiceCollectionFactory;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard\Creditmemo\CollectionFactory
    as GiftcardCreditmemoCollectionFactory;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * Class OrderRepositoryPlugin
 *
 * @package Aheadworks\Giftcard\Plugin\Model\Order
 */
class OrderRepositoryPlugin
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var GiftcardOrderCollectionFactory
     */
    private $giftcardOrderCollectionFactory;

    /**
     * @var GiftcardInvoiceCollectionFactory
     */
    private $giftcardInvoiceCollectionFactory;

    /**
     * @var GiftcardCreditmemoCollectionFactory
     */
    private $giftcardCreditmemoCollectionFactory;

    /**
     * @param OrderExtensionFactory $orderExtensionFactory
     * @param GiftcardOrderCollectionFactory $giftcardOrderCollectionFactory
     * @param GiftcardInvoiceCollectionFactory $giftcardInvoiceCollectionFactory
     * @param GiftcardCreditmemoCollectionFactory $giftcardCreditmemoCollectionFactory
     */
    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        GiftcardOrderCollectionFactory $giftcardOrderCollectionFactory,
        GiftcardInvoiceCollectionFactory $giftcardInvoiceCollectionFactory,
        GiftcardCreditmemoCollectionFactory $giftcardCreditmemoCollectionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->giftcardOrderCollectionFactory = $giftcardOrderCollectionFactory;
        $this->giftcardInvoiceCollectionFactory = $giftcardInvoiceCollectionFactory;
        $this->giftcardCreditmemoCollectionFactory = $giftcardCreditmemoCollectionFactory;
    }

    /**
     * Add Gift Card data to order object
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        return $this->addGiftcardDataToOrder($order);
    }

    /**
     * Add Gift Card data to order object
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $orders
     * @return OrderCollection
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $orders)
    {
        /** @var OrderInterface $order */
        foreach ($orders->getItems() as $order) {
            $this->addGiftcardDataToOrder($order);
        }
        return $orders;
    }

    /**
     * Add Gift Card data to order
     *
     * @param OrderInterface $order
     * @return OrderInterface
     */
    public function addGiftcardDataToOrder($order)
    {
        if ($order->getExtensionAttributes() && $order->getExtensionAttributes()->getAwGiftcardCodes()) {
            return $order;
        }

        $giftcardOrderItems = $this->giftcardOrderCollectionFactory->create()
            ->addFieldToFilter('order_id', $order->getEntityId())
            ->load()
            ->getItems();

        if (!$giftcardOrderItems) {
            return $order;
        }

        $giftcardInvoiceItems = $this->giftcardInvoiceCollectionFactory->create()
            ->addFieldToFilter('order_id', $order->getEntityId())
            ->load()
            ->getItems();
        $giftcardCreditmemoItems = $this->giftcardCreditmemoCollectionFactory->create()
            ->addFieldToFilter('order_id', $order->getEntityId())
            ->load()
            ->getItems();
        /** @var \Magento\Sales\Api\Data\OrderExtension $orderExtension */
        $orderExtension = $order->getExtensionAttributes()
            ? $order->getExtensionAttributes()
            : $this->orderExtensionFactory->create();

        $orderExtension->setBaseAwGiftcardAmount($order->getBaseAwGiftcardAmount());
        $orderExtension->setAwGiftcardAmount($order->getAwGiftcardAmount());
        $orderExtension->setBaseAwGiftcardInvoiced($order->getBaseAwGiftcardInvoiced());
        $orderExtension->setAwGiftcardInvoiced($order->getAwGiftcardInvoiced());
        $orderExtension->setBaseAwGiftcardRefunded($order->getBaseAwGiftcardRefunded());
        $orderExtension->setAwGiftcardRefunded($order->getAwGiftcardRefunded());

        $orderExtension->setAwGiftcardCodes($giftcardOrderItems);
        $orderExtension->setAwGiftcardCodesInvoiced($giftcardInvoiceItems);
        $orderExtension->setAwGiftcardCodesRefunded($giftcardCreditmemoItems);
        $order->setExtensionAttributes($orderExtension);
        return $order;
    }
}
