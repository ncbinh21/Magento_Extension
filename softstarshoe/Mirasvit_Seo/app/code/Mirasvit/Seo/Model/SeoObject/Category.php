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



namespace Mirasvit\Seo\Model\SeoObject;

class Category extends \Mirasvit\Seo\Model\SeoObject\AbstractObject
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\StoreFactory
     */
    protected $objectStoreFactory;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\PagerFactory
     */
    protected $objectPagerFactory;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory
     */
    protected $objectWrapperFilterFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

     /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Mirasvit\Seo\Model\SeoObject\StoreFactory                      $objectStoreFactory
     * @param \Mirasvit\Seo\Model\SeoObject\PagerFactory                      $objectPagerFactory
     * @param \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory             $objectWrapperFilterFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Mirasvit\Seo\Model\Config                                      $config
     * @param \Magento\Framework\Stdlib\StringUtils                           $string
     * @param \Mirasvit\Seo\Helper\Data                                       $seoData
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function __construct(
        \Magento\Framework\Registry                                     $registry,
        \Magento\Store\Model\StoreManagerInterface                      $storeManager,
        \Mirasvit\Seo\Model\SeoObject\StoreFactory                      $objectStoreFactory,
        \Mirasvit\Seo\Model\SeoObject\PagerFactory                      $objectPagerFactory,
        \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory             $objectWrapperFilterFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Mirasvit\Seo\Model\Config                                      $config,
        \Magento\Framework\Stdlib\StringUtils                           $string,
        \Mirasvit\Seo\Helper\Data                                       $seoData
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->objectStoreFactory = $objectStoreFactory;
        $this->objectPagerFactory = $objectPagerFactory;
        $this->objectWrapperFilterFactory = $objectWrapperFilterFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->config = $config;
        $this->string = $string;
        $this->seoData = $seoData;
        $this->_construct();
    }

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;
    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;
    /**
     * @var array
     */
    protected $parseObjects = [];

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function _construct()
    {
        $this->category = $this->registry->registry('seo_current_category');
        if (!$this->category) {
            $this->category = $this->registry->registry('current_category');
        }

        if (!$this->category || $this->seoData->getFullActionCode() == 'brand_index_viewbrand') {
            return;
        }

        if ($this->category && $parent = $this->category->getParentCategory()) {
            if ($this->storeManager->getStore()->getRootCategoryId() != $parent->getId()) {
                if (is_object($parent) && $parent->getLevel() > 0
                    && ($parentParent = $parent->getParentCategory())
                    && ($this->storeManager->getStore()->getRootCategoryId() != $parentParent->getId())) {
                    $this->setAdditionalVariable('category', 'parent_parent_name', $parentParent->getName());
                }
                $this->setAdditionalVariable('category', 'parent_name', $parent->getName());
                $this->setAdditionalVariable('category', 'parent_url', $parent->getUrl());
            }
            $this->setAdditionalVariable('category', 'url', $this->category->getUrl());
            $this->setAdditionalVariable('category', 'page_title', $this->category->getMetaTitle());
        }
        if ($this->category) {
            $this->parseObjects['category'] = $this->category;
        }

        //мы можем создавать данную модель при расчете сео продукта
        $this->product = $this->registry->registry('current_product');
        if ($this->product) {
            $this->parseObjects['product'] = $this->product;
            $this->setAdditionalVariable('product', 'url', $this->product->getProductUrl());
        }
        $this->parseObjects['store'] = $this->objectStoreFactory->create();
        $this->parseObjects['pager'] = $this->objectPagerFactory->create();
        $this->parseObjects['filter'] = $this->objectWrapperFilterFactory->create();

        $this->init();
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function init()
    {
        if (!$this->category) {
            return;
        }

        $isCategoryMetaTagsUsed = $this->config->isCategoryMetaTagsUsed();
        $templateData = $this->registry->registry('m_seo_template_data');
        $categoryMetaTitle = trim($this->category->getMetaTitle());
        $categoryMetaKeywords = trim($this->category->getMetaKeywords());
        $categoryMetaDescription = trim($this->category->getMetaDescription());

        if ($isCategoryMetaTagsUsed && !$this->getMetaTitle() && $categoryMetaTitle) {
            $this->setMetaTitle($this->parse($categoryMetaTitle));
        }
        if ($isCategoryMetaTagsUsed && !$this->getMetaKeywords() && $categoryMetaKeywords) {
            $this->setMetaKeywords($this->parse($categoryMetaKeywords));
        }
        if ($isCategoryMetaTagsUsed && !$this->getMetaDescription() && $categoryMetaDescription) {
            $this->setMetaDescription($this->parse($categoryMetaDescription));
        }

        if ((!$isCategoryMetaTagsUsed || !$categoryMetaTitle)
            && isset($templateData['meta_title']) && trim($templateData['meta_title'])) {
            $this->setMetaTitle($templateData['meta_title']);
        }

        if ((!$isCategoryMetaTagsUsed || !$categoryMetaKeywords)
            && isset($templateData['meta_keywords']) && trim($templateData['meta_keywords'])) {
            $this->setMetaKeywords($templateData['meta_keywords']);
        }
        if ((!$isCategoryMetaTagsUsed || !$categoryMetaDescription)
            && isset($templateData['meta_description']) && trim($templateData['meta_description'])) {
            $this->setMetaDescription($templateData['meta_description']);
        }

        if (!$this->getTitle()) {
            $this->setTitle($this->category->getName());
        }

        if (!$this->getMetaTitle()) {
            if ($categoryMetaTitle) {
                $this->setMetaTitle($this->parse($categoryMetaTitle));
            } else {
                $this->setMetaTitle($this->category->getName());
            }
        }

        if (!$this->getMetaKeywords()) {
            if ($categoryMetaKeywords) {
                $this->setMetaKeywords($this->parse($categoryMetaKeywords));
            } else {
                $this->setMetaKeywords($this->category->getName());
            }
        }

        if (!$this->getMetaDescription()) {
            if ($categoryMetaDescription) {
                $this->setMetaDescription($this->parse($categoryMetaDescription));
            } else {
                $this->setMetaDescription($this->category->getName());
            }
        }
    }
}
