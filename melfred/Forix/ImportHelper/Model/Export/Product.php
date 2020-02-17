<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 31/07/2018
 * Time: 13:42
 */

namespace Forix\ImportHelper\Model\Export;


class Product extends \Forix\ImportHelper\Model\Export\AbstractProduct
{
    /**
     * @return array
     */
    protected function getColumnToSelect()
    {
        $column = parent::getColumnToSelect();
        $myColumn = [
            'redesigned' => 'redesigned',
            'configurable_variations' => 'configurable_variations',
            'configurable_variation_labels' => 'configurable_variation_labels',
            'custom_options' => 'custom_options',
            'bundle_values' => 'bundle_values',
            'associated_skus' => 'associated_skus',
        ];
        return array_unique(array_merge($column, $myColumn));
    }
}