<?php

namespace Forix\OrderComments\Helper;

class OrderNote
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory
     */
    protected $collectionFactory;

    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Status\History\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param $orderId
     * @return \Magento\Framework\DataObject
     */
    public function getOrderNote($orderId)
    {
        $historyCollection = $this->collectionFactory->create();
        return $historyCollection->addFieldToFilter('is_customer_notified', 1)
            ->addFieldToFilter('parent_id', $orderId)
            ->getFirstItem();
    }
}