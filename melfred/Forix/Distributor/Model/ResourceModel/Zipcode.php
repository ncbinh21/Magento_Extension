<?php


namespace Forix\Distributor\Model\ResourceModel;

class Zipcode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_distributor_zipcode', 'zipcode_id');
    }
}
