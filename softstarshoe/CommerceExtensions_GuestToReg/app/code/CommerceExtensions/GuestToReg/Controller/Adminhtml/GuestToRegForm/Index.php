<?php
/**
 * Copyright � 2016 CommerceExtensions . All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Controller\Adminhtml\GuestToRegForm;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;
 
    /**
     * @param \Magento\Backend\App\Action\Context        $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) 
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }
 
    /**
     * Grid List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('CommerceExtensions_GuestToReg::GuestToRegForm');
        $resultPage->getConfig()->getTitle()->prepend(__('Guests To Registered Customers'));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock('CommerceExtensions\GuestToReg\Block\Adminhtml\Customer')
        );
        return $resultPage;

    }
 
    /**
     * Check Grid List Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('CommerceExtensions_GuestToReg::guestcustomer');
    }
	
}