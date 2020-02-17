<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Product\Plugin\Model\ResourceModel\Attribute;

use Magento\CatalogInventory\Model\ResourceModel\Stock\Status;
use Magento\ConfigurableProduct\Model\ResourceModel\Attribute\OptionSelectBuilderInterface;
use Magento\Framework\DB\Select;
use \Zend_Db_Expr;
/**
 * Plugin for OptionSelectBuilderInterface to add stock status filter.
 */
class RecommendOptionSelectBuilder
{
    /**
     * Add stock status filter to select.
     *
     * @param OptionSelectBuilderInterface $subject
     * @param Select $select
     * @return Select
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetSelect(OptionSelectBuilderInterface $subject, Select $select)
    {
        $select->columns( new Zend_Db_Expr('product_link.recommend_sku'));
        return $select;
    }
}
