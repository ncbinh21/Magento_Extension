<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Model;

use Magento\Catalog\Model\Product;
use Amasty\ShopbyBase\Helper\FilterSetting as FilterSettingHelper;
use Magento\Framework\DataObjectFactory as ObjectFactory;
use Magento\Store\Model\ScopeInterface;

class XmlSitemap
{
    /**
     * @var ObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var \Amasty\ShopbyBase\Helper\OptionSetting
     */
    private $optionSetting;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        \Amasty\ShopbyBase\Helper\OptionSetting $optionSetting,
        ObjectFactory $dataObjectFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->eavConfig = $eavConfig;
        $this->optionSetting = $optionSetting;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $attrCode
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeByCode($attrCode)
    {
        return $this->eavConfig->getAttribute(Product::ENTITY, $attrCode);
    }

    /**
     * @param $url
     * @return bool|string
     */
    public function removeSid($url)
    {
        $baseUrl = substr($url, 0, strpos($url, '?'));
        if (!$baseUrl) {
            return $url;
        }
        $parsed = [];
        parse_str(substr($url, strpos($url, '?') + 1), $parsed);
        if (isset($parsed['SID'])) {
            $url = $baseUrl;
            unset($parsed['SID']);
            if (!empty($parsed)) {
                $url .= '?' . http_build_query($parsed);
            }
        }

        return $url;
    }

    /**
     * Overrided in Amasty/ShopbySeo/Plugin/XmlSitemap/ShopbyBase/Model/Sitemap.php
     * @param $url
     * @return string
     */
    public function applySeoUrl($url)
    {
        return $url;
    }

    /**
     * @param $storeId
     * @param null $baseUrl
     * @return array
     */
    public function getBrandUrls($storeId, $baseUrl = null)
    {
        $result = [];
        $attrCode   = $this->scopeConfig->getValue(
            'amshopby_brand/general/attribute_code',
            ScopeInterface::SCOPE_STORE
        );

        if (!$attrCode) {
            return $result;
        }

        $brandAttribute = $this->getAttributeByCode($attrCode);
        foreach ($brandAttribute->getSource()->getAllOptions() as $option) {
            if ($option['value']) {
                $url = $this->optionSetting->getSettingByValue(
                    $option['value'],
                    FilterSettingHelper::ATTR_PREFIX . $attrCode,
                    $storeId
                )->getUrlPath();

                $url = $this->applySeoUrl($url);
                $url = $this->removeSid($url);
                //remove baseurl for default sitemap
                if ($baseUrl) {
                    $url = str_replace($baseUrl, '', $url);
                }

                $result[] = $this->dataObjectFactory->create()->setUrl($url);
            }
        }

        return $result;
    }
}
