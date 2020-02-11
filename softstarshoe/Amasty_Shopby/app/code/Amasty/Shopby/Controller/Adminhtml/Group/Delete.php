<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Controller\Adminhtml\Group;

use Magento\Backend\App\Action;

class Delete extends \Amasty\Shopby\Controller\Adminhtml\Group
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('group_id');
        $model = $this->groupAttrFactory->create();
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the group.'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath('*/*/edit', ['group_id' => $id]);
            }
        }
        $this->messageManager->addError(__('We can\'t find a group to delete.'));

        return $resultRedirect->setPath('*/*/');
    }
}
