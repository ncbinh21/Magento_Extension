<?php


namespace Forix\Media\Controller\Adminhtml\Video;

class Delete extends \Forix\Media\Controller\Adminhtml\Video
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
        $id = $this->getRequest()->getParam('video_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Forix\Media\Model\Video');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Video.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['video_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Video to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
