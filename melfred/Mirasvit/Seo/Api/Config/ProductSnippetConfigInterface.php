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
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Api\Config;

/**
 * Rich Snippets
 * Product
 */
interface ProductSnippetConfigInterface
{
    /**
     * @return int
     */
    public function isDeleteWrongRichSnippets();

    /**
     * @return int
     */
    public function getRichSnippetsDescription();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsItemImage();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsItemAvailability();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsPaymentMethod();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsDeliveryMethod();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsProductCategory();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsManufacturerPartNumber();

    /**
     * @return array|string
     */
    public function getRichSnippetsBrandAttributes();

    /**
     * @return array|string
     */
    public function getRichSnippetsModelAttributes();

    /**
     * @return array|string
     */
    public function getRichSnippetsColorAttributes();

    /**
     * @return string
     */
    public function getRichSnippetsWeightCode();

    /**
     * @return bool
     */
    public function isEnabledRichSnippetsDimensions();

    /**
     * @return string
     */
    public function getRichSnippetsDimensionUnit();

    /**
     * @return array|string
     */
    public function getRichSnippetsHeightAttributes();
    /**
     * @return array|string
     */
    public function getRichSnippetsWidthAttributes();

    /**
     * @return array|string
     */
    public function getRichSnippetsDepthAttributes();

    /**
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getRichSnippetsCondition();

    /**
     * @return array|string
     */
    public function getRichSnippetsConditionAttribute();

    /**
     * @return string
     */
    public function getRichSnippetsNewConditionValue();
    /**
     * @return string
     */
    public function getRichSnippetsUsedConditionValue();

    /**
     * @return string
     */
    public function getRichSnippetsRefurbishedConditionValue();

    /**
     * @return string
     */
    public function getRichSnippetsDamagedConditionValue();

    /**
     * @return bool
     */
    public function IsIndividualReviewsSnippetsEnabled();

}