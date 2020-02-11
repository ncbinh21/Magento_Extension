<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */


namespace Amasty\OrderStatus\Block\Adminhtml;

class Status extends \Magento\Backend\Block\Widget\Grid\Container
{
    public function _construct()
    {
        $this->_controller = 'adminhtml_status';
        $this->_headerText = __('Manage Custom Order Statuses');
        $this->_blockGroup = 'Amasty_OrderStatus';
        $this->_addButtonLabel = 'Add New Custom Order Status';
        parent::_construct();
    }
}
