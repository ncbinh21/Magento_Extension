<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 28/11/2018
 * Time: 15:29
 */
namespace Forix\Payment\Cron;

use \Forix\Payment\Model\ResourceModel\OrderSchedule\CollectionFactory as OrderScheduleCollectionFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Sales\Model\Order as MagentoOrder;
class SyncSalesOrder
{
    const SCHEDULE_PENDING = 1;
    const SCHEDULE_PROCESSING = 2;
    const SCHEDULE_COMPLETED = 3;

    protected $_sage100Factory;
    protected $_orderScheduleCollectionFactory;
    protected $_logger;
    protected $_orderFactory;
    public function __construct(
        \Magento\Payment\Model\Method\Logger $logger,
        OrderScheduleCollectionFactory $orderScheduleCollectionFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Forix\Payment\Model\Sage100Factory $sage100Factory
    )
    {
        $this->_orderScheduleCollectionFactory =  $orderScheduleCollectionFactory;
        $this->_sage100Factory = $sage100Factory;
        $this->_orderFactory = $orderFactory;
        $this->_logger = $logger;
    }

    public function execute()
    {
        $sageService = $this->_sage100Factory->create();
        /**
         * @var $collection \Forix\Payment\Model\ResourceModel\OrderSchedule\Collection
         */
        $collection = $this->_orderScheduleCollectionFactory->create();
        $collection->addFieldToFilter('sales_order_no', ['null' => false]);
        $collection->addFieldToFilter('status', ['nin' => [
            self::SCHEDULE_COMPLETED,
            self::SCHEDULE_PROCESSING,
        ]]);
        $collection->walk([$this,'processOrderItem'], [$sageService]);
    }

    /**
     * @param \Forix\Payment\Model\Data\OrderSchedule $item
     * @param \Forix\Payment\Model\Sage100 $sageService
     */
    public function processOrderItem($item, $sageService){
        $saleOrderNo = $item->getSalesOrderNo();
        try {
            if($saleOrderNo) {
                // Change Item Status ----------
                $item->setStatus(self::SCHEDULE_PROCESSING);
                $item->getResource()->save($item);
                // Change Item Status ----------

                //$this->_sage
                $saleOrder = $sageService->getSalesOrder($saleOrderNo);
                if($saleOrder->getStatus()) {
                    // Change Order Status ----------
                        $order = $this->_orderFactory->create();
                        $order->loadByIncrementId($item->getParentId());
                        $order->setStatus($saleOrder->getStatus());
                        $order->getResource()->save($order);
                    // Change Order Status ----------
                    // Change Item Status ----------
                        $item->setStatus(self::SCHEDULE_COMPLETED);
                        $item->getResource()->save($item);
                    // Change Item Status ----------
                }
            }
        }catch (\Exception $e){
            // Revert Item Status ----------
                $item->setStatus(self::SCHEDULE_PENDING);
                $item->getResource()->save($item);
            // Revert Item Status ----------
            $this->_logger->debug(
                [
                    'method' => 'processOrderItem',
                    'data' => $saleOrderNo,
                    'message' => $e->getMessage()
                ]
            );
        }
    }

}