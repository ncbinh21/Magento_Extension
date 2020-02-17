<?php


namespace Forix\Distributor\Controller\Adminhtml\Zipcode;

class Delete extends \Forix\Distributor\Controller\Adminhtml\Zipcode
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->saveLogAction();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('zipcode_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Forix\Distributor\Model\Zipcode::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Zipcode.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['zipcode_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Zipcode to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
