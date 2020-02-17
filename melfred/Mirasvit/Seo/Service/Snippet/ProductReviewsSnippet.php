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



namespace Mirasvit\Seo\Service\Snippet;

use Mirasvit\Seo\Api\Service\Snippet\ProductReviewsSnippetInterface;
use Mirasvit\Seo\Api\Config\ProductSnippetConfigInterface;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Mirasvit\Seo\Traits\SnippetsTrait;

class ProductReviewsSnippet implements ProductReviewsSnippetInterface
{
    /**
     * @param ProductSnippetConfigInterface $config
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
     */
    public function __construct(
        ProductSnippetConfigInterface $config,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $reviewCollection
    ) {
        $this->config = $config;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->reviewCollection = $reviewCollection;
    }

    /**
     * @return string
     */
    public function getReviewsSnippets()
    {
        $reviewsSnippets = '';
        if (!$this->config->IsIndividualReviewsSnippetsEnabled()) {
            return $reviewsSnippets;
        } elseif ($product = $this->registry->registry('current_product')) {
            $reviewsSnippets = $this->getIndividualReviewsData($product);
        }
        return $reviewsSnippets;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getIndividualReviewsData($product)
    {
        $reviewsData = '';

        $collection = $this->reviewCollection->create()
                ->addStatusFilter(\Magento\Review\Model\Review::STATUS_APPROVED)
                ->addEntityFilter('product', $product->getId())
                ->setDateOrder();
        if (count($collection)) {
            foreach ($collection as $review) {
                $reviewsData .= '
                <script type="application/ld+json">
                {
                    "@context": "http://schema.org/",
                      "@type": "Review",
                      "itemReviewed": {
                        "@type": "Thing",
                        "name": "' . SnippetsTrait::prepareSnippet($product->getName()) . '"
                      },
                      ' . $this->getReviewTitle($review) . '
                      "author": {
                        "@type": "Person",
                        "name": "' . SnippetsTrait::prepareSnippet($review->getNickname()) . '"
                      },
                      ' . $this->getDatePublished($review) . '
                      "reviewBody": "' . SnippetsTrait::prepareSnippet($review->getDetail()) . '"
                      ' . $this->getPublisher() . '
                }
                </script>';
            }
        }

        return $reviewsData;
    }

    /**
     * @return string
     */
    private function getStoreName()
    {
        return $this->scopeConfig->getValue(
                                  'general/store_information/name',
                                  \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                                  );
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return string
     */
    private function getReviewTitle($review)
    {
        return $review->getTitle() ? '"name": "' . SnippetsTrait::prepareSnippet($review->getTitle()) . '",' : '' ;
    }

    /**
     * @param \Magento\Review\Model\Review $review
     * @return string
     */
    private function getDatePublished($review)
    {
        return $review->getCreatedAt() ? '"datePublished": "' . $review->getCreatedAt() . '",' : '' ;
    }

    /**
     * @return string
     */
    private function getPublisher()
    {
        if ($storeName = $this->getStoreName()) {
            return ', "publisher": {
                        "@type": "Organization",
                        "name": "' . SnippetsTrait::prepareSnippet($storeName) . '"
                      }';
        } else {
            return '';
        }
    }
}