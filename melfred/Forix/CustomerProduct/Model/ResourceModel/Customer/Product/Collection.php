<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 11/07/2018
 * Time: 15:17
 */

namespace Forix\CustomerProduct\Model\ResourceModel\Customer\Product;

use Magento\Framework\Data\Collection\AbstractDb;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    protected function _initSelect()
    {
        parent::_initSelect();
        $mainAlias = self::MAIN_TABLE_ALIAS;
        $this->getSelect()->joinInner(
            ['order_item' => $this->getTable('sales_order_item')],
            "{$mainAlias}.entity_id = order_item.product_id",
            "product_options"
        )->joinInner(
            ['order' => $this->getTable('sales_order')],
            "order_item.order_id = order.entity_id"
        );
        return $this;
    }


    public function addCustomerToFilter($customerId)
    {
        $this->getSelect()->where(new \Zend_Db_Expr("order.customer_id = {$customerId}"));
        return $this;
    }

    public function addOrderToFilter($orderId)
    {
        $this->getSelect()->where(new \Zend_Db_Expr("order.entity_id = {$orderId}"));
        return $this;
    }

    /**
     * Initialize resources
     *
     * @return void
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init(\Forix\CustomerProduct\Model\Customer\Product::class, \Forix\CustomerProduct\Model\ResourceModel\Customer\Product\Flat::class);
        } else {
            $this->_init(\Forix\CustomerProduct\Model\Customer\Product::class, \Forix\CustomerProduct\Model\ResourceModel\Customer\Product::class);
        }
        $this->_initTables();
    }
}