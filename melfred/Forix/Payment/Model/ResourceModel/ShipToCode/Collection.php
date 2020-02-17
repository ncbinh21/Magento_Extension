<?php

namespace Forix\Payment\Model\ResourceModel\ShipToCode;


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
            \Forix\Payment\Model\ShipToCode::class,
            \Forix\Payment\Model\ResourceModel\ShipToCode::class
        );
    }

    public function getAddEmailToFilter($email){
        $this->addFieldToFilter('customer_email', $email);
        return $this;
    }

    public function getCustomerNoFilter($customerNo){
        $this->addFieldToFilter('customer_no', $customerNo);
        return $this;
    }
}