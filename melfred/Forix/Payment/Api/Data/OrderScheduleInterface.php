<?php


namespace Forix\Payment\Api\Data;

interface OrderScheduleInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const ORDERSCHEDULE_ID = 'orderschedule_id';
    const SALES_ORDER_NO = 'sales_order_no';
    const PARENT_ID = 'parent_id';
    const STATUS = 'status';
    const COUNT = 'count';

    /**
     * Get orderschedule_id
     * @return string|null
     */
    public function getOrderscheduleId();

    /**
     * Set orderschedule_id
     * @param string $orderscheduleId
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setOrderscheduleId($orderscheduleId);

    /**
     * Get sales_order_no
     * @return string|null
     */
    public function getSalesOrderNo();

    /**
     * Set sales_order_no
     * @param string $salesOrderNo
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setSalesOrderNo($salesOrderNo);
    /**
     * Get get order increment Id
     * @return string|null
     */
    public function getParentId();

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setParentId($parentId);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Forix\Payment\Api\Data\OrderScheduleExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Forix\Payment\Api\Data\OrderScheduleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Forix\Payment\Api\Data\OrderScheduleExtensionInterface $extensionAttributes
    );

    /**
     * Get status
     * @return string|null
     */
    public function getStatus();

    /**
     * Set status
     * @param string $status
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setStatus($status);

    /**
     * Get count
     * @return string|null
     */
    public function getCount();

    /**
     * Set count
     * @param string $count
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setCount($count);
}
