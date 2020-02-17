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



namespace Mirasvit\Seo\Service\Alternate;

use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;

class Url implements \Mirasvit\Seo\Api\Service\Alternate\UrlInterface
{
    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\Seo\Api\Config\AlternateConfigInterface
     */
    protected $alternateConfig;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    protected $storeManager;

    /**
     * @var array
     */
    protected $stores = [];

    /**
     * @var array
     */
    protected $storesBaseUrlsCountValues = [];

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig
     * @param \Mirasvit\Seo\Helper\Data $seoData
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Mirasvit\Seo\Api\Config\AlternateConfigInterface $alternateConfig,
        \Mirasvit\Seo\Helper\Data $seoData
    ) {
        $this->context = $context;
        $this->alternateConfig = $alternateConfig;
        $this->seoData = $seoData;
        $this->storeManager = $this->context->getStoreManager();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getStoresCurrentUrl()
    {
        $alternateManualConfig = false;
        $currentStoreGroup = $this->storeManager->getStore()->getGroupId();
        $currentStore = $this->storeManager->getStore();
        $alternateAddMethod = $this->alternateConfig->getAlternateHreflang($currentStore);
        if ($alternateAddMethod == AlternateConfig::ALTERNATE_CONFIGURABLE) {
            $alternateManualConfig = $this->alternateConfig->getAlternateManualConfig($currentStore);
        }
        $storesNumberInGroup = 0;
        $storeUrls = [];
        $storesBaseUrls = [];

        foreach ($this->storeManager->getStores() as $store) {
            if ($store->getIsActive()
                && ((!$alternateManualConfig
                    && $store->getGroupId() == $currentStoreGroup
                    && $this->alternateConfig->getAlternateHreflang($store))
                || ($alternateManualConfig
                    && in_array($store->getId(), $alternateManualConfig)))
            ) {
                //we works only with stores which have the same store group
                $this->stores[$store->getId()] = $store;
                $currentUrl = $store->getCurrentUrl(false);
                $storesBaseUrls[$store->getId()] = $store->getBaseUrl();
                $storeUrls[$store->getId()] = new \Magento\Framework\DataObject(
                    [
                        'store_base_url' => $store->getBaseUrl(),
                        'current_url' => $currentUrl,
                        'store_code' => $store->getCode()
                    ]
                );

                ++$storesNumberInGroup;
            }
        }

        $isSimilarLinks = (count($storesBaseUrls) - count(array_unique($storesBaseUrls)) > 0) ? true : false;

        if (count($storeUrls) > 1) {
            foreach ($storeUrls as $storeId => $storeData) {
                $storeUrls[$storeId] = $this->_storeUrlPrepare(
                    $storesBaseUrls,
                    $storeData->getStoreBaseUrl(),
                    $storeData->getCurrentUrl(),
                    $storeData->getStoreCode(),
                    $isSimilarLinks,
                    $alternateAddMethod
                );
            }
        }

        $this->storesBaseUrlsCountValues = array_count_values($storesBaseUrls);
        //array with quantity of identical Base Urls

        if ($storesNumberInGroup > 1 && count($storeUrls) > 1) { //if a current store is multilanguage
            return $storeUrls;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getStores()
    {
        return $this->stores;
    }

    /**
     * {@inheritdoc}
     */
    public function getUrlAddition($store)
    {
        $urlAddition = (isset($this->storesBaseUrlsCountValues[$store->getBaseUrl()])
            && $this->storesBaseUrlsCountValues[$store->getBaseUrl()] > 1) ?
            strstr(htmlspecialchars_decode($store->getCurrentUrl(false)), '?') : '';

        return $urlAddition;
    }

    /**
     * Prepare store current url.
     *
     * @param string $storesBaseUrls
     * @param string $storeBaseUrl
     * @param string $currentUrl
     * @param string $storeCode
     * @param bool $isSimilarLinks
     * @param int $alternateAddMethod
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _storeUrlPrepare($storesBaseUrls,
        $storeBaseUrl,
        $currentUrl,
        $storeCode,
        $isSimilarLinks,
        $alternateAddMethod
    ) {
        if (strpos($currentUrl, $storeBaseUrl) == false && !$alternateAddMethod) {
            $currentUrl = str_replace($storesBaseUrls, $storeBaseUrl, $currentUrl); // fix bug with incorrect base urls
        }

        $currentUrl = str_replace('&amp;', '&', $currentUrl);
        $currentUrl = preg_replace('/SID=(.*?)(&|$)/', '', $currentUrl);

        //cut get params for AMASTY_XLANDING if "Cut category additional data for alternate url" enabled
        if ($this->alternateConfig->isHreflangCutCategoryAdditionalData()
            && $this->seoData->getFullActionCode() == AlternateConfig::AMASTY_XLANDING) {
            $currentUrl = strtok($currentUrl, '?');
        }

        $deleteStoreQuery = (substr_count($storeBaseUrl, '/') > 3) ? true : false;

        if (strpos($currentUrl, '___store=' . $storeCode) === false
            || (!$deleteStoreQuery && $isSimilarLinks)) {
            return $currentUrl;
        }

        if (strpos($currentUrl, '?___store=' . $storeCode) !== false
            && strpos($currentUrl, '&') === false) {
            $currentUrl = str_replace('?___store=' . $storeCode, '', $currentUrl);
        } elseif (strpos($currentUrl, '?___store=' . $storeCode) !== false
            && strpos($currentUrl, '&') !== false) {
            $currentUrl = str_replace('?___store=' . $storeCode . '&', '?', $currentUrl);
        } elseif (strpos($currentUrl, '&___store=' . $storeCode) !== false) {
            $currentUrl = str_replace('&___store=' . $storeCode, '', $currentUrl);
        }

        return $currentUrl;
    }

}