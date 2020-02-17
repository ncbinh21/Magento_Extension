<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Block\Adminhtml;

/**
 * Slider grid container.
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Slider extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_slider';
        $this->_blockGroup = 'Forix_Bannerslider';
        $this->_headerText = __('Sliders');
        $this->_addButtonLabel = __('Add New Slider');
        parent::_construct();
    }
}
