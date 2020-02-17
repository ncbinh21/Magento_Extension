<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Controller\Adminhtml\Slider;

/**
 * Delete Slider action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Delete extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    public function execute()
    {
        $sliderId = $this->getRequest()->getParam('slider_id');
        try {
            $slider = $this->_sliderFactory->create()->setId($sliderId);
            $slider->delete();
            $this->messageManager->addSuccess(
                __('Delete successfully !')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
