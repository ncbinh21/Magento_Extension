<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model\ResourceModel\Slider;

/**
 * Slider Collection
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * _contruct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Forix\Bannerslider\Model\Slider', 'Forix\Bannerslider\Model\ResourceModel\Slider');
    }
}
