<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Controller\Adminhtml\Template;

use Yosto\InstagramConnect\Controller\Adminhtml\Template;

/**
 * Class Index
 * @package Yosto\InstagramConnect\Controller\Adminhtml\Template
 */
class Index extends Template
{
    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }

        $resultPage = $this->_resultPageFactory->create();
        $resultPage->setActiveMenu('InstagramConnect_Template::template_index');
        $resultPage->getConfig()->getTitle()->prepend(__('Templates'));
        $resultPage->addBreadcrumb(__('Templates'), __('Templates'));
        $resultPage->addBreadcrumb(
            __('Templates Management'),
            __('Templates Management')
        );
        return $resultPage;
    }

}