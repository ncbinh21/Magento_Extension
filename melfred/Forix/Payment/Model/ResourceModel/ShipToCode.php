<?php

namespace Forix\Payment\Model\ResourceModel;


class ShipToCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_payment_shiptocode', 'shiptocode_id');
    }
}