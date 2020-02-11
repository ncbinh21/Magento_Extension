<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\InvoicePrint\Controller\Adminhtml\Rule;

use Forix\InvoicePrint\Controller\Adminhtml\AbstractRule;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Edit
 *
 * @package Forix\InvoicePrint\Controller\Adminhtml\Rule
 */
class Save extends AbstractRule
{
    /**
     * @return mixed
     * @throws LocalizedException
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var \Forix\InvoicePrint\Model\Rule $model */
            $model = $this->_ruleFactory->create();

            if (!empty($data['rule_id'])) {
                $model->load($data['rule_id']);
                if ($data['rule_id'] != $model->getId()) {
                    throw new LocalizedException(__('Wrong badge rule.'));
                }
            }
            unset($data['rule_id']);

            if (isset($data['rule']['conditions'])) {
                $data['conditions'] = $data['rule']['conditions'];
                unset($data['rule']);
            }
            $model->loadPost($data);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($model->getData());
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('Rules has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                echo $e->getMessage();
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                echo $e->getMessage();
                $this->messageManager->addErrorMessage($e, __('Something went wrong while saving the rule.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
                $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($data);
                return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
