<?php


namespace Forix\ProductWizard\Controller\Adminhtml\Wizard;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;
    protected $_imageUploader;
    protected $_logger;
    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Catalog\Model\ImageUploader $imageUploader,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->_logger = $logger;
        parent::__construct($context);
        $this->_imageUploader = $imageUploader;
    }

    /**
     * Filter category data
     *
     * @deprecated 101.0.8
     * @param array $rawData
     * @return array
     */
    protected function _filterWizardPostData(array $rawData)
    {
        $data = $rawData;
        if (isset($data['base_image']) && is_array($data['base_image'])) {
            if (!empty($data['base_image']['delete'])) {
                $data['base_image'] = null;
            } else {
                if (isset($data['base_image'][0]['name'])) {
                    $data['base_image'] = $data['base_image'][0]['name'];
                } else {
                    unset($data['base_image']);
                }
            }
        }
        return $data;
    }

    /**
     * Gets image name from $value array.
     * Will return empty string in a case when $value is not an array
     *
     * @param array $value Attribute value
     * @return string
     */
    private function getUploadedImageName($value)
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    /**
     * Check if temporary file is available for new image upload.
     *
     * @param array $value
     * @return bool
     */
    private function isTmpFileAvailable($value)
    {
        return is_array($value) && isset($value[0]['tmp_name']);
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
//            $data = $this->_filterWizardPostData($data);
            $id = $this->getRequest()->getParam('wizard_id');
        
            $model = $this->_objectManager->create('Forix\ProductWizard\Model\Wizard')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Wizard no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            try {

                $value = $this->getRequest()->getParam('base_image');

                if ($this->isTmpFileAvailable($value) && $imageName = $this->getUploadedImageName($value)) {
                    try {
                        $this->_imageUploader->moveFileFromTmp($imageName);
                        $data['base_image'] = $imageName;
                    } catch (\Exception $e) {
                        $this->_logger->critical($e);
                    }
                } else {
                    $data['base_image'] = '';
                    if(isset($value['0']) && isset($value['0']['name'])) {
                        $data['base_image'] =$value['0']['name'];
                    }

                }

                $bannerImage = $this->getRequest()->getParam('banner_image');

                if ($this->isTmpFileAvailable($bannerImage) && $bannerImageName = $this->getUploadedImageName($bannerImage)) {
                    try {
                        $this->_imageUploader->moveFileFromTmp($bannerImageName);
                        $data['banner_image'] = $bannerImageName;
                    } catch (\Exception $e) {
                        $this->_logger->critical($e);
                    }
                } else {
                    $data['banner_image'] = '';
                    if(isset($value['0']) && isset($bannerImage['0']['name'])) {
                        $data['banner_image'] =$bannerImage['0']['name'];
                    }

                }

                $model->setData($data);

                $model->save();

                $this->messageManager->addSuccessMessage(__('You saved the Wizard.'));
                $this->dataPersistor->clear('forix_productwizard_wizard');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['wizard_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Wizard.'));
            }
        
            $this->dataPersistor->set('forix_productwizard_wizard', $data);
            return $resultRedirect->setPath('*/*/edit', ['wizard_id' => $this->getRequest()->getParam('wizard_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
