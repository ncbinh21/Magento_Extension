<?php

namespace Forix\AdvancedAttribute\Block\Adminhtml\Options\Edit;

/**
 * Admin page left menu
 */

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('attributes_banner_options_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Advanced Attribute Information'));
    }
}