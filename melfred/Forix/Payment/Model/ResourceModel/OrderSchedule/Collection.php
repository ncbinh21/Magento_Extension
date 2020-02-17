<?php


namespace Forix\Payment\Model\ResourceModel\OrderSchedule;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Forix\Payment\Model\OrderSchedule::class,
            \Forix\Payment\Model\ResourceModel\OrderSchedule::class
        );
    }
}
