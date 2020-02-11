<?php
/**
 * Copyright © 2016 CommerceExtensions. All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Block\Adminhtml;

class Customer extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_blockGroup = 'CommerceExtensions_GuestToReg';
        $this->_controller = 'adminhtml_customer';
        $this->_headerText = __('Guests To Registered Customers');
        parent::_construct();
		$this->removeButton('add');
    }
}