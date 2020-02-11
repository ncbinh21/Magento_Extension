<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_OrderStatus
 */

namespace Amasty\OrderStatus\Controller\Adminhtml\Status;

class Save extends \Magento\Backend\App\Action
{
    public function execute()
    {
        /** @var \Amasty\OrderStatus\Model\Status $status */
        $status = $this->_objectManager->get('Amasty\OrderStatus\Model\Status');

        $alias = '';
        if ($id = $this->getRequest()->getParam('id')) {
            $status->load($id);
            $alias = $status->getAlias();
        }
        try {
            $data = $this->getRequest()->getPostValue();
            if(array_key_exists('parent_state', $data) && is_array($data['parent_state'])) {
                $data['parent_state'] = implode(',', $data['parent_state']);
            }

            $status->setData($data);
            if ($id) {
                $status->setId($id);
                $status->setAlias($alias);
            }
            $storeEmailTemplate = array();
            if (isset($data['store_template'])) {
                $storeEmailTemplate = $data['store_template'];
                unset($data['store_template']);
            }
            $status->save();

            /** @var \Amasty\OrderStatus\Model\Template $template */
            $template = $this->_objectManager->get('Amasty\OrderStatus\Model\Template');
            $template->saveTemplates($storeEmailTemplate, $status);
            $this->messageManager->addSuccess(__('The status has been saved.'));
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', ['id' => $status->getId()]);
            } else {
                $this->_redirect('*/*');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            $this->_redirect('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
            return;
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Amasty_OrderStatus::amostatus');
    }
}
