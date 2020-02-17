<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/07/2018
 * Time: 15:15
 */
namespace Forix\CustomerProduct\Model\Customer;
class Product extends \Magento\Catalog\Model\Product
{
    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Forix\CustomerProduct\Model\ResourceModel\Customer\Product::class);
    }

}