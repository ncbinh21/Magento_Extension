<?php


namespace Forix\ProductWizard\Controller\Adminhtml\GroupItem;

class Delete extends \Forix\ProductWizard\Controller\Adminhtml\GroupItem
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('group_item_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Forix\ProductWizard\Model\GroupItem');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Group Item.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['group_item_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Group Item to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
