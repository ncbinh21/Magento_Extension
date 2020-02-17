<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 09/08/2018
 * Time: 21:38
 */

namespace Forix\ImportHelper\Model\Export\Product;
class BitBlade extends \Forix\ImportHelper\Model\Export\AbstractProduct
{

    /**
     * @param \Forix\ImportHelper\Model\ResourceModel\RawData\Collection $collection
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    protected function addFieldToFilter($collection)
    {
        $collection = parent::addFieldToFilter($collection);
        $collection->addFieldToFilter('file_name', ['like' => '%Bits & Blades.csv']);
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
            "product_line" => "product_line",
            "visibility" => "visibility",
            "attribute_set_code" => "attribute_set",
            "badge" => "badge",
            "description" => "description",
            "features" => "features",
            "small_description" => "small_description",
            "weight_lbs" => "weight_lbs",
            "qty" => "qty",
            "stock_status" => "stock_status",
            "backorders" => "backorders",
            "tax_class" => "tax_class",
            "price" => "price",
            "special_price" => "special_price",
            "mb_redesigned_date" => "redesigned_date",
            "mb_redesigned_description" => "redesigned_description",
            "mb_oem" => "oem",
            "mb_rig_model" => "rig_models",
            "mb_blade_bolt_pattern" => "blade_bolt_pattern",
            "mb_blade_feature" => "blade_feature",
            "mb_blade_cutter_option" => "blade_cutter_option",
            "mb_pilot_cutting_diameter" => "pilot_hole_cutting_diameter",
            "mb_soil_type_best" => "soil_type_best",
            "mb_soil_type_better" => "soil_type_better",
            "mb_soil_type_good" => "soil_type_good",
            "mb_transmitter_front_connect" => "transmitter_housing_front_connection",
            "mb_transmitter_front_thread" => "transmitter_housing_front_thread",
            "mb_eagle_claw_carbide_teeth" => "eagle_claw_carbide_teeth",
            "mb_bit_type" => "bit_type",
            "mb_bit_thread" => "bit_thread_eagle_claw_iron_fist",
            'mb_redesigned' => 'redesigned',
            "image" => "image",
            "image_label" => "image_label",
            "gallery" => "gallery",
            "gallery_label" => "gallery_label",
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
            "relation_attributes" => "relation_attributes",
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