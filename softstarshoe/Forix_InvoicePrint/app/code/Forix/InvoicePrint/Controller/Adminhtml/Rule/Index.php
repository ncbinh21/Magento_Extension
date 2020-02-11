<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Start Shoes
 * Date: 01/03/2018
 * Time: 11:49
 */
namespace Forix\InvoicePrint\Controller\Adminhtml\Rule;

use Forix\InvoicePrint\Controller\Adminhtml\AbstractRule;

/**
 * Class Index
 * @package Forix\InvoicePrint\Controller\Adminhtml\Rule
 */
class Index extends AbstractRule
{

    /**
     * @return void
     */
    public function execute()
    {
        $this->_initAction()->_addBreadcrumb(__('Invoice Print'), __('Invoice Print'));
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Manage Invoice Print Break Page Rules'));
        $this->_view->renderLayout();
    }
}
