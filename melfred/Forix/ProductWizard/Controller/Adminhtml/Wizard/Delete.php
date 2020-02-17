<?php


namespace Forix\ProductWizard\Controller\Adminhtml\Wizard;

class Delete extends \Forix\ProductWizard\Controller\Adminhtml\Wizard
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
        $id = $this->getRequest()->getParam('wizard_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Forix\ProductWizard\Model\Wizard');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Wizard.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['wizard_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Wizard to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
