<?php


namespace Forix\FanPhoto\Controller\Adminhtml\Photo;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('photo_id');
        
            $model = $this->_objectManager->create('Forix\FanPhoto\Model\Photo')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Photo no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
            unset($data['image_url']);
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Photo.'));
                $this->dataPersistor->clear('forix_fanphoto_photo');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['photo_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Photo.').$e->getMessage());
            }
        
            $this->dataPersistor->set('forix_fanphoto_photo', $data);
            return $resultRedirect->setPath('*/*/edit', ['photo_id' => $this->getRequest()->getParam('photo_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
