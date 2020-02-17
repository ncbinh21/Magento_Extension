<?php


namespace Forix\Guide\Model\ResourceModel\Download;

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
            \Forix\Guide\Model\Download::class,
            \Forix\Guide\Model\ResourceModel\Download::class
        );
    }
}
