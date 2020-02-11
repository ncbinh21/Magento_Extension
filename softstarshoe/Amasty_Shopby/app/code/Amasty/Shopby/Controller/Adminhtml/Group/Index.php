<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller\Adminhtml\Group;

class Index extends \Amasty\Shopby\Controller\Adminhtml\Group
{
    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Amasty_Shopby::group_attributes')
            ->addBreadcrumb(__('Groups'), __('Groups'));
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Group Attributes'));
        return $resultPage;
    }
}
