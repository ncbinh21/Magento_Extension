<?php
/**
 * User: Forix
 * Date: 6/24/2016
 * Time: 1:24 PM
 */

namespace Forix\Bannerslider\Model\ResourceModel;

/**
 * Report Resource Model
 * @category Forix
 * @package  Forix_Bannerslider
 * @module   Bannerslider
 * @author   Forix Developer
 */
class Report extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * construct
     * @return void
     */
    protected function _construct()
    {
        $this->_init('forix_bannerslider_report', 'report_id');
    }
}
