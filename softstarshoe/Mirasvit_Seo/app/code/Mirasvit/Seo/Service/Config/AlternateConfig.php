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

use \Magento\Store\Model\ScopeInterface as ScopeInterface;

class AlternateConfig implements \Mirasvit\Seo\Api\Config\AlternateConfigInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @return int
     */
    public function getAlternateHreflang($store)
    {
        return $this->scopeConfig->getValue(
            'seo/general/is_alternate_hreflang',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @param bool $hreflang
     * @return array|string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getAlternateManualConfig($store, $hreflang = false)
    {
        $storeId = (is_object($store)) ? $store->getId() : $store;
        $config = $this->getPreparedAlternateManualConfig();

        if (!is_array($config)) {
            return [];
        }
        $result = [];
        $storeGroup = false;
        $storeHreflangResult = false;

        foreach ($config as $value) {
            if ($value['option'] == $storeId) {
                $storeGroup = $value['pattern'];
                $storeHreflangResult = $value['hreflang'];
            }
            $result[$value['pattern']][] = $value['option'];
        }

        if ($hreflang) {
            return $storeHreflangResult;
        }

        return ($storeGroup
            && isset($result[$storeGroup])
            && in_array($storeId, $result[$storeGroup])) ? $result[$storeGroup] : [];
    }

    /**
     * @return array
     */
    protected function getPreparedAlternateManualConfig()
    {
        $config = $this->scopeConfig->getValue(
            'seo/general/alternate_configurable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $configDecode = json_decode($config);

        if (is_object($configDecode)) {
            $config = (array)$configDecode;
            foreach ($config as $key => $value) {
                if (is_object($value)) {
                    $config[$key] = (array)$value;
                }
            }
        }

        if (!is_array($config)) {
            $config = unserialize($config);
        }

        return $config;
    }

    /**
     * @param array $storeUrls
     * @return string $xDefaultUrl
     */
    public function getAlternateManualXDefault($storeUrls)
    {
        $xDefaultUrl = false;
        $config = $this->scopeConfig->getValue('seo/general/configurable_hreflang_x_default');
        if ($config == '[]' || !$config) {
            $config = [];
        } elseif ($decode = json_decode($config)) {
            $config = [];
            if (is_object($decode)) {
                $decode = (array)$decode;
                foreach ($decode as $key => $value) {
                    if (is_object($decode)) {
                        $config[$key] = (array)$value;
                    }
                }
            }
        } else {
            $config = unserialize($config);
        }
        $storeIds = array_keys($storeUrls);
        foreach ($config as $value) {
            if (in_array($value['option'], $storeIds)) {
                $xDefaultUrl = $storeUrls[$value['option']];
                break;
            }
        }

        return $xDefaultUrl;
    }

    /**
     * @return bool
     */
    public function isHreflangLocaleCodeAddAutomatical()
    {
        return $this->scopeConfig->getValue('seo/general/is_hreflang_locale_code_automatical');
    }

    /**
     * @return bool
     */
    public function isHreflangCutCategoryAdditionalData()
    {
        return $this->scopeConfig->getValue('seo/general/is_hreflang_cut_category_additional_data');
    }

    /**
     * @return string|int
     */
    public function getXDefault()
    {
        return $this->scopeConfig->getValue('seo/general/is_hreflang_x_default',
            ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getStore()->getWebsiteId());
    }

    /**
     * @param int|\Magento\Store\Model\Store $store
     * @return string
     */
    public function getHreflangLocaleCode($store)
    {
        return trim($this->scopeConfig->getValue(
            'seo/general/hreflang_locale_code',
            ScopeInterface::SCOPE_STORE,
            $store
        ));
    }
}
