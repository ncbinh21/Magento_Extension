<?php

namespace Forix\Payment\Model;


class ShipToCode extends \Magento\Framework\Model\AbstractModel
{
    protected $_eventPrefix = 'forix_payment_shiptocode';
    protected $_idFieldName = 'shiptocode_id';
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\ShipToCode::class);
    }
}