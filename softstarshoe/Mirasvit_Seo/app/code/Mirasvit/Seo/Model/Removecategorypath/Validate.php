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



namespace Mirasvit\Seo\Model\Removecategorypath;

use Magento\Store\Model\StoreManagerInterface;

class Validate extends \Magento\Framework\DataObject
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection
     */
    protected $urlRewriteCategory;

     /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store Id of processed category
     *
     * @var int
     */
    protected $storeId;

    /**
     * Store Ids for which will do validation
     *
     * @var int
     */
    protected $storeIds;

    /**
     * @param \Magento\Framework\UrlInterface                              $urlBuilder
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Framework\App\Request\Http                          $request
     * @param \Mirasvit\Seo\Model\Config                                   $config
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite
     * @param \Magento\Framework\App\Config\ScopeConfigInterface           $scopeConfig
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Request\Http $request,
        \Mirasvit\Seo\Model\Config $config,
        StoreManagerInterface $storeManager,
        \Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection $urlRewrite,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->registry = $registry;
        $this->request = $request;
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->urlRewriteCategory = $urlRewrite->addFieldToFilter('entity_type', 'category')
                                                ->addFieldToFilter('redirect_type', 0);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Check category urls duplicate
     *
     * @return string|bool
     */
    public function checkCategoyUrlsDuplicate()
    {
        $currentStoreId = $this->request->getParam('store_id');
        $currentWebsiteId = $this->request->getParam('website_id');

        if (!$this->isEnabledRemoveParentCategoryPath($currentStoreId, $currentWebsiteId)) {
            return '"Use Categories Path for Product URLs" is disabled.<br/> Reload the page.';
        }

        $this->setStoreIds($currentStoreId, $currentWebsiteId);

        if(!$this->storeIds) {
            return 'Something went wrong.';
        }

        if ($categoriesPathStoresInfo = $this->getUseCategoriesPathStoresInfo()) {
            return $categoriesPathStoresInfo;
        }

        $urlRewriteCategory = $this->urlRewriteCategory->addFieldToFilter('store_id', ['in' => $this->storeIds]);
        $rewrite = [];
        $duplicate = [];
        $duplicateCategoryPath = false;
        foreach ($urlRewriteCategory as $categoryRewrite) {
            $categoryId = $categoryRewrite->getEntityId();
            $requestPath = $categoryRewrite->getRequestPath();
            $storeId = $categoryRewrite->getStoreId();
            $rewrite[$storeId][$categoryId] = $requestPath;
        }

        foreach ($rewrite as $storeId => $categoryData) {
            $this->storeId = $storeId;
            if (($duplicateInfo = $this->getDuplicateUrls($categoryData))
                && $duplicateInfo !== true) {
                    return $duplicateInfo;
            }
        }

        return true;
    }

    /**
     * Check if Use Categories Path for Product URLs is enabled
     *
     * @param int $storeId
     * @param int $websiteId
     * @return bool
     */
    protected function isEnabledRemoveParentCategoryPath($storeId, $websiteId) {
        if ($storeId) {
            $result = $this->config->isEnabledRemoveParentCategoryPath($storeId);
        } elseif ($websiteId) {
            $result = $this->config->isEnabledRemoveParentCategoryPath(null, $websiteId);
        } elseif($storeId == 0 && $websiteId == 0) {
            $result = $this->config->isEnabledRemoveParentCategoryPath();
        }

        return $result;
    }


    /**
     * Get stores with Use Categories Path for Product URLs enabled
     *
     * @return bool|string
     */
    protected function getUseCategoriesPathStoresInfo() {
        $stores = [];
        foreach ($this->storeManager->getStores() as $store) {
            $storeId = $store->getId();
            if (in_array($storeId, $this->storeIds) && $this->isProductLongUrlEnabled($storeId)) {
                $stores[$storeId] = $store->getName() . '( ID: ' . $storeId . ' )';
            }
        }
        if ($stores) {
            return '"Use Categories Path for Product URLs" enabled for following stores: <br/>'
                    . implode(', ', $stores)
                    . '.<br/> Category URLs for this stores will not be changed.';
        }

        return false;
    }

    /**
     * Set store Ids
     *
     * @param int $storeId
     * @param int $websiteId
     * @return void
     */
    protected function setStoreIds($storeId, $websiteId) {
        if ($storeId) {
            $this->storeIds = [$storeId];
        } elseif ($websiteId) {
                $this->storeIds = $this->storeManager->getWebsite($websiteId)->getStoreIds();
        } elseif($storeId == 0 && $websiteId == 0) {
            foreach ($this->storeManager->getStores() as $store) {
                $this->storeIds[] = $store->getId();
            }
        }

        foreach ($this->storeIds as $key => $storeId) {
             if (!$this->config->isEnabledRemoveParentCategoryPath($storeId)) {
                unset($this->storeIds[$key]);
             }
        }

        $this->registry->register('store_ids_for_remove_parent_category_path', $this->storeIds);
    }

    /**
     * Check if "Use Categories Path for Product URLs" enabled
     *
     * @param int $storeId
     * @return bool
     */
    protected function isProductLongUrlEnabled($storeId) {
        return $this->scopeConfig->getValue(
                   \Magento\Catalog\Helper\Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
                   \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                   $storeId
               );
    }

    /**
     * Get duplicate urls array or get duplicate urls info text
     *
     * @param array $categoryData
     * @param bool $getResult
     * @return array|string|bool
     */
    public function getDuplicateUrls($categoryData, $getResult = false) {
        $info = false;
        $preparedCategoryUrl = [];
        foreach ($categoryData as $categoryId => $categoryUrl) {
            $categoryPath = explode('/', $categoryUrl);
            $preparedCategoryUrl[$categoryId] = $categoryPath[count($categoryPath) - 1];
        }
        $withoutDuplicates = array_unique($preparedCategoryUrl);
        $duplicate = array_diff_key($preparedCategoryUrl, $withoutDuplicates);
        $duplicate = array_intersect($preparedCategoryUrl, $duplicate);

        if ($getResult) {
            return $duplicate;
        }

        if ($duplicate) {
            return $this->getDuplicateInfo();
        }

        return true;
    }

    /**
     * Get duplicate urls info text
     *
     * @return string
     */
    protected function getDuplicateInfo() {
        $url = $this->urlBuilder->getUrl('seo/duplicateinfo/index');

        return '<div style="color:#ce4400;">Parent Category Path is not removed <br/>
        Please visit <a href="'. $url .'" target="_blank">check duplicate urls</a> <br/>
        After fix all issues there push the button again</div>';
    }
}
