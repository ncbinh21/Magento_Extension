<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: Melfredborzall
 * Date: 11/06/2018
 * Time: 12:01
 */

namespace Forix\Product\Model;

class ProductType extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('\Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable');
    }
    
}