<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 12:34 PM
 */
namespace Forix\ImportHelper\Model\ResourceModel\Product\Relations;

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
            '\Forix\ImportHelper\Model\Product\Relations',
            '\Forix\ImportHelper\Model\ResourceModel\Product\Relations'
        );
    }
}