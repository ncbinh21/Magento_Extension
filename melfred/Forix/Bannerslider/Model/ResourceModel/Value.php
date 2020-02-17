<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model\ResourceModel;

/**
 * Value Resource Model
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Value extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_bannerslider_value', 'value_id');
    }
}
