<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 03/08/2018
 * Time: 11:22
 */

namespace Forix\ImportHelper\Model\Export;


class AbstractProduct extends \Forix\ImportHelper\Model\Export\ExportRawAbstract
{
    protected $_typeColumn = \Magento\CatalogImportExport\Model\Import\Product::COL_TYPE;

    /**
     * @return array
     */
    protected function getColumnToSelect()
    {
        return [
            'sku' => 'sku',
            'name' => 'name',
            'product_type' => 'product_type',
            'categories' => 'categories',
            'product_line' => 'product_line',
            'visibility' => 'visibility',
            'attribute_set' => 'attribute_set',
            'badge' => 'badge',
            'short_description' => 'short_description',
            'description' => 'description',
            'features' => 'features',
            'small_description' => 'small_description',
            'weight_lbs' => 'weight_lbs',
            'qty' => 'qty',
            'stock_status' => 'stock_status',
            'backorders' => 'backorders',
            'taxclass' => 'taxclass',
            'price' => 'price',
            'special_price' => 'special_price',
            'distributor_price' => 'distributor_price',
            'cost' => 'cost',
            'manufacturer' => 'manufacturer',
            'redesigned' => 'redesigned',
            'redesigned_description' => 'redesigned_description',
            'downloads' => 'downloads',
            'related_articles' => 'related_articles',
            'oem' => 'oem',
            'rig_models' => 'rig_models',
            'image' => 'image',
            'image_label' => 'image_label',
            'gallery' => 'gallery',
            'gallery_label' => 'gallery_label',
            'meta_title' => 'meta_title',
            'meta_description' => 'meta_description',
            'meta_keyword' => 'meta_keyword',
            'related_sku' => 'related_sku',
            'crosssell_sku' => 'crosssell_sku',
            'upsell_sku' => 'upsell_sku',
            'redesigned_date' => 'redesigned_date',
            'configurable_variations' => 'configurable_variations',
            'configurable_variation_labels' => 'configurable_variation_labels',
            'custom_options' => 'custom_options',
            'bundle_values' => 'bundle_values',
            'associated_skus' => 'associated_skus',
            'url_key' => 'url_key',
            'store_view_code' => 'store_view_code',
            '_product_websites' => '_product_websites'
        ];
    }

    /**
     * @param bool $resetCollection
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    public function getCollection($resetCollection = false)
    {
        $requestNew = false;
        if (!($this->_collection) || $resetCollection) {
            $requestNew = true;
        }
        $collection = parent::getCollection($resetCollection);
        if ($requestNew) {
            $collection->addFieldToSelect($this->getColumnToSelect());
            $collection->addFieldToFilter('error_list', ['eq' => '', 'null' => true]);
        }
        return $collection;
    }
}