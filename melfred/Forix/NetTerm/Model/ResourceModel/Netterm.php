<?php


namespace Forix\NetTerm\Model\ResourceModel;

class Netterm extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_netterm_netterm', 'netterm_id');
    }
}
