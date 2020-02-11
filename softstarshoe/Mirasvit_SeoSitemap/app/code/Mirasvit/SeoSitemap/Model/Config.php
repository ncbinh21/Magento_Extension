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



namespace Mirasvit\SeoSitemap\Model;

class Config
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Model\Context                   $context
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
    }

    /**
     * @param null $store
     * @return string
     */
    public function getFrontendSitemapBaseUrl($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_base_url',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getFrontendSitemapMetaTitle($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_meta_title',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getFrontendSitemapMetaKeywords($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_meta_keywords',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getFrontendSitemapMetaDescription($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_meta_description',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getFrontendSitemapH1($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/sitemap_h1',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return bool|int
     */
    public function getIsShowProducts($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_show_products',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return bool|int
     */
    public function getIsShowStores($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/is_show_stores',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getFrontendLinksLimit($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/frontend/links_limit',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return bool|int
     */
    public function getIsAddProductImages($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/is_add_product_images',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return bool|int
     */
    public function getIsEnableImageFriendlyUrls($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/is_enable_image_friendly_urls',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return string
     */
    public function getImageUrlTemplate($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/image_url_template',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getImageSizeWidth($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/image_size_width',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getImageSizeHeight($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/image_size_height',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return bool|int
     */
    public function getIsAddProductTags($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/is_add_product_tags',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getProductTagsChangefreq($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/product_tags_changefreq',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getProductTagsPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/product_tags_priority',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getLinkChangefreq($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/link_changefreq',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getLinkPriority($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/link_priority',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getSplitSize($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/split_size',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /**
     * @param null|string $store
     * @return int
     */
    public function getMaxLinks($store = null)
    {
        return $this->scopeConfig->getValue(
            'seositemap/google/max_links',
            \Magento\Framework\App\Config\ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $store
        );
    }

    /************************/
}
