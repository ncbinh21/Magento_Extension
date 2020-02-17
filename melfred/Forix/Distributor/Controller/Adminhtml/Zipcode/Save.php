<?php


namespace Forix\Distributor\Controller\Adminhtml\Zipcode;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Forix\Distributor\Controller\Adminhtml\Zipcode
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    /*public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }*/

    public function __construct(\Magento\Backend\App\Action\Context $context,
                                \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
                                \Magento\Framework\Registry $coreRegistry)
    {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $this->saveLogAction();
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('zipcode_id');
        
            $model = $this->_objectManager->create(\Forix\Distributor\Model\Zipcode::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Zipcode no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);


            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Zipcode.'));
                $this->dataPersistor->clear('forix_distributor_zipcode');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['zipcode_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Zipcode.'));
            }
        
            $this->dataPersistor->set('forix_distributor_zipcode', $data);
            return $resultRedirect->setPath('*/*/edit', ['zipcode_id' => $this->getRequest()->getParam('zipcode_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
