<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\ResourceModel\GroupAttrOption;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'group_option_id';

    protected function _construct()
    {
        $this->_init(
            \Amasty\Shopby\Model\GroupAttrOption::class,
            \Amasty\Shopby\Model\ResourceModel\GroupAttrOption::class
        );
    }
}
