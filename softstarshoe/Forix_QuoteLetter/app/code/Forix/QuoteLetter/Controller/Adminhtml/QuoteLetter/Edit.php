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

namespace Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter;

class Edit extends \Forix\QuoteLetter\Controller\Adminhtml\QuoteLetter
{

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $model = $this->_quoteLetterBuilder->build($this->getRequest());
        if(!$model){
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }
        $id = $model->getId();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Quoteletter') : __('New Quoteletter'),
            $id ? __('Edit Quoteletter') : __('New Quoteletter')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Quoteletters'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Quoteletter'));
        return $resultPage;
    }
}
