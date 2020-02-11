<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Plugin;

use Amasty\Shopby\Helper\FilterSetting as FilterHelper;
use Magento\Catalog\Block\Product\View\Attributes;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class ProductViewAttributesPlugin
{
    /**
     * @var FilterHelper
     */
    private $filterHelper;

    /**
     * @var Attribute
     */
    private $eav;

    /**
     * @var PriceCurrencyInterface
     */
    private $currency;

    public function __construct(FilterHelper $filterSetting, Attribute $attribute, PriceCurrencyInterface $currency)
    {
        $this->filterHelper = $filterSetting;
        $this->eav = $attribute;
        $this->currency = $currency;
    }

    public function afterGetAdditionalData(Attributes $subject, $data)
    {
        $priceAttributeCodes = $this->eav->getAttributeCodesByFrontendType('price');
        foreach ($data as &$row) {
            if (in_array($row['code'], $priceAttributeCodes)) {
                $setting = $this->filterHelper->getSettingByAttributeCode($row['code']);
                if (!$setting->getUnitsLabelUseCurrencySymbol()) {
                    $row['value'] = preg_replace('@<[^>]+>@u', '', $row['value']);
                    $pattern = '@\s*' . preg_quote($this->currency->getCurrencySymbol(), '@') . '\s*@u';
                    $row['value'] = preg_replace($pattern, '', $row['value']);
                    $row['value'] .= ' ' . $setting->getUnitsLabel();
                }
            }
        }
        return $data;
    }
}
