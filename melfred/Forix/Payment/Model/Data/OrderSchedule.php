<?php


namespace Forix\Payment\Model\Data;

use Forix\Payment\Api\Data\OrderScheduleInterface;

class OrderSchedule extends \Magento\Framework\Api\AbstractExtensibleObject implements OrderScheduleInterface
{

    /**
     * Get orderschedule_id
     * @return string|null
     */
    public function getOrderscheduleId()
    {
        return $this->_get(self::ORDERSCHEDULE_ID);
    }

    /**
     * Set orderschedule_id
     * @param string $orderscheduleId
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setOrderscheduleId($orderscheduleId)
    {
        return $this->setData(self::ORDERSCHEDULE_ID, $orderscheduleId);
    }

    /**
     * Get sales_order_no
     * @return string|null
     */
    public function getSalesOrderNo()
    {
        return $this->_get(self::SALES_ORDER_NO);
    }

    /**
     * Set sales_order_no
     * @param string $salesOrderNo
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setSalesOrderNo($salesOrderNo)
    {
        return $this->setData(self::SALES_ORDER_NO, $salesOrderNo);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Forix\Payment\Api\Data\OrderScheduleExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Forix\Payment\Api\Data\OrderScheduleExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Forix\Payment\Api\Data\OrderScheduleExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * Get status
     * @return string|null
     */
    public function getStatus()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set status
     * @param string $status
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Get get order increment Id
     * @return string|null
     */
    public function getParentId()
    {
        return $this->_get(self::STATUS);
    }

    /**
     * Set parent_id
     * @param string $parentId
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setParentId($parentId)
    {
        return $this->setData(self::PARENT_ID, $parentId);
    }

    /**
     * Get count
     * @return string|null
     */
    public function getCount()
    {
        return $this->_get(self::COUNT);
    }

    /**
     * Set count
     * @param string $count
     * @return \Forix\Payment\Api\Data\OrderScheduleInterface
     */
    public function setCount($count)
    {
        return $this->setData(self::COUNT, $count);
    }
}
