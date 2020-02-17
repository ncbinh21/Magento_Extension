<?php
namespace Forix\AdvancedAttribute\Controller\Adminhtml\Attributes;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;

class Manage extends \Magento\Backend\App\Action {
    protected $_coreRegistry;
    
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ){
         $this->_coreRegistry = $coreRegistry;
         parent::__construct($context);
    }


    public function execute()
    {

        $this->_view->loadLayout();
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Advanced Options'));

        $this->_view->renderLayout();
    }

}