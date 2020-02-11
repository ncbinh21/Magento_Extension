<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Block\Adminhtml\Status\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('amostatus_status_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Order Status Information'));
    }
}
