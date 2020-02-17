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



namespace Mirasvit\Seo\Model\SeoObject;

class Product extends \Mirasvit\Seo\Model\SeoObject\AbstractObject
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Mirasvit\Seo\Helper\Data
     */
    protected $seoData;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\StoreFactory
     */
    protected $objectStoreFactory;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\Framework\Registry                                     $registry
     * @param \Mirasvit\Seo\Helper\Data                                       $seoData
     * @param \Mirasvit\Seo\Model\SeoObject\StoreFactory                      $objectStoreFactory
     * @param \Magento\Framework\Stdlib\StringUtils                           $string
     * @param \Magento\Store\Model\StoreManagerInterface                      $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Mirasvit\Seo\Model\Config                                      $config
     */
    public function __construct(
        \Magento\Framework\Registry                                     $registry,
        \Mirasvit\Seo\Helper\Data                                       $seoData,
        \Mirasvit\Seo\Model\SeoObject\StoreFactory                      $objectStoreFactory,
        \Magento\Framework\Stdlib\StringUtils                           $string,
        \Magento\Store\Model\StoreManagerInterface                      $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory                          $categoryFactory,
        \Mirasvit\Seo\Model\Config                                      $config
    ) {
        $this->registry = $registry;
        $this->seoData = $seoData;
        $this->objectStoreFactory = $objectStoreFactory;
        $this->string = $string;
        $this->storeManager = $storeManager;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->config = $config;
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
     */
    public function _construct()
    {
        parent::_construct();
        $this->product = $this->registry->registry('current_product');
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        if (!$this->product) {
            return;
        }

        $this->parseObjects['product'] = $this->product;

        $this->setAdditionalVariable('product', 'url', $this->product->getProductUrl());
        $this->setAdditionalVariable('product', 'final_price', $this->product->getFinalPrice());
        $this->setAdditionalVariable(
            'product',
            'final_price_minimal',
            $this->seoData->getCurrentProductFinalPrice($this->product)
        );
        $this->setAdditionalVariable(
            'product',
            'final_price_range',
            $this->seoData->getCurrentProductFinalPriceRange($this->product)
        );

        $categoryId = $this->product->getSeoCategory();
        $this->category = $this->registry->registry('current_category');

        if ($this->category && !$categoryId) {
            $this->parseObjects['category'] = $this->category;
        } elseif ($this->product) {
            if (!$categoryId) {
                $categoryIds = $this->product->getCategoryIds();
                if (count($categoryIds) > 0) {
                    //we need this for multi websites configuration
                    $categoryRootId = $this->storeManager->getStore()->getRootCategoryId();
                    $category = $this->categoryCollectionFactory->create()
                                ->addFieldToFilter('path', ['like' => "%/{$categoryRootId}/%"])
                                ->addFieldToFilter('entity_id', $categoryIds)
                                ->setOrder('level', 'desc')
                                ->setOrder('entity_id', 'desc')
                                ->getFirstItem()
                            ;
                    $categoryId = $category->getId();
                }
            }
            //load category with flat data attributes
            $category = $this->categoryFactory->create()->load($categoryId);
            $this->category = $category;
            $this->parseObjects['category'] = $category;
            if (!$this->registry->registry('seo_current_category')) {
                // to be sure that register will not be done twice
                $this->registry->register('seo_current_category', $category);
            };
        }

        $this->parseObjects['store'] = $this->objectStoreFactory->create();

        $this->init();
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *  @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function init()
    {
        $isProductMetaTagsUsed = $this->config->isProductMetaTagsUsed();
        $templateData = $this->registry->registry('m_seo_template_data');
        $productMetaTitle = trim($this->product->getMetaTitle());
        //magento use getMetaKeyword instead getMetaKeywords
        $productMetaKeyword = trim($this->product->getMetaKeyword());
        $productMetaDescription = trim($this->product->getMetaDescription());

        if ($isProductMetaTagsUsed && !$this->getMetaTitle() && $productMetaTitle) {
            $this->setMetaTitle($this->parse($productMetaTitle));
        }

        if ($isProductMetaTagsUsed && !$this->getMetaKeyword() && $productMetaKeyword) {
            $this->setMetaKeywords($this->parse($productMetaKeyword));
        }

        if ($isProductMetaTagsUsed && !$this->getMetaDescription() && $productMetaDescription) {
            $this->setMetaDescription($this->parse($productMetaDescription));
        }

        if ((!$isProductMetaTagsUsed || !$productMetaTitle)
            && isset($templateData['meta_title']) && trim($templateData['meta_title'])) {
            $this->setMetaTitle($templateData['meta_title']);
        }
        if ((!$isProductMetaTagsUsed || !$productMetaKeyword)
            && isset($templateData['meta_keywords']) && trim($templateData['meta_keywords'])) {
            $this->setMetaKeywords($templateData['meta_keywords']);
        }
        if ((!$isProductMetaTagsUsed || !$productMetaDescription)
            && isset($templateData['meta_description']) && trim($templateData['meta_description'])) {
            $this->setMetaDescription($templateData['meta_description']);
        }

        if (!$this->getTitle()) {
            $this->setTitle($this->product->getName());
        }

        if (!$this->getMetaTitle()) {
            if ($productMetaTitle) {
                $this->setMetaTitle($this->parse($productMetaTitle));
            } else {
                $this->setMetaTitle($this->product->getName());
            }
        }

        if (!$this->getMetaKeywords()) {
            if ($productMetaKeyword) {
                $this->setMetaKeywords($this->parse($productMetaKeyword));
            } else {
                $this->setMetaKeywords($this->product->getName());
            }
        }

        if (!$this->getMetaDescription()) {
            if ($productMetaDescription) {
                $this->setMetaDescription($this->parse($productMetaDescription));
            } elseif ($this->product->getDescription()) {
                $this->setMetaDescription($this->string->substr($this->product->getDescription(), 0, 255));
            } else {
                $this->setMetaDescription($this->product->getName());
            }
        }
    }
}
