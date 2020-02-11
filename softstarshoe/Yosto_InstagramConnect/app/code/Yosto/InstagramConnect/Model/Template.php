<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Model;

use Magento\Framework\Model\AbstractModel;
use Yosto\InstagramConnect\Helper\Constant;

/**
 * Class Group
 * @package Yosto\DealGroup\Model
 */
class Template extends AbstractModel
{
    /**
     * Override constructor
     */
    protected function _construct()
    {
        $this->_init(Constant::TEMPLATE_RESOURCE_MODEL);
    }
}