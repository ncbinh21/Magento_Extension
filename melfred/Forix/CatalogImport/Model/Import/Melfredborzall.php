<?php

namespace Forix\CatalogImport\Model\Import;
use Magento\CatalogImportExport\Model\Import\Product;

class Melfredborzall extends \Forix\CatalogImport\Model\Import\Product
{
    protected $_imagesArrayKeys = ['image', 'small_image', 'thumbnail', 'swatch_image', 'image_details', '_media_image'];
    CONST CATEGORIES_SEPARATOR = ',';
    protected $_defaultAttributes = array(
        self::COL_ATTR_SET => 'Default',
        self::COL_STORE_VIEW_CODE => 'default',
        self::COL_PRODUCT_WEBSITES => 'base',
        self::COL_TYPE => 'simple',
        'use_config_manage_stock' => 1,
        'mb_ground_condition' => "Dirt"
    );

    public function getEntityTypeId(){
        return 4;
    }

    /**
     * EAV entity type code getter.
     *
     * @abstract
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'melfred_catalog_product';
    }

    CONST CUSTOM_FIELDS_MAP = [
        //region Attribute Map
        // ------------------------------------ Product info
        self::COL_ATTR_SET => 'attribute_set',
        self::COL_CATEGORY => 'categories',
        self::COL_SKU => 'sku',
        self::COL_TYPE => 'type',
        self::COL_NAME => 'name',
        self::URL_KEY => 'url_key',
        self::COL_MEDIA_IMAGE => 'gallery',
        'attribute_set' => 'attribute_set',
        'status' => 'status',
        "product_type" => "product_type",
        'qty' => 'qty',
        'is_in_stock' => 'is_in_stock',
        // ------------------------------------ Product Image Info

        'image_label' => 'image_label',
        'thumbnail_label' => 'image_label',
        'small_label' => 'image_label',
        '_media_image_label' => 'gallery_label',
        'image' => 'image',
        'small_image' => 'image',
        'thumbnail' => 'image',
        'swatch_image' => 'image',

        // ------------------------------------ Price
        'price' => 'price',
        'tax_class_id' => 'taxclass',
        'special_price' => 'special_price',

        // ------------------------------------ Product Related
        '_related_sku' => "related_sku",
        '_crossell_sku' => "crosssell_sku",
        '_upsell_sku' => "upsell_sku",

        // ------------------------------------ SEO info.
        'description' => 'description',
        'short_description' => 'short_description',
        "mb_small_description" => "small_description",
        'meta_title' => 'meta_title',
        'meta_description' => 'meta_description',
        'meta_keyword' => 'meta_keyword',
        'visibility' => 'visibility',

        // ------------------------------------ Default Attributes
        'weight' => 'weight_lbs',
        'color' => 'color',
        'size' => 'size',
        "badge" => "badge",
        "stock_status" => "stock_status",

        "mb_product_line" => "product_line",
        "mb_features" => "features",
        "backorders" => "backorders",


        "distributor_price" => "distributor_price",
        "cost" => "cost",
        "mb_redesigned_date" => "redesigned_date",
        "redesigned" => "redesigned",
        "redesigned_description" => "redesigned_description",

        "mb_related_articles" => "related_articles",
        "mb_content_download" => "downloads",
        "mb_oem" => "oem",
        "manufacturer" => "manufacturer",
        "mb_rig_model" => "rig_models",
        "mb_adapter_gender" => "adapter_gender",
        "mb_adapter_option" => "adapter_option",
        "mb_adapter_type" => "adapter_type",
        "mb_auntie_lube_size" => "auntie_c_s_thread_lube_size",
        "mb_auntie_lube_type" => "auntie_c_s_thread_lube_type",

        "mb_bit_thread" => "bit_thread_eagle_claw_iron_fist",

        "mb_blade_bolt_pattern" => "blade_bolt_pattern",
        "mb_blade_cutter_option" => "blade_cutter_option",

        //"mb_blade_cutting_size" => "pilot_hole_cutting_diameter", //Removed
        "mb_pilot_cutting_diameter" => "pilot_hole_cutting_diameter",

        "mb_break_connectorpins_type" => "breakaway_connector_pins_type",
        "mb_break_connector_type" => "breakaway_connector_type",

        //"mb_digitrak_locator" => "digitrak_locator_model", Removed

        "mb_digitrak_falcon_locator" => "digitrak_falcon_components", // => Attribute Label => Digitrak Component

        //"mb_digitrak_locator" => "digitrak_falcon_model", //Attribute Label ==> Digitrak Locator Model Removed

        "mb_digitrak_aurora" => "digitrak_aurora_remote_displays", //Removed

        "mb_duct_size" => "duct_size", //Removed

        //"mb_eagle_claw_teeth" => "eagle_claw_carbide_teeth", Removed

        "mb_fastream_cutter_size" => "fastream_cutter_block_cut_size",

        "mb_fastream_cutter_block"           => "fastream_cutter_block_housing_fitup", //Not exists

        "mb_glove_size" => "glove_size",

        "mb_ironfist_cutter" => "iron_fist_cutter_block_type",
        "mb_pitbull_blades_styles" => "pitbull_blade_style",

        "mb_pitbull_housing_size" => "pitbull_housing_diameter",
        "mb_pitbull_housing_feature" => "pitbull_housing_feature",
        "mb_pitbull_housing_rear_thread" => "pitbull_housing_rear_thread",
        "mb_pullback_type" => "pullback_type",
        //"mb_pullback_grip" => "pulling_grip_type", Removed
        "mb_pulling_sling_cable" => "pulling_sling_cable_diameter",
        "mb_pulling_sling_legs" => "pulling_sling_legs",
        "mb_quick_disconnect" => "quick_disconnect_feature",
        "mb_quick_disconnect_type" => "quick_disconnect_type",
        "mb_reamer_cutter_option" => "reamer_cutter_option",
        "mb_reamer_cutting_size" => "reamer_cutting_size",
        "mb_reamer_rear_flange" => "reamer_rear_flange_size_flange_swivel_to_fit",
        "mb_reamer_rear_connection" => "reamer_rear_connection_option",
        "mb_reamer_front_thread" => "reamer_front_thread",
        //"mb_reamer_back_thread" => "reamer_rear_thread", Removed
        "mb_reamer_shaft_size" => "reamer_shaft_size",
        "mb_shoe_size" => "shoe_size",
        "mb_slide_collar" => "slide_collar_feature",
        "mb_soil_type_best" => "soil_type_best",
        "mb_soil_type_better" => "soil_type_better",
        "mb_soil_type_good" => "soil_type_good",
        "mb_stabillizer_barrel" => "stabillizer_barrel_feature",
        //"mb_swivel_capacities" => "swivel_capacity", Removed
        "mb_swivel_connection" => "swivel_connection",
        "mb_swivel_thread" => "swivel_thread",
        "mb_swivel_types" => "swivel_type",
        "mb_thread_connection_type" => "thread_connection_type",

        "mb_transmitter_diameter" => "transmitter_housing_diameter",

        "mb_transmitter_feature" => "transmitter_housing_feature",

        "mb_transmitter_front_connect" => "transmitter_housing_front_connection",

        "mb_transmitter_front_thread" => "transmitter_housing_front_thread",

        "mb_transmitter_rear_thread" => "transmitter_housing_rear_thread",

        "mb_transmitter_housing_type" => "transmitter_housing_type",

        "mb_transmitter_type" => "transmitter_type",
        //endregion

        // ------------- New Product Attribute
        "mb_thread_option" => "thread_option",
        "mb_blade_feature" => "blade_feature",
        "mb_break_away_connector" => "break_away_connector",
        "mb_break_range_required" => "break_range_required",
        "mb_break_away_type" => "break_away_type",
        "mb_digitrak_locator_model" => "digitrak_locator_model",
        "mb_digitrak_locator" => "digitrak_locator",
        "mb_digitrak_system_feature" => "digitrak_system_feature",
        "mb_digitrak_remote_display" => "digitrak_remote_display",
        "mb_eagle_claw_carbide_teeth" => "eagle_claw_carbide_teeth",
        "mb_bit_type" => "bit_type",
        "mb_pulling_grip_type" => "pulling_grip_type",
        "mb_reamer_rear_thread" => "reamer_rear_thread",
        "mb_reamer_packing_size" => "reamer_packing_size",
        "mb_soil_type" => "soil_type",
        "mb_swivel_capacity" => "swivel_capacity",
        "mb_safety_accessory" => "safety_accessory",
        "mb_wiper_option" => "wiper_option",
        "mb_breakaway_type" => "breakaway_type",
        "mb_digitrak_battery_rating" => "digitrak_battery_rating",
        "mb_breakout_jaw" => "breakout_jaw_type",
        "mb_fit_reamer_rear_connection" => "fit_reamer_rear_connection_option",
        "mb_fit_stabillizer_barrel" => "fit_stabillizer_barrel_feature",
        "mb_fit_hole_cutting_diameter" => "fit_pilot_hole_cutting_diameter",
        "mb_fit_transmitter_diameter" => "fit_transmitter_housing_diameter",
        "mb_digitrak_component" => "digitrak_component",
        "mb_digitrak_transmit_feature" => "digitral_transmitter_feature",
    ];

    protected function uploadMediaFiles($fileName, $renameFileOff = false)
    {
        if (!empty($fileName)) {
            if (file_exists(BP.'/pub/media/import/image_upload/pngs/' . $fileName)) {
                $fileName = 'image_upload/pngs/' . $fileName;
            } else {
                $fileName = 'https://melfred.mage.forixstage.com/imageimport/import/pngs/' . $fileName;
            }
            return parent::uploadMediaFiles($fileName, $renameFileOff);
        }
        return '';
    }


    /**
     * Initialize attribute sets code-to-id pairs.
     *
     * @return $this
     */
    protected function _initAttributeSets()
    {
        foreach ($this->_setColFactory->create()->setEntityTypeFilter(4) as $attributeSet) {
            $this->_attrSetNameToId[$attributeSet->getAttributeSetName()] = $attributeSet->getId();
            $this->_attrSetIdToName[$attributeSet->getId()] = $attributeSet->getAttributeSetName();
        }
        return $this;
    }
}