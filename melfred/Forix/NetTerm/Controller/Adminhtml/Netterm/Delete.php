<?php


namespace Forix\NetTerm\Controller\Adminhtml\Netterm;

class Delete extends \Forix\NetTerm\Controller\Adminhtml\Netterm
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
        $id = $this->getRequest()->getParam('netterm_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Forix\NetTerm\Model\Netterm::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the net terms.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['netterm_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a net terms to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
