<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\Payment\Model\ResourceModel\CcGuId;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'ccguid_id';
    protected $_eventPrefix = 'ccguid_collection';
    protected $_eventObject = 'ccguid_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\Payment\Model\CcGuId', 'Forix\Payment\Model\ResourceModel\CcGuId');
    }

}
