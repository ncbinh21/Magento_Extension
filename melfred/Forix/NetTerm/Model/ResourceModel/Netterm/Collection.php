<?php


namespace Forix\NetTerm\Model\ResourceModel\Netterm;

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
            \Forix\NetTerm\Model\Netterm::class,
            \Forix\NetTerm\Model\ResourceModel\Netterm::class
        );
    }
}
