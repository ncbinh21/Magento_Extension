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



namespace Mirasvit\Seo\Block\Snippets;

use Mirasvit\Seo\Model\Config as Config;

class Category extends \Magento\Framework\View\Element\Template
{
    const PRODUST_COLLECTION = 'category_product_for_snippets';
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * @var \Mirasvit\Seo\Model\Config
     */
    protected $config;

    /**
     * @var \Mirasvit\Seo\Helper\Snippets\Price
     */
    protected $seoSnippetsPriceHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory           $categoryFactory
     * @param \Magento\Catalog\Model\ProductFactory            $productFactory
     * @param \Mirasvit\Seo\Model\Config                       $config
     * @param \Mirasvit\Seo\Helper\Snippets\Price              $seoSnippetsPriceHelper
     * @param \Magento\Framework\Registry                      $registry
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory           $categoryFactory,
        \Magento\Catalog\Model\ProductFactory            $productFactory,
        \Mirasvit\Seo\Model\Config                       $config,
        \Mirasvit\Seo\Helper\Snippets\Price              $seoSnippetsPriceHelper,
        \Magento\Framework\Registry                      $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->config = $config;
        $this->seoSnippetsPriceHelper = $seoSnippetsPriceHelper;
        $this->registry = $registry;
        $this->request = $context->getRequest();
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @var bool
     */
    protected $isCategoryFilterChecked = false;

    /**
     * @var int, float
     */
    protected $categorySnippetsRating;

    /**
     * @var int
     */
    protected $categorySnippetsRatingCount;

    /**
     * @var string
     */
    protected $categoryMinPrice;

