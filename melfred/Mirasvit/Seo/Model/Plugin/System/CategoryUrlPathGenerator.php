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



namespace Mirasvit\Seo\Model\Plugin\System;

class CategoryUrlPathGenerator
{
    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $categoryUrlSuffix = [];

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator
     */
    protected $urlPathGenerator;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Mirasvit\Seo\Model\Config                                $config
     * @param \\Magento\Store\Model\StoreManagerInterface               $storeManager
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $urlPathGenerator
     * @param \\Magento\Framework\App\Config\ScopeConfigInterface       $scopeConfig
     */
    public function __construct(
       \Mirasvit\Seo\Model\Config $config,
       \Magento\Store\Model\StoreManagerInterface $storeManager,
       \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $urlPathGenerator,
       \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
        $this->urlPathGenerator = $urlPathGenerator;
        $this->scopeConfig = $scopeConfig;

    }

    /**
     * Get category url path
     *
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator\Interceptor subject
     * @param callable $proceed
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return string
     */
    public function aroundGetUrlPathWithSuffix($subject, $proceed, $category, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $category->getStoreId();
        }

        $categoryUrlPath = $this->urlPathGenerator->getUrlPath($category);

        if ($this->config->isEnabledRemoveParentCategoryPath($storeId)
            && $categoryUrlPath && strpos($categoryUrlPath, '/') !== false) {
                $path = explode('/', $categoryUrlPath);
                $categoryUrlPath = $path[count($path) - 1];
        }

        return $categoryUrlPath . $this->getCategoryUrlSuffix($storeId);
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    protected function getCategoryUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        if (!isset($this->categoryUrlSuffix[$storeId])) {
            $this->categoryUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->categoryUrlSuffix[$storeId];
    }
}
