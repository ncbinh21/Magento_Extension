<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Service\Config;

use Mirasvit\Seo\Api\Config\ProductSnippetConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ProductSnippetConfig implements ProductSnippetConfigInterface
{
    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return int
     */
    public function getRichSnippetsDescription()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_item_description');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsItemImage()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_item_image');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsItemAvailability()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_item_availability');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsPaymentMethod()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_payment_method');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsDeliveryMethod()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_delivery_method');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsProductCategory()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_product_category');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsManufacturerPartNumber()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_manufacturer_part_number');
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsBrandAttributes()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_brand_config'
        ));
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsModelAttributes()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_model_config'
        ));
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsColorAttributes()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_color_config'
        ));
    }

    /**
     * @return string
     */
    public function getRichSnippetsWeightCode()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_weight_config');
    }

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsDimensions()
    {
        return $this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_dimensions_config');
    }

    /**
     * @return string
     */
    public function getRichSnippetsDimensionUnit()
    {
        return trim($this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_dimensional_unit'));
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsHeightAttributes()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_height_config'
        ));
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsWidthAttributes()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_width_config'
        ));
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsDepthAttributes()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_depth_config'
        ));
    }

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getRichSnippetsCondition()
    {
        return $this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_product_condition_config'
        );
    }

    /**
     * @return array|string
     */
    public function getRichSnippetsConditionAttribute()
    {
        return $this->prepereAttributes($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_product_condition_attribute'
        ));
    }

    /**
     * @return string
     */
    public function getRichSnippetsNewConditionValue()
    {
        return trim($this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_product_condition_new'));
    }

    /**
     * @return string
     */
    public function getRichSnippetsUsedConditionValue()
    {
        return trim($this->scopeConfig->getValue('seo_snippets/product_snippets/rich_snippets_product_condition_used'));
    }

    /**
     * @return string
     */
    public function getRichSnippetsRefurbishedConditionValue()
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_product_condition_refurbished'
        ));
    }

    /**
     * @return string
     */
    public function getRichSnippetsDamagedConditionValue()
    {
        return trim($this->scopeConfig->getValue(
            'seo_snippets/product_snippets/rich_snippets_product_condition_damaged'
        ));
    }

    /**
     * @param string $attributes
     * @return array|string
     */
    protected function prepereAttributes($attributes)
    {
        $attributes = strtolower(trim($attributes));
        $attributes = explode(',', trim($attributes));
        $attributes = array_map('trim', $attributes);
        $attributes = array_diff($attributes, [null]);

        return $attributes;
    }
}