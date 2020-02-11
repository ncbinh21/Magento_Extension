<?php

/**
 * Forix
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Forix.com license that is
 * available through the world-wide-web at this URL:
 *
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Forix
 * @package     Forix_Bannerslider
 * @copyright   Copyright (c) 2012 Forix (http://www.forixwebdesign.com/)
 * @license
 */

namespace Forix\Bannerslider\Controller\Adminhtml\Slider;

/**
 * Mass Delete Slider action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class MassDelete extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $sliderIds = $this->getRequest()->getParam('slider');
        if (!is_array($sliderIds) || empty($sliderIds)) {
            $this->messageManager->addError(__('Please select slider(s).'));
        } else {
            $sliderCollection = $this->_sliderCollectionFactory->create()
                ->addFieldToFilter('slider_id', ['in' => $sliderIds]);
            try {
                foreach ($sliderCollection as $slider) {
                    $slider->delete();
                }
                $this->messageManager->addSuccess(
                    __('A total of %1 record(s) have been deleted.', count($sliderIds))
                );
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }
        $resultRedirect = $this->_resultRedirectFactory->create();

        return $resultRedirect->setPath('*/*/');
    }
}
