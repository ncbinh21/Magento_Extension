<?php


namespace Forix\FanPhoto\Controller\Adminhtml\Photo;

class Delete extends \Forix\FanPhoto\Controller\Adminhtml\Photo
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
        $id = $this->getRequest()->getParam('photo_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Forix\FanPhoto\Model\Photo');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Photo.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['photo_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Photo to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
