<?php


namespace Forix\Distributor\Model\ResourceModel\Zipcode;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function getIdFieldName()
    {
        return $this->getResource()->getIdFieldName();
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Forix\Distributor\Model\Zipcode::class,
            \Forix\Distributor\Model\ResourceModel\Zipcode::class
        );
    }

    /**
     * @param array $distributors
     * @return $this
     */
    public function addDistributorToFilter(array $distributors){
        $this->addFieldToFilter('distributor_id', ['in' => $distributors]);
        return $this;
    }

}
