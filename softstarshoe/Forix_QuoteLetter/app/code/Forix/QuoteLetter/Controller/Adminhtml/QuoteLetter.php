<?php
/**
 * Hidro Forix Webdesign. 
 * Copyright (C) 2017  Hidro Le
 * 
 * This file included in Forix/QuoteLetter is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Forix\QuoteLetter\Controller\Adminhtml;

abstract class QuoteLetter extends \Magento\Backend\App\Action
{

    const ADMIN_RESOURCE = 'Forix_QuoteLetter::top_level';
    protected $_coreRegistry;
    protected $_quoteLetterBuilder;
    protected $_resultPageFactory;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter\Builder $quoteLetterBuilder,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_quoteLetterBuilder = $quoteLetterBuilder;
        $this->_resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }
    
    protected function _initQuoteLetter(){
        if(!$this->_coreRegistry->registry('forix_quoteletter_quoteletter')) {
            // 1. Get ID and create model
            $id = $this->getRequest()->getParam('quoteletter_id');
            $model = $this->_quoteLetterBuilder->build($this->getRequest());
            // 2. Initial checking
            if ($id) {
                if (!$model->getId()) {
                    $this->messageManager->addErrorMessage(__('This Quoteletter no longer exists.'));
                    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                    return false;
                }
            }
            $this->_coreRegistry->register('forix_quoteletter_quoteletter', $model);
        }
        return $this->_coreRegistry->registry('forix_quoteletter_quoteletter');
    }
    
    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Forix'), __('Forix'))
            ->addBreadcrumb(__('Quoteletter'), __('Quoteletter'));
        return $resultPage;
    }
}
