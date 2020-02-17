<?php


namespace Forix\Guide\Controller\Adminhtml\Download;

class Delete extends \Forix\Guide\Controller\Adminhtml\Download
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
        $id = $this->getRequest()->getParam('download_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Forix\Guide\Model\Download::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Download.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['download_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Download to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
