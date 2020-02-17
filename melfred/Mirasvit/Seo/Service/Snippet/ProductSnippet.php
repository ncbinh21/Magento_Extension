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



namespace Mirasvit\Seo\Service\Snippet;

use Mirasvit\Seo\Api\Service\Snippet\ProductSnippetInterface;
use Magento\Framework\Registry;
use Mirasvit\Seo\Api\Config\ProductSnippetConfigInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Seo\Helper\Data as SeoDataHelper;
use Mirasvit\Seo\Helper\Snippets as seoSnippets;
use Mirasvit\Seo\Helper\Snippets\Price;
use Magento\Payment\Model\Config as PaymentConfig;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Config as ShippingConfig;
use Mirasvit\Seo\Traits\SnippetsTrait;

class ProductSnippet implements ProductSnippetInterface
{
    /**
     * @var string
     */
    public $goodrelationsUrl = 'http://purl.org/goodrelations/v1#';

    /**
     * @param Registry $registry
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Registry $registry,
        ProductSnippetConfigInterface $config,
        Image $catalogImage,
        CategoryFactory $categoryFactory,
        StoreManagerInterface $storeManager,
        SeoDataHelper $seoData,
        seoSnippets $seoSnippets,
        Price $seoSnippetsPriceHelper,
        PaymentConfig $paymentConfig,
        ScopeConfigInterface $scopeConfig,
        ShippingConfig $shippingMethodConfig
    ) {
        $this->registry = $registry;
        $this->config = $config;
        $this->catalogImage = $catalogImage;
        $this->categoryFactory = $categoryFactory;
        $this->storeManager = $storeManager;
        $this->seoData = $seoData;
        $this->seoSnippets = $seoSnippets;
        $this->seoSnippetsPriceHelper = $seoSnippetsPriceHelper;
        $this->paymentConfig = $paymentConfig;
        $this->scopeConfig = $scopeConfig;
        $this->shippingMethodConfig = $shippingMethodConfig;
    }

    /**
     * @return string
     */
    public function getProductSnippets()
    {
        $poductSnippets = '';
        if ($product = $this->registry->registry('current_product')) {
            $poductSnippets =
                $this->getBaseProductSnippets($product)
                . $this->getManufacturerPartNumber($product)
                . $this->getImage($product)
                . $this->getCategoryName($product)
                . $this->getBrand($product)
                . $this->getModel($product)
                . $this->getColor($product)
                . $this->getWeight($product)
                . $this->getDimensions($product)
                . $this->getDescription($product)
                . $this->getAggregateRating($product)
                . $this->getOffer($product);
        }

        return $poductSnippets;
    }

