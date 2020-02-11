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

use Forix\Bannerslider\Model\Slider;

/**
 * Save Slider action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Save extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public function execute()
    {
        $resultRedirect = $this->_resultRedirectFactory->create();
        $formPostValues = $this->getRequest()->getPostValue();

        if (isset($formPostValues['slider'])) {
            $sliderData = $formPostValues['slider'];
            $sliderId = isset($sliderData['slider_id']) ? $sliderData['slider_id'] : null;
            if (isset($sliderData['style_slide'])) {
                if ($sliderData['style_slide'] == Slider::STYLESLIDE_EVOLUTION_ONE || $sliderData['style_slide'] == Slider::STYLESLIDE_EVOLUTION_THREE ||
                    $sliderData['style_slide'] == Slider::STYLESLIDE_EVOLUTION_TWO || $sliderData['style_slide'] == Slider::STYLESLIDE_EVOLUTION_FOUR
                ) {
                    $sliderData['animationB'] = $sliderData['animationA'];
                } elseif ($sliderData['style_slide'] == Slider::STYLESLIDE_POPUP) {
                    $sliderData['position'] = 'pop-up';
                } elseif ($sliderData['style_slide'] == Slider::STYLESLIDE_SPECIAL_NOTE) {
                    $sliderData['position'] = 'note-allsite';
                }
            }

            if ($sliderData['style_content'] == Slider::STYLE_CONTENT_NO) {
                $sliderData['position'] = $sliderData['position_custom'];
            }

            if (isset($sliderData['category_ids'])) {
                $sliderData['category_ids'] = implode(',', $sliderData['category_ids']);
            }
            if (isset($sliderData['cms_ids'])) {
                $sliderData['cms_ids'] = implode(',', $sliderData['cms_ids']);
            }
            if (isset($sliderData['news_category_ids'])) {
                $sliderData['news_category_ids'] = implode(',', $sliderData['news_category_ids']);
            }

            $model = $this->_sliderFactory->create();

            $model->load($sliderId);

            $model->setData($sliderData);

            try {
                $model->save();

                if (isset($formPostValues['slider_banner'])) {
                    $bannerGridSerializedInputData = $this->_jsHelper->decodeGridSerializedInput($formPostValues['slider_banner']);
                    $bannerIds = [];
                    foreach ($bannerGridSerializedInputData as $key => $value) {
                        $bannerIds[] = $key;
                        $bannerOrders[] = $value['order_banner_slider'];
                    }

                    $unSelecteds = $this->_bannerCollectionFactory
                        ->create()
                        ->setStoreViewId(null)
                        ->addFieldToFilter('slider_id', $model->getId());
                    if (count($bannerIds)) {
                        $unSelecteds->addFieldToFilter('banner_id', array('nin' => $bannerIds));
                    }

                    foreach ($unSelecteds as $banner) {
                        $banner->setSliderId(0)
                            ->setStoreViewId(null)
                            ->setOrderBanner(0)->save();
                    }

                    $selectBanner = $this->_bannerCollectionFactory
                        ->create()
                        ->setStoreViewId(null)
                        ->addFieldToFilter('banner_id', array('in' => $bannerIds));
                    $i = -1;
                    foreach ($selectBanner as $banner) {
                        $banner->setSliderId($model->getId())
                            ->setStoreViewId(null)
                            ->setOrderBanner($bannerOrders[++$i])->save();
                    }
                }

                $this->messageManager->addSuccess(__('The slider has been saved.'));
                $this->_getSession()->setFormData(false);

                if ($this->getRequest()->getParam('back') === 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit', ['slider_id' => $model->getId(), '_current' => TRUE]
                    );
                } elseif ($this->getRequest()->getParam('back') === 'new') {
                    return $resultRedirect->setPath('*/*/new', ['_current' => TRUE]);
                }

                return $resultRedirect->setPath('*/*/');

            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->messageManager->addException($e, __('Something went wrong while saving the slider.'));
            }

            $this->_getSession()->setFormData($formPostValues);

            return $resultRedirect->setPath('*/*/edit', ['slider_id' => $sliderId]);
        }

        return $resultRedirect->setPath('*/*/');
    }
}
