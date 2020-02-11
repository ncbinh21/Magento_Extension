<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Controller\Adminhtml\Rule;

use Forix\ProductLabel\Controller\Adminhtml\AbstractRule;

/**
 * Class Edit
 *
 * @package Forix\ProductLabel\Controller\Adminhtml\Rule
 */
class Edit extends AbstractRule
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_ruleFactory->create();
        $this->_coreRegistry->register('current_label_rule', $model);

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This rule no longer exists.'));
                $this->_redirect('productlabel/rule/*');
                return;
            }
            $model->getConditions()->setFormName('label_rule_form');
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
            $model->getActions()->setFormName('label_rule_form');
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