    /**
     * @param Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getBaseProductSnippets($product)
    {
        $baseProductSnippets =
            '"name":"' . SnippetsTrait::prepareSnippet($product->getName()) . '",' .
            '"sku":"' . SnippetsTrait::prepareSnippet($product->getSku()) . '"';

        return $baseProductSnippets;
    }

    /**
     * @param Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getManufacturerPartNumber($product)
    {
        $mpn = '';
        if ($this->config->isEnabledRichSnippetsManufacturerPartNumber()) {
            $mpn = ',"mpn":"' . SnippetsTrait::prepareSnippet($product->getSku()) . '"';
        }

        return $mpn;
    }

    /**
     * @param string $product
     * @return $this|string
     */
    public function getImage($product)
    {
        $image = '';
        if ($this->config->isEnabledRichSnippetsItemImage()
            && ($image = $this->catalogImage->init($product, 'product_page_image_large'))) {
                $image = ',"image":"' . SnippetsTrait::prepareSnippet($image->getUrl()) . '"';
        }

        return $image;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getCategoryName($product)
    {
        $categoryName = '';

        if (!$this->config->isEnabledRichSnippetsProductCategory()) {
            return $categoryName;
        }

        if ($category = $this->registry->registry('current_category')) {
            $categoryName = $category->getName();
        } else {
            $categoryIds = $product->getCategoryIds();
            $categoryIds = array_reverse($categoryIds);
            if (isset($categoryIds[0])) {
                $categoryName = $this->categoryFactory->create()
                                ->setStoreId($this->storeManager->getStore()->getStoreId())
                                ->load($categoryIds[0])
                                ->getName();
            }
        }

        if ($categoryName) {
            $categoryName = SnippetsTrait::prepareSnippet($categoryName);
            $categoryName = ',"category":"' . $categoryName . '"';
        }

        return $categoryName;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getBrand($product)
    {
        $brand = '';
        $attributeBrand = false;
        if ($brandPrepared = $this->config->getRichSnippetsBrandAttributes()) {
            $attributeBrand = $this->getRichSnippetsAttributeValue($brandPrepared, $product);
        }
        if ($attributeBrand) {
            $attributeBrand = SnippetsTrait::prepareSnippet($attributeBrand);
            $brand = ',"brand":"' . $attributeBrand . '"';
        }

        return $brand;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getModel($product)
    {
        $model = '';
        $attributeModel = false;

        if ($modelPrepared = $this->config->getRichSnippetsModelAttributes()) {
            $attributeModel = $this->getRichSnippetsAttributeValue($modelPrepared, $product);
        }
        if ($attributeModel) {
            $attributeModel = SnippetsTrait::prepareSnippet($attributeModel);
            $model = ',"model":"' . $attributeModel . '"';
        }

        return $model;
    }

    /**
     * @param string $attributeArray
     * @param string $product
     * @return bool|string
     */
    protected function getRichSnippetsAttributeValue($attributeArray, $product)
    {
        $attributeValue = false;
        foreach ($attributeArray as $attributeName) {
            if ($attribute = $product->getResource()->getAttribute($attributeName)) {
                $attributeValue = trim($attribute->getFrontend()->getValue($product));
            }
            if ($attributeValue && $attributeValue != 'No' && $attributeValue != 'Нет') {
                $attributeValue = SnippetsTrait::prepareSnippet($attributeValue);
                return $attributeValue;
            }
        }

        return false;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getColor($product)
    {
        $color = '';
        $attributeColor = false;

        if ($colorPrepared = $this->config->getRichSnippetsColorAttributes()) {
            $attributeColor = $this->getRichSnippetsAttributeValue($colorPrepared, $product);
        }
        if ($attributeColor) {
            $attributeColor = SnippetsTrait::prepareSnippet($attributeColor);
            $color = ',"color":"' . $attributeColor . '"';
        }

        return $color;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getWeight($product)
    {
        $weight = '';

        if (($weightPrepared = $product->getWeight()) && ($weightCode = $this->config->getRichSnippetsWeightCode())) {
            $weight = '
                ,"weight": {
                    "@type": "QuantitativeValue",
                    "value": "' . SnippetsTrait::prepareSnippet($weightPrepared) . '",
                    "unitCode": "' . SnippetsTrait::prepareSnippet($weightCode) . '"
              }';
        }

        return $weight;
    }

    /**
     * @param string $product
     * @return string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getDimensions($product)
    {
        $dimensions = '';
        $attributeHeight = false;
        $attributeWidth = false;
        $attributeDepth = false;

        if (!$this->config->isEnabledRichSnippetsDimensions()) {
            return $dimensions;
        }

        $dimensionalUnit = $this->config->getRichSnippetsDimensionUnit();
        if ($dimensionalUnit) {
            $dimensionalUnit = $this->seoSnippets->prepareDimensionCode($dimensionalUnit);
        }
        if ($height = $this->config->getRichSnippetsHeightAttributes()) {
            $attributeHeight = $this->getRichSnippetsAttributeValue($height, $product);
        }
        if ($attributeHeight) {
            if ($dimensionalUnit) {
                $unitCodeWeight = '"unitCode": "' . $dimensionalUnit . '"';
            }
            $dimensions .= '
                ,"weight": {
                    "@type": "QuantitativeValue",
                    "value": "' . $attributeHeight . '",
                    ' . $unitCodeWeight . '
            }';
        }

        if ($width = $this->config->getRichSnippetsWidthAttributes()) {
            $attributeWidth = $this->getRichSnippetsAttributeValue($width, $product);
        }
        if ($attributeWidth) {
            if ($dimensionalUnit) {
                $unitCodeWidth = '"unitCode": "' . $dimensionalUnit . '"';
            }
            $dimensions .= '
                ,"width": {
                    "@type": "QuantitativeValue",
                    "value": "' . $attributeWidth . '",
                    ' . $unitCodeWidth . '
            }';
        }

        if ($depth = $this->config->getRichSnippetsDepthAttributes()) {
            $attributeDepth = $this->getRichSnippetsAttributeValue($depth, $product);
        }
        if ($attributeDepth) {
            if ($dimensionalUnit) {
                $unitCodeDepth = '"unitCode": "' . $dimensionalUnit . '"';
            }
            $dimensions .= '
                ,"depth": {
                    "@type": "QuantitativeValue",
                    "value": "' . $attributeDepth . '",
                    ' . $unitCodeDepth . '
            }';
        }

        return $dimensions;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getDescription($product)
    {
        $description = '';

        if ($this->config->getRichSnippetsDescription() == ProductSnippetInterface::DESCRIPTION_SNIPPETS) {
            $description = $product->getDescription();
        }
        if ($this->config->getRichSnippetsDescription() == ProductSnippetInterface::META_DESCRIPTION_SNIPPETS
            && $this->registry->registry(ProductSnippetInterface::DESCRIPTION_REGISTER_TAG)) {
            $description = $this->registry->registry(ProductSnippetInterface::DESCRIPTION_REGISTER_TAG);
        }

        if ($description) {
            $description = str_replace(
                '"',
                '&#34;',
                strip_tags($description)
            );

            $description = ',"description":"' . SnippetsTrait::prepareSnippet($description) . '"';
        }

        return $description;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getAggregateRating($product)
    {
        $ratingData = '';
        if (!is_object($product->getRatingSummary())) {
            return $ratingData;
        }
        if (($ratingValue = $product->getRatingSummary()->getRatingSummary())
            && ($reviewsCount = $product->getRatingSummary()->getReviewsCount())) {
            $ratingValue = number_format($ratingValue * 5 / 100, 2);
            $ratingData .= '
                ,"aggregateRating": {
                    "@type": "AggregateRating",
                    "ratingValue": "' . $ratingValue . '",
                    "ratingCount": "' . $reviewsCount . '",
                    "reviewCount": "' . $reviewsCount . '",
                    "bestRating": "5"
                 }';
        }

        return $ratingData;
    }

    /**
     * @param string $product
     * @return string
     */
    public function getOffer($product)
    {
        $offer = '';
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        if ($productFinalPrice = $this->seoData->getCurrentProductFinalPrice($product, true)) {
            /* Google Structured Data Testing Tool throws Warning on price attribute with comma like price = 3,99 */
            $productFinalPrice = $this->seoSnippetsPriceHelper->formatPriceValue($productFinalPrice);
            $offer = '
            ,"offers": {
                "@type": "Offer",
                "price": "' . $productFinalPrice . '",
                ' . $this->getItemAvailability($product) . '
                ' . $this->getProductCondition($product) . '
                ' . $this->getPaymentMethods() . '
                ' . $this->getDeliveryMethods() . '
                "priceCurrency": "' . $currencyCode . '"
              }
            ';
        }
        return $offer;
    }

    /**
     * @param string $product
     * @return string
     */
    protected function getItemAvailability($product)
    {
        $availability = '';

        if (!$this->config->isEnabledRichSnippetsItemAvailability()) {
            return $availability;
        }

        $productAvailability = (method_exists($product, 'isAvailable')) ?
            $product->isAvailable() : $product->isInStock();

        if ($productAvailability) {
            $availability = '"availability": "http://schema.org/InStock",';
        } else {
            $availability = '"availability": "http://schema.org/OutOfStock",';
        }

        return $availability;
    }

    /**
     * @param string $product
     * @return string
     */
    protected function getProductCondition($product)
    {
        $condition = '';
        $richSnippetsCondition = $this->config->getRichSnippetsCondition();

        if ($richSnippetsCondition == ProductSnippetInterface::CONDITION_RICH_SNIPPETS_CONFIGURE
            && ($conditionAttribute = $this->config->getRichSnippetsConditionAttribute())
            && ($attributeCondition = $this->_getRichSnippetsAttributeValue($conditionAttribute, $product))) {
            switch (strtolower($attributeCondition)) {
                case (strtolower($this->config->getRichSnippetsNewConditionValue())):
                    $condition = '"itemCondition":"http://schema.org/NewCondition",';
                    break;
                case (strtolower($this->config->getRichSnippetsUsedConditionValue())):
                    $condition = '"itemCondition":"http://schema.org/UsedCondition",';
                    break;
                case (strtolower($this->config->getRichSnippetsRefurbishedConditionValue())):
                    $condition = '"itemCondition":"http://schema.org/RefurbishedCondition",';
                    break;
                case (strtolower($this->config->getRichSnippetsDamagedConditionValue())):
                    $condition = '"itemCondition":"http://schema.org/DamagedCondition",';
                    break;
            }
        } elseif ($richSnippetsCondition == ProductSnippetInterface::CONDITION_RICH_SNIPPETS_NEW_ALL) {
            $condition = '"itemCondition":"http://schema.org/NewCondition",';
        }

        return $condition;
    }

    /**
     * @return string
     */
    protected function getPaymentMethods()
    {
        $paymentMethods = '';

        if ($this->config->isEnabledRichSnippetsPaymentMethod()
            && ($activePaymentMethods = $this->getActivePaymentMethods())) {
            $paymentMethods = '"acceptedPaymentMethod": [ "' . implode('", "', $activePaymentMethods) . '" ],';
        }

        return $paymentMethods;
    }

    /**
     * @return array
     */
    private function getActivePaymentMethods()
    {
        $payments = $this->paymentConfig->getActiveMethods();
        $methods = [];
        foreach (array_keys($payments) as $paymentCode) {
            if (strpos($paymentCode, 'paypal') !== false) {
                $methods[] = $this->goodrelationsUrl.'PayPal';
            }
            if (strpos($paymentCode, 'googlecheckout') !== false) {
                $methods[] = $this->goodrelationsUrl.'GoogleCheckout';
            }
            if (strpos($paymentCode, 'cash') !== false) {
                $methods[] = $this->goodrelationsUrl.'Cash';
            }
            if ($paymentCode == 'ccsave') {
                if ($existingMethods = $this->getActivePaymentCctypes()) {
                    $methods = array_merge($methods, $existingMethods);
                }
            }
        }

        return array_unique($methods);
    }

    /**
     * @return array|bool
     */
    private function getActivePaymentCctypes()
    {
        $existingMethods = [];
        $methods = [
            'AE' => 'AmericanExpress',
            'VI' => 'VISA',
            'MC' => 'MasterCard',
            'DI' => 'Discover',
            'JCB' => 'JCB',
        ];

        if ($cctypes = $this->scopeConfig->getValue(
            'payment/ccsave/cctypes',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $this->storeManager->getStore()->getStoreId()
        )
            ) {
            $cctypesArray = explode(',', $cctypes);
            foreach ($cctypesArray as $cctypeValue) {
                if (isset($methods[$cctypeValue])) {
                    $existingMethods[] = $this->goodrelationsUrl.$methods[$cctypeValue];
                }
            }

            return $existingMethods;
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getDeliveryMethods()
    {
        $deliveryMethods = '';

        if ($this->config->isEnabledRichSnippetsDeliveryMethod()
            && ($activeDeliveryMethods = $this->getActiveDeliveryMethods())) {
            $deliveryMethods = '"availableDeliveryMethod": [ "' . implode('", "', $activeDeliveryMethods) . '" ],';
        }

        return $deliveryMethods;
    }

    /**
     * @return array
     */
    private function getActiveDeliveryMethods()
    {
        $existingMethods = [];
        $methods = [
            'flatrate' => 'DeliveryModeFreight',
            'freeshipping' => 'DeliveryModeFreight',
            'tablerate' => 'DeliveryModeFreight',
            'dhl' => 'DHL',
            'fedex' => 'FederalExpress',
            'ups' => 'UPS',
            'usps' => 'DeliveryModeMail',
            'dhlint' => 'DHL',
        ];

        $deliveryMethods = $this->shippingMethodConfig->getActiveCarriers();
        foreach (array_keys($deliveryMethods) as $code) {
            if (isset($methods[$code])) {
                $existingMethods[] = $this->goodrelationsUrl.$methods[$code];
            }
        }

        return array_unique($existingMethods);
    }
}