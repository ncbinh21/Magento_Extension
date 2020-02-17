<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Controller\Adminhtml\Slider;

/**
 * Banners of slider action
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Banners extends \Forix\Bannerslider\Controller\Adminhtml\Slider
{
    public function execute()
    {
        $resultLayout = $this->_resultLayoutFactory->create();
        $resultLayout->getLayout()->getBlock('bannerslider.slider.edit.tab.banners')
                     ->setInBanner($this->getRequest()->getPost('banner', null));

        return $resultLayout;
    }
}
