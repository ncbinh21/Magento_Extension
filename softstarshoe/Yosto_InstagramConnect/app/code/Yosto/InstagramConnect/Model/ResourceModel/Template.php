<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Model\ResourceModel;

use Yosto\InstagramConnect\Helper\Constant;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Group
 * @package Yosto\InstagramConnect\Model\ResourceModel
 */
class Template extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Constant::INSTAGRAM_TEMPLATE_TABLE, Constant::INSTAGRAM_TEMPLATE_ID);
    }
}