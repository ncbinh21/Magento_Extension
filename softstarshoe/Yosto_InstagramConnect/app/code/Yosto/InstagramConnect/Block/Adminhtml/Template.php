<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Class Template
 * @package Yosto\InstagramConnect\Block\Adminhtml
 */
class Template extends Container
{
    /**
     * Override constructor
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Yosto_InstagramConnect';
        $this->_headerText = __('Template Management');
        $this->_addButtonLabel = __('Add Template');
        parent::_construct();
    }
}