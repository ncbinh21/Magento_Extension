<?php

namespace Forix\Payment\Controller\Adminhtml\Order;

use Magento\Framework\App\Action\Context;
use Magento\Sales\Model\OrderFactory;

class PushOrderToSage extends \Magento\Framework\App\Action\Action
{
    protected $sage100Factory;
    protected $collectionOrderFactory;
    protected $orderFactory;
    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Forix\Payment\Model\ResourceModel\OrderSchedule\CollectionFactory $collectionOrderFactory,
        \Forix\Payment\Model\Sage100Factory $sage100Factory,
        Context $context
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderFactory = $orderFactory;
        $this->collectionOrderFactory = $collectionOrderFactory;
        $this->sage100Factory = $sage100Factory;
        parent::__construct($context);
    }

    public function execute()
    {
        $response = [
            'success' => false,
        ];
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $sage100 = $this->sage100Factory->create();
            $order = $this->orderFactory->create()->load($orderId);
            if($salesOrderNo = $sage100->getNextSalesOrderNo()) {
                $order->setSalesOrderNo($salesOrderNo);
                if($sage100->createSalesOrder($order)) {
                    $response = [
                        'success' => true,
                    ];
                    $order = $this->orderRepository->get($orderId);
                    $order->setSalesOrderNo($salesOrderNo);
                    $order->save();
                    $this->updateOrderStatusSage($order, $salesOrderNo, 1);
                }
            }
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
            );
        }
        catch (\Exception $exception) {
            $this->getResponse()->representJson(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonEncode($response)
            );
        }
    }

    public function updateOrderStatusSage($order, $salesOrderNo, $status)
    {
        $checkPushSage = $this->collectionOrderFactory->create()->addFieldToFilter('parent_id', $order->getIncrementId())->setOrder('orderschedule_id', 'DESC')->getFirstItem();
        $checkPushSage->setData('sales_order_no', $salesOrderNo);
        $checkPushSage->setData('status', $status);
        $checkPushSage->save();
    }
}