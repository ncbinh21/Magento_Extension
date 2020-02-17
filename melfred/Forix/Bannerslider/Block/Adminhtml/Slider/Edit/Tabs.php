<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Block\Adminhtml\Slider\Edit;

/**
 * Admin Locator left menu.
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('slider_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Slider Information'));
    }
}
