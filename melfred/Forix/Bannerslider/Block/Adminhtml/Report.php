<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */
namespace Forix\Bannerslider\Block\Adminhtml;

/**
 * Report grid container.
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Report extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor.
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report';
        $this->_blockGroup = 'Forix_Bannerslider';
        $this->_headerText = __('Reports');
        parent::_construct();
        $this->removeButton('add');
    }
}
