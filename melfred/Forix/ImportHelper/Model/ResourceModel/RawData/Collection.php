<?php

/**
 * Project: LoveMyBubbles.
 * User: Hidro Le
 * Title: Magento Develop
 * Date: 7/31/17
 * Time: 4:26 PM
 */

namespace Forix\ImportHelper\Model\ResourceModel\RawData;

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
            'Forix\ImportHelper\Model\RawData',
            'Forix\ImportHelper\Model\ResourceModel\RawData'
        );
    }
}