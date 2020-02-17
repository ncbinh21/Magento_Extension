<?php
namespace Forix\AdvancedAttribute\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

abstract class Banners extends Action{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }
    /**
     * Initialize action
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initResultPage()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_Backend::stores_attributes')
            ->addBreadcrumb(__('Option Banner'), __('Option Banner'));
        return $resultPage;
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Forix_AdvancedAttribute::manage_attribute_banner');
    }
}