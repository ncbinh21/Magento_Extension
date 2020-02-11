<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Controller\Adminhtml\Banner;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Save Banner action.
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Save extends \Forix\Bannerslider\Controller\Adminhtml\Banner
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();

        if ($data = $this->getRequest()->getPostValue()) {
            $model = $this->_bannerFactory->create();
            $storeViewId = $this->getRequest()->getParam('store');

            if ($id = $this->getRequest()->getParam('banner_id')) {
                $model->load($id);
            }
            $imageNames = ['image', 'image_thumb', 'tablet', 'tablet_thumb', 'phone', 'phone_thumb'];//\Forix\Bannerslider\Model\Banner::LIST_MEDIA_NAME;
            foreach($imageNames as $name) {
                if (isset($_FILES[$name]) && isset($_FILES[$name]['name']) && strlen($_FILES[$name]['name'])) {
                    /*
                     * Save image upload
                     */
                    try {
                        $uploader = $this->_objectManager->create(
                            'Magento\MediaStorage\Model\File\Uploader',
                            ['fileId' => $name]
                        );
                        $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);

                        /** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
                        $imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();

                        $uploader->addValidateCallback('banner_image', $imageAdapter, 'validateUploadFile');
                        $uploader->setAllowRenameFiles(true);
                        $uploader->setFilesDispersion(true);

                        /** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
                        $mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
                            ->getDirectoryRead(DirectoryList::MEDIA);
                        $result = $uploader->save(
                            $mediaDirectory->getAbsolutePath(\Forix\Bannerslider\Model\Banner::BASE_MEDIA_PATH)
                        );
                        $data[$name] = \Forix\Bannerslider\Model\Banner::BASE_MEDIA_PATH . $result['file'];
                    } catch (\Exception $e) {
                        if ($e->getCode() == 0) {
                            $this->messageManager->addError($e->getMessage());
                        }
                    }

                } else {
                    if (isset($data[$name]) && isset($data[$name]['value'])) {
                        if (isset($data[$name]['delete'])) {
                            $data[$name] = null;
                            $data[$name.'_delete_image'] = true;
                        } elseif (isset($data[$name]['value'])) {
                            $data[$name] = $data[$name]['value'];
                        } else {
                            $data[$name] = null;
                        }
                    }
                }
            }
            /** @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate */
            //$localeDate = $this->_objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');
            //$data['start_time'] = $localeDate->date($data['start_time'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');
            //$data['end_time'] = $localeDate->date($data['end_time'])->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i');
            //print_r($data);
            $model->setData($data)
                ->setStoreViewId($storeViewId);

            try {
                $model->save();

                $this->messageManager->addSuccess(__('The image has been saved.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'banner_id' => $model->getId(),
                            '_current' => true,
                            'store' => $storeViewId,
                            'current_slider_id' => $this->getRequest()->getParam('current_slider_id'),
                            'saveandclose' => $this->getRequest()->getParam('saveandclose'),
                        ]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath(
                        '*/*/new',
                        ['_current' => TRUE]
                    );
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the banner.'));
            }

            $this->_getSession()->setFormData($data);

            return $resultRedirect->setPath(
                '*/*/edit',
                ['banner_id' => $this->getRequest()->getParam('banner_id')]
            );
        }

        return $resultRedirect->setPath('*/*/');
    }
}