    /**
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return bool
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity) 
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function categorySnippetsFilter()
    {
        if ($this->getConfig()->getCategoryRichSnippets(
            $this->context->getStoreManager()->getStore()->getId()
        ) == Config::CATEGYRY_RICH_SNIPPETS_PAGE
        ) {
            if (!$this->registry->registry(self::PRODUST_COLLECTION)) {
                $this->isCategoryFilterChecked = true;

                return false;
            }
            $productCollection = $this->registry->registry(self::PRODUST_COLLECTION);
            if ($productCollection->count()) {
                $price = [];
                $rating = [];
                $reviewsCount = 0;
                foreach ($productCollection as $product) {
                    if (is_object($product->getRatingSummary())) {
                        if ($product->getRatingSummary()->getReviewsCount() > 0) {
                            $reviewsCount += $product->getRatingSummary()->getReviewsCount();
                        }
                        if ($product->getRatingSummary()->getRatingSummary() > 0) {
                            $rating[] = $product->getRatingSummary()->getRatingSummary();
                        }
                    }
                    if ($product->getFinalPrice() > 0) {
                        $price [] = $product->getFinalPrice();
                    } elseif ($product->getMinimalPrice() > 0) {
                        $price[] = $product->getMinimalPrice();
                    }
                }
                if (count($price) > 0) {
                    $this->categoryMinPrice = min($price);
                }

                if (array_sum($rating) > 0) {
                    $rating = array_filter($rating);
                    $summaryRating = array_sum($rating);
                    if ($this->getConfig()->getRichSnippetsRewiewCount(
                        $this->context->getStoreManager()->getStore()->getStoreId()
                    ) == Config::REVIEWS_NUMBER
                    ) {
                        $this->categorySnippetsRatingCount = $reviewsCount;
                        $this->categorySnippetsRating = $summaryRating / count($rating);
                    } else { //Config::PRODUCTS_WITH_REVIEWS_NUMBER
                        $this->categorySnippetsRatingCount = count($rating);
                        $this->categorySnippetsRating = $summaryRating / $this->categorySnippetsRatingCount;
                    }
                }
            }
        }

        if ($this->getConfig()->getCategoryRichSnippets(
            $this->context->getStoreManager()->getStore()->getId()
        ) == Config::CATEGYRY_RICH_SNIPPETS_CATEGORY
        ) {
            $currentCategory = false;
            if (!$currentCategory && $this->registry->registry('current_category')) {
                $currentCategory = $this->registry->registry('current_category');
            }

            if (!$currentCategory) {
                return false;
            }

            $minPriceProductCollection = $this->productFactory->create()
                                        ->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addStoreFilter($this->context->getStoreManager()->getStore()->getStoreId())
                                        ->addCategoryFilter($currentCategory)
                                        ->addAttributeToFilter(
                                            'visibility',
                                            [\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                                                 \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH, ]
                                        )
                                        ->addFieldToFilter(
                                            'status',
                                            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                                        )
                                        ->joinField(
                                            'rating_summary',
                                            'review_entity_summary',
                                            'rating_summary',
                                            'entity_pk_value=entity_id',
                                            [
                                                'entity_type' => 1,
                                                'store_id' => $this->context->getStoreManager()->getStore()->getId()
                                            ],
                                            'left'
                                        )
                                        ->joinField(
                                            'reviews_count',
                                            'review_entity_summary',
                                            'reviews_count',
                                            'entity_pk_value=entity_id',
                                            [
                                                'entity_type' => 1,
                                                'store_id' => $this->context->getStoreManager()->getStore()->getId()
                                            ],
                                            'left'
                                        )
                                        ->addFieldToFilter('price', ['gt' => 0])
                                        ->setOrder('price', 'ASC');

            if ($minPriceProductCollection->getSize() > 0) {
                $minPriceProductCollection->setPage(0, 1);
                $this->categoryMinPrice = $minPriceProductCollection->getFirstItem()->getFinalPrice();
            }

            $productRatingCollection = $this->productFactory->create()
                                        ->getCollection()
                                        ->addAttributeToSelect('*')
                                        ->addStoreFilter($this->context->getStoreManager()->getStore()->getStoreId())
                                        ->addCategoryFilter($currentCategory)
                                        ->addAttributeToFilter(
                                            'visibility',
                                            [\Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                                                \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH, ]
                                        )
                                        ->addFieldToFilter(
                                            'status',
                                            \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
                                        )
                                        ->joinField(
                                            'rating_summary',
                                            'review_entity_summary',
                                            'rating_summary',
                                            'entity_pk_value=entity_id',
                                            [
                                                'entity_type' => 1,
                                                'store_id' => $this->context->getStoreManager()->getStore()->getId()
                                            ],
                                            'left'
                                        )
                                        ->joinField(
                                            'reviews_count',
                                            'review_entity_summary',
                                            'reviews_count',
                                            'entity_pk_value=entity_id',
                                            [
                                                'entity_type' => 1,
                                                'store_id' => $this->context->getStoreManager()->getStore()->getId()
                                            ],
                                            'left'
                                        )
                                        ->addFieldToFilter('price', ['gt' => 0])
                                        ->setOrder('price', 'ASC')
                                        ->addFieldToFilter('rating_summary', ['nin' => [0, null]]);

            if ($productRatingCollection->getSize() > 0) {
                $rating = $productRatingCollection->getColumnValues('rating_summary');
                $rating = array_diff($rating, [0, null]);
                // double check, for some stores addFieldToFilter('rating_summary', ["nin" => [0, NULL]])
                // return also empty fields
                $summaryRating = array_sum($rating);

                if ($this->getConfig()->getRichSnippetsRewiewCount(
                    $this->context->getStoreManager()->getStore()->getStoreId()
                ) == Config::REVIEWS_NUMBER
                ) {
                    $this->categorySnippetsRatingCount = array_sum(
                        $productRatingCollection->getColumnValues('reviews_count')
                    );
                    $this->categorySnippetsRating = (count($rating) > 0) ? ($summaryRating / count($rating)) : 0;
                } else { //Config::PRODUCTS_WITH_REVIEWS_NUMBER
                    $this->categorySnippetsRatingCount = count($rating);
                    if ($this->categorySnippetsRatingCount > 0) {
                        $this->categorySnippetsRating = $summaryRating / $this->categorySnippetsRatingCount;
                    } else {
                        $this->categorySnippetsRating = 0;
                    }
                }
            }
        }

        $this->isCategoryFilterChecked = true;
    }

    /**
     * @return string
     */
    public function getCategoryMinPrice()
    {
        if (!$this->isCategoryFilterChecked) {
            $this->categorySnippetsFilter();
        }

        return $this->seoSnippetsPriceHelper->formatPriceValue($this->categoryMinPrice);

    }

    /**
     * @return int, float
     */
    public function getCategorySnippetsRating()
    {
        if (!$this->isCategoryFilterChecked) {
            $this->categorySnippetsFilter();
        }

        return $this->categorySnippetsRating;
    }

    /**
     * @return int
     */
    public function getCategorySnippetsRatingCount()
    {
        if (!$this->isCategoryFilterChecked) {
            $this->categorySnippetsFilter();
        }

        return $this->categorySnippetsRatingCount;
    }

    /**
     * @return string
     */
    public function getCurrentCategoryName()
    {
        return $this->registry->registry('current_category')->getName();
    }

    /**
     * @return string
     */
    public function getCurrentCurrencyCode()
    {
        return $this->context->getStoreManager()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * @return bool
     */
    public function isCategoryRichSnippetsEnabled()
    {
        if ($this->getConfig()->getCategoryRichSnippets($this->context->getStoreManager()->getStore()->getId())) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getCurrentPageProductCount()
    {
        $categoryProductCount = 0;
        if ($categoryProduct = $this->registry->registry(self::PRODUST_COLLECTION)) {
            $categoryProductCount = $categoryProduct->count();
        }

        return $categoryProductCount;
    }
}
