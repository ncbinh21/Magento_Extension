<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Model\ResourceModel\Template;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Yosto\InstagramConnect\Helper\Constant;

/**
 * Class Collection
 * @package Yosto\InstagramConnect\Model\ResourceModel\Template
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'template_id';
    /**
     * Override constructor
     */
    public function _construct()
    {
        $this->_init(Constant::TEMPLATE_MODEL, Constant::TEMPLATE_RESOURCE_MODEL);
    }
}