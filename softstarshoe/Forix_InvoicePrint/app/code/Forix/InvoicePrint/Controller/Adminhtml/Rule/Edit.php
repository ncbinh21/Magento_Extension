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
 * Class Edit
 *
 * @package Forix\InvoicePrint\Controller\Adminhtml\Rule
 */
class Edit extends AbstractRule
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_initRule();
        if ($id) {
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('invoiceprint/rule/*');
                return;
            }
            $model->getConditions()->setFormName('invoice_rule_form');
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
            $model->getActions()->setFormName('invoice_rule_form');
            $model->getActions()->setJsFormObject(
                $model->getActionsFieldSetId($model->getActions()->getFormName())
            );
        }

        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();

        $this->_addBreadcrumb(
            $id ? __('Edit Badge Rule') : __('New Badge Rule'),
            $id ? __('Edit Badge Rule') : __('New Badge Rule')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getId() ? $model->getName() : __('New Badge Rule')
        );
        $this->_view->renderLayout();
    }
}
