<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 09/08/2018
 * Time: 21:38
 */

namespace Forix\ImportHelper\Model\Export\Product;
class Reamers extends \Forix\ImportHelper\Model\Export\AbstractProduct
{

    /**
     * @param \Forix\ImportHelper\Model\ResourceModel\RawData\Collection $collection
     * @return \Forix\ImportHelper\Model\ResourceModel\RawData\Collection
     */
    protected function addFieldToFilter($collection)
    {
        $collection = parent::addFieldToFilter($collection);
        $collection->addFieldToFilter('file_name', ['like' => '%Reamers.csv%']);
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
            "mb_product_line" => "product_line",
            "mb_features" => "features",
            "mb_redesigned_date" => "redesigned_date",
            "mb_redesigned_description" => "redesigned_description",
            "mb_oem" => "oem",
            "mb_rig_model" => "rig_models",
            "mb_reamer_cutter_option" => "reamer_cutter_option",
            "mb_reamer_cutting_size" => "reamer_cutting_size",
            "mb_reamer_packing_size" => "reamer_packing_size",
            "mb_reamer_rear_flange" => "reamer_rear_flange_size_flange_swivel_to_fit",
            "mb_reamer_rear_connection_option" => "reamer_rear_connection_option",
            "mb_reamer_front_thread" => "reamer_front_thread",
            "mb_reamer_rear_thread" => "reamer_rear_thread",
            "mb_reamer_shaft_size" => "reamer_shaft_size",
            "mb_soil_type_best" => "soil_type_best",
            "mb_soil_type_better" => "soil_type_better",
            "mb_soil_type_good" => "soil_type_good",
            "mb_stabillizer_barrel_feature" => "stabillizer_barrel_feature",
            "mb_fit_stabillizer_barrel" => "fit_stabillizer_barrel_feature",
            "mb_swivel_capacity" => "swivel_capacity",
            "mb_bit_type" => "bit_type",
            "mb_pilot_cutting_diameter" => "pilot_hole_cutting_diameter",
            'mb_redesigned' => 'redesigned',
            "mb_fit_reamer_rear_connection" => "fit_reamer_rear_connection_option",
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