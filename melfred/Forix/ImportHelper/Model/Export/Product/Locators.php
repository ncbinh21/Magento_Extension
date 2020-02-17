<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 09/08/2018
 * Time: 21:38
 */

namespace Forix\ImportHelper\Model\Export\Product;
class Locators extends \Forix\ImportHelper\Model\Export\AbstractProduct
{

    /**
     * @param \Forix\ImportHelper\Model\ResourceModel\RawData\Collection $collection
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    protected function addFieldToFilter($collection)
    {
        $collection = parent::addFieldToFilter($collection);
        $collection->addFieldToFilter('file_name', ['like' => '%Locators.csv%']);
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
            "badge" => "badge",
            "description" => "description",
            "small_description" => "small_description",
            "weight_lbs" => "weight_lbs",
            "qty" => "qty",
            "stock_status" => "stock_status",
            "backorders" => "backorders",
            "tax_class" => "tax_class",
            "price" => "price",
            "special_price" => "special_price",
            "mb_redesigned_date" => "redesigned_date",
            "mb_product_line" => "product_line",
            "mb_features" => "features",
            "mb_redesigned_description" => "redesigned_description",
            "mb_oem" => "oem",
            "mb_digitrak_locator_model" => "digitrak_locator_model",
            "mb_digitrak_component" => "digitrak_component",
            "mb_transmitter_type" => "transmitter_type",
            "mb_digitrak_locator" => "digitrak_locator",
            "mb_digitrak_system_feature" => "digitrak_system_feature",
            "mb_digitrak_transmit_feature" => "digitral_transmitter_feature",
            "mb_digitrak_remote_display" => "digitrak_remote_display",
            "mb_digitrak_battery_rating" => "digitrak_battery_rating",
            'mb_redesigned' => 'redesigned',
            "relation_attributes" => "relation_attributes",
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