<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbySeo
 */


namespace Amasty\ShopbySeo\Helper;

use Amasty\Shopby\Helper\Category;
use Amasty\Shopby\Model\ResourceModel\Catalog\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Module\Manager;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Url
 * @package Amasty\ShopbySeo\Helper
 */
class Url extends AbstractHelper
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Manager
     */
    protected $moduleManager;

    /**
     * @var CategoryCollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var bool
     */
    protected $isBrandFilterActive;

    /**
     * @var array
     */
    protected $originalParts;

    /**
     * @var array
     */
    protected $query;

    /**
     * @var string
     */
    protected $paramsDelimiterCurrent;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var string[]
     */
    protected $categoryUrls;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\Shopby\Model\Layer\Cms\Manager
     */
    protected $cmsManager;

    /** @var \Magento\Framework\App\ResourceConnection  */
    protected $resource;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    protected $settingHelper;

    /**
     * @var string
     */
    protected $aliasDelimiter;

    /**
     * @var string
     */
    protected $rootRoute;

    /**
     * @var null
     */
    protected $brandAttributeCode;

    /**
     * @var bool
     */
    protected $appendShopbySuffix;

    /**
     * @var string
     */
    protected $brandUrlKey;

    /**
     * @var int[]
     */
    private $filterPositions;

    /**
     * Url constructor.
     * @param Context $context
     * @param Data $helper
     * @param CategoryCollectionFactory $categoryCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Amasty\Shopby\Model\Layer\Cms\Manager $cmsManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Amasty\Shopby\Helper\FilterSetting $settingHelper
     */
    public function __construct(
        Context $context,
        Data $helper,
        CategoryCollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Amasty\Shopby\Model\Layer\Cms\Manager $cmsManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Amasty\ShopbyBase\Helper\FilterSetting $settingHelper
    ) {
        parent::__construct($context);
        $this->helper = $helper;
        $this->moduleManager = $context->getModuleManager();
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->coreRegistry = $coreRegistry;
        $this->cmsManager = $cmsManager;
        $this->storeManager = $storeManager;
        $this->resource = $resource;
        $this->settingHelper = $settingHelper;
        $this->aliasDelimiter =
            $this->scopeConfig->getValue('amasty_shopby_seo/url/option_separator', ScopeInterface::SCOPE_STORE);
        $this->rootRoute = trim($this->scopeConfig->getValue('amshopby_root/general/url', ScopeInterface::SCOPE_STORE));
        $this->brandAttributeCode = $this->moduleManager->isEnabled('Amasty_ShopbyBrand')
        && $this->scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE)
            ? $this->scopeConfig->getValue('amshopby_brand/general/attribute_code', ScopeInterface::SCOPE_STORE) : null;
        $this->appendShopbySuffix = $this->scopeConfig->isSetFlag('amasty_shopby_seo/url/add_suffix_shopby');
        $this->brandUrlKey =
            trim($this->scopeConfig->getValue('amshopby_brand/general/url_key', ScopeInterface::SCOPE_STORE));
    }

    /**
     * @return \Magento\Framework\App\RequestInterface
     */
    public function getRequest()
    {
        return $this->_getRequest();
    }

    /**
     * @param string $url
     * @return string
     */
    public function seofyUrl($url)
    {
        if (!$this->initialize($url) || $this->cmsManager->isCmsPageNavigation()) {
            return $url;
        }

        $this->query = $this->parseQuery();

        if (isset($this->query['options']) && $this->query['options'] == 'cart') {
            return $url;
        }

        $routeUrl = $this->originalParts['route'];

        $moduleName = $this->_getRequest()->getModuleName();
        $settingCategory = $this->settingHelper->getSettingByAttributeCode(Category::ATTRIBUTE_CODE);
        $fromRootToCategory = isset($this->query['cat'])
            && (in_array($moduleName, ['catalog', 'amshopby', 'cms', 'ambrand']))
            && !$settingCategory->isMultiselect();
        if ($fromRootToCategory) {
            $routeUrl = $this->getCategoryRouteUrl() ?: $routeUrl;
        }

        if ($this->coreRegistry->registry('amasty_shopby_root_category_index')
            && $this->query
            && !$fromRootToCategory
        ) {
            $routeUrl = $this->rootRoute;
            $isShopby = true;
        } else {
            $isShopby = false;
        }

        $routeUrlTrimmed = $this->removeCategorySuffix($routeUrl);
        $endsWithLine = strlen($routeUrlTrimmed)
            && $routeUrlTrimmed[strlen($routeUrlTrimmed) - 1] == DIRECTORY_SEPARATOR;
        if ($endsWithLine) {
            //if routeUrl is valid Magento route
            return $url;
        }

        $moveSuffix = $routeUrlTrimmed != $routeUrl;
        $resultPath = $routeUrlTrimmed;

        $seoAliases = $this->cutAliases();
        if ($seoAliases) {
            $resultPath = $this->injectAliases($resultPath, $seoAliases);
        }

        $resultPath = $this->cutReplaceExtraShopby($resultPath);
        $resultPath = ltrim($resultPath, DIRECTORY_SEPARATOR);

        if ($moveSuffix || ($isShopby && $this->appendShopbySuffix)) {
            $resultPath = $this->addCategorySuffix($resultPath);
        }

        $result = $this->query ? ($resultPath . '?' . $this->query2Params($this->query)) : $resultPath;

        if ($this->originalParts['hash']) {
            $result .= '#' . $this->originalParts['hash'];
        }

        return $this->originalParts['domain'] . $result;
    }

    /**
     * @param string $url
     * @return bool
     */
    protected function initialize($url)
    {
        $this->originalParts = [];

        /**
         * TODO: this code do not execute now. Maybe it is not necessary
         */
        $url = str_replace('amshopby/index/index/', $this->rootRoute, $url);

        if (!preg_match('@^([^/]*//[^/]*/)(.*)$@', $url)) {
            return false;
        }

        $parsedUrl = parse_url($url);
        $this->originalParts['domain'] = $this->storeManager->getStore()->getBaseUrl();
        $this->originalParts['route'] = isset($parsedUrl['path']) ? $parsedUrl['path'] : null;

        if (strpos($this->originalParts['route'], 'media/') !== false) {
            return false;
        }

        if ($this->originalParts['route'] !== null) {
            $routeBaseUrl = parse_url($this->originalParts['domain'], PHP_URL_PATH);
            if (strpos($this->originalParts['route'], $routeBaseUrl) === 0) {
                $this->originalParts['route'] = substr($this->originalParts['route'], strlen($routeBaseUrl));
                if (empty($this->originalParts['route'])) {
                    $this->originalParts['route'] = null;
                }
            }
        }

        $this->originalParts['params'] = isset($parsedUrl['query']) ? $parsedUrl['query'] : null;
        $this->originalParts['hash'] = isset($parsedUrl['fragment']) ? $parsedUrl['fragment'] : null;

        $delimiterEscaped = isset($parsedUrl['query']) && strpos($parsedUrl['query'], '&amp;') !== false;
        $this->paramsDelimiterCurrent = $delimiterEscaped ? '&amp;' : '&';

        return true;
    }

    /**
     * @return array
     */
    protected function parseQuery()
    {
        $query = [];
        $this->isBrandFilterActive = false;
        if (!isset($this->originalParts['params'])) {
            return $query;
        }

        $parts = explode($this->paramsDelimiterCurrent, $this->originalParts['params']);
        foreach ($parts as $part) {
            $param = explode('=', $part, 2);
            if (count($param) != 2) {
                continue;
            }

            $paramName = $param[0];
            $value = $param[1];
            $query[$paramName] = $value;
            if ($this->brandAttributeCode === $paramName) {
                $this->isBrandFilterActive = true;
            }
        }

        return $query;
    }

    /**
     * @return string
     */
    public function getCategoryRouteUrl()
    {
        $categoryId = isset($this->query['cat']) ?$this->query['cat']: null;
        return $this->getCategoryUrlById($categoryId);
    }

    /**
     * @param string|null $categoryId
     * @return string
     */
    private function getCategoryUrlById($categoryId)
    {
        if (!$categoryId) {
            return '';
        }

        if ($this->categoryUrls === null) {
            $collection = $this->categoryCollectionFactory->create();
            $collection->addUrlRewriteToResult();
            $select = $collection->getSelect();
            $select->reset('columns');
            $select->columns('entity_id');
            $urlRewriteTable = $this->resource->getTableName('url_rewrite');
            $select->columns($urlRewriteTable . '.request_path');
            $this->categoryUrls = $select->getAdapter()->fetchPairs($select);
        }

        return isset($this->categoryUrls[$categoryId]) ? $this->categoryUrls[$categoryId] : '';
    }

    /**
     * @return string[]
     */
    protected function cutAliases()
    {
        $optionsData = $this->helper->getOptionsSeoData();

        $aliasesByCode = [];
        $brandAliases = [];
        foreach ($this->query as $paramName => $rawValue) {
            if ($this->isParamSeoSignificant($paramName)) {
                $values = explode(',', str_replace('%2C', ',', $rawValue));
                foreach ($values as $value) {
                    if (!array_key_exists($value, $optionsData)) {
                        continue;
                    }
                    $alias = $optionsData[$value]['alias'];
                    if ($paramName == $this->brandAttributeCode) {
                        $brandAliases[] = $alias;
                    } else {
                        $attributeCode = $optionsData[$value]['attribute_code'];
                        if (array_key_exists($attributeCode, $aliasesByCode)) {
                            $aliasesByCode[$attributeCode][] = $alias;
                        } else {
                            $aliasesByCode[$attributeCode] = [$alias];
                        }
                    }
                }
                unset($this->query[$paramName]);
            }
        }

        $this->sortAliases($aliasesByCode);

        $aliases = $this->mergeAliases($brandAliases, $aliasesByCode);

        return $aliases;
    }

    /**
     * @param array $seoAliases
     */
    private function sortAliases(&$seoAliases)
    {
        $filterPositions = $this->getFilterPositions();
        uksort($seoAliases, function ($first, $second) use ($filterPositions) {
            if ($first == $second) {
                return 0;
            }

            if (!isset($filterPositions[$first])) {
                return 1;
            }

            if (!isset($filterPositions[$second])) {
                return -1;
            }

            return $filterPositions[$first] - $filterPositions[$second];
        });
    }

    /**
     * @return int[]|null
     */
    private function getFilterPositions()
    {
        if ($this->filterPositions === null) {
            $allFilters = $this->coreRegistry->registry(\Amasty\Shopby\Model\Layer\FilterList::ALL_FILTERS_KEY);

            if (!$allFilters) {
                return null;
            }

            $this->filterPositions = [];
            $position = 0;

            foreach ($allFilters as $filter) {
                $code = $filter->getRequestVar();
                $this->filterPositions[$code] = $position;
                $position++;
            }
        }

        return $this->filterPositions;
    }

    /**
     * @param string[] $brandAliases
     * @param string[][] $aliasesByCode
     * @return string[]
     */
    private function mergeAliases($brandAliases, $aliasesByCode)
    {
        $result = $brandAliases;
        array_walk_recursive($aliasesByCode, function ($alias) use (&$result) {
            $result[] = $alias;
        });
        return $result;
    }

    /**
     * @param string $param
     * @return bool
     */
    protected function isParamSeoSignificant($param)
    {
        $seoList = $this->helper->getSeoSignificantAttributeCodes();
        return in_array($param, $seoList);
    }

    /**
     * @param $routeUrl
     * @param array $aliases
     * @return string
     */
    protected function injectAliases($routeUrl, array $aliases)
    {
        $result = $routeUrl;
        if ($aliases) {
            $result .= DIRECTORY_SEPARATOR . implode($this->aliasDelimiter, $aliases);
        }
        return $result;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    protected function cutReplaceExtraShopby($url)
    {
        $cut = false;
        $allProductsEnabled =
            $this->moduleManager->isEnabled('Amasty_ShopbyRoot') &&
            $this->scopeConfig->isSetFlag('amshopby_root/general/enabled', ScopeInterface::SCOPE_STORE);
        if ($allProductsEnabled || $this->moduleManager->isEnabled('Amasty_ShopbyBrand')) {
            $l = strlen($this->rootRoute);
            if (substr($url, 0, $l) == $this->rootRoute
                && strlen($url) > $l
                && $url[$l] == DIRECTORY_SEPARATOR
            ) {
                $url = substr($url, strlen($this->rootRoute));
                $cut = true;
            }
        }

        if ($cut) {
            if ($this->isBrandFilterActive) {
                $url = $this->brandUrlKey . $url;
            }
        }
        return $url;
    }

    /**
     * @param array $query
     * @return string
     */
    protected function query2Params($query)
    {
        $result = [];
        foreach ($query as $code => $value) {
            $result[] = $code . '=' . $value;
        }
        return implode($this->paramsDelimiterCurrent, $result);
    }

    /**
     * @param string $url
     * @return string
     */
    public function addCategorySuffix($url)
    {
        $suffix = $this->scopeConfig->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE);
        if (strlen($suffix)) {
            $url .= $suffix;
        }
        return $url;
    }

    /**
     * @param string $url
     * @return bool|string
     */
    public function removeCategorySuffix($url)
    {
        $suffix = $this->scopeConfig->getValue('catalog/seo/category_url_suffix', ScopeInterface::SCOPE_STORE);
        if ($this->coreRegistry->registry('amasty_shopby_root_category_index') && $this->query) {
            if (strlen($suffix)) {
                $p = strrpos($this->rootRoute, $suffix);
                if ($p !== false && $p == strlen($this->rootRoute) - strlen($suffix)) {
                    return $url;
                }
            }
        }
        if (strlen($suffix)) {
            $p = strrpos($url, $suffix);
            if ($p !== false && $p == strlen($url) - strlen($suffix)) {
                $url = substr($url, 0, $p);
            }
        }
        return $url;
    }

    /**
     * @return bool
     */
    public function isSeoUrlEnabled()
    {
        return !!$this->scopeConfig->getValue('amasty_shopby_seo/url/mode', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return bool
     */
    public function isSeoRedirectEnabled()
    {
        return !!$this->scopeConfig->getValue('amasty_shopby_seo/url/seo_redirect', ScopeInterface::SCOPE_STORE);
    }
}
