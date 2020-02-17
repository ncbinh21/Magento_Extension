<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 09/08/2018
 * Time: 21:38
 */

namespace Forix\ImportHelper\Model\Export\Product;
class Adapters extends \Forix\ImportHelper\Model\Export\AbstractProduct
{

    /**
     * @param \Forix\ImportHelper\Model\ResourceModel\RawData\Collection $collection
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    protected function addFieldToFilter($collection)
    {
        $collection = parent::addFieldToFilter($collection);
        $collection->addFieldToFilter('file_name', ['like' => '%Adapters.csv%']);
        return $collection;
    }

    protected function getColumnToSelect()
    {
        return [
            "sku" => "sku",
            "sku_parent" => "sku_parent",
            "name" => "name",
            "product_type" => "product_type",
            "categories" => "categories",
            "visibility" => "visibility",
            "attribute_set_code" => "attribute_set",
            "description" => "description",
            "image" => "image",
            "image_label" => "image_label",
            "additional_images" => "gallery",
            "additional_image_labels" => "gallery_label",
            'thumbnail_label' => 'image_label',
            'small_label' => 'image_label',
            'small_image' => 'image',
            'thumbnail' => 'image',
            'swatch_image' => 'image',
            "meta_title" => "meta_title",
            "meta_description" => "meta_description",
            "meta_keyword" => "meta_keyword",
            "mb_product_line" => "product_line",
            "mb_badge" => "badge",
            "mb_small_description" => "small_description",
            "mb_weight_lbs" => "weight_lbs",
            "mb_qty" => "qty",
            "mb_stock_status" => "stock_status",
            "mb_backorders" => "backorders",
            "mb_tax_class" => "tax_class",
            "price" => "price",
            "mb_special_price" => "special price",
            "mb_features" => "features",
            "mb_redesigned_date" => "redesigned_date",
            "mb_redesigned_description" => "redesigned_description",
            "mb_oem" => "oem",
            "mb_rig_model" => "rig_models",
            "mb_adapter_gender" => "adapter_gender",
            "mb_adapter_option" => "adapter_option",
            "mb_adapter_type" => "adapter_type",
            "mb_quick_disconnect_type" => "quick_disconnect_type",
            "mb_swivel_capacity" => "swivel_capacity",
            "mb_swivel_type" => "swivel_type",
            "mb_thread_option" => "thread_option",
            "mb_thread_connection_type" => "thread_connection_type",
            "mb_fit_transmitter_diameter" => "fit_transmitter_housing_diameter",
            'mb_redesigned' => 'redesigned',


            "_related_sku" => "related_sku",
            "_crosssell_sku" => "crosssell_sku",
            "_upsell_sku" => "upsell_sku",
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
}