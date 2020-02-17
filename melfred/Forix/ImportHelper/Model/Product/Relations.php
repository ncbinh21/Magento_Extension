<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 7/26/18
 * Time: 12:31 PM
 */
namespace Forix\ImportHelper\Model\Product;

class Relations extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('\Forix\ImportHelper\Model\ResourceModel\Product\Relations');
    }
}