<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model\ResourceModel;

/**
 * Slider Resource Model
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Slider extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_bannerslider_slider', 'slider_id');
    }
}
