<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Block;

use Magento\Framework\DataObject;
use Magento\Framework\View\Element\AbstractBlock;
use Amasty\SeoRichData\Model\Source\Product\Description as DescriptionSource;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;

class Product extends AbstractBlock
{
    const IN_STOCK = 'http://schema.org/InStock';
    const OUT_OF_STOCK = 'http://schema.org/OutOfStock';
    const NEW_CONDITION = 'http://schema.org/NewCondition';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    private $pageConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var \Amasty\SeoRichData\Helper\Config
     */
    private $configHelper;

    /**
     * Product constructor.
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Page\Config $pageConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param ConfigHelper $configHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Page\Config $pageConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->coreRegistry = $coreRegistry;
        $this->pageConfig = $pageConfig;
        $this->storeManager = $storeManager;
        $this->stockRegistry = $stockRegistry;
        $this->configHelper = $configHelper;
    }

    protected function _toHtml()
    {
        if (!$this->configHelper->forProductEnabled()) {
            return '';
        }

        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->coreRegistry->registry('current_product');

        $description = '';
        switch ($this->configHelper->getProductDescriptionMode()) {
            case DescriptionSource::SHORT_DESCRIPTION:
                $description = $product->getShortDescription();
                break;
            case DescriptionSource::FULL_DESCRIPTION:
                $description = $product->getDescription();
                break;
            case DescriptionSource::META_DESCRIPTION:
                $description = $this->pageConfig->getDescription();
                break;
        }

        $offers = $this->prepareOffers($product);

        $offers = $this->unsetUnnecessaryData($offers);

        $rating = $this->getRating($product);

        $image = '';
        $images = $product->getMediaGalleryImages();
        if ($images instanceof \Magento\Framework\Data\Collection) {
            /** @var DataObject $image */
            foreach ($images as $image) {
                if ($product->getImage() == $image->getData('file')) {
                    $image = $image->getData('url');
                    break;
                }
            }
        }

        $product = [
            '@context' => 'http://schema.org',
            '@type' => 'Product',
            'name' => $product->getName(),
            'sku' => $product->getSku(),
            'description' => $description,
            'image' => $image,
            'aggregateRating' => $rating,
            'offers' => $offers
        ];

        $json = json_encode($product);
        $result = "<script type=\"application/ld+json\">{$json}</script>";

        return $result;
    }

    protected function prepareOffers($product)
    {
        $offers[] = [];

        $priceCurrency = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $orgName = $this->storeManager->getStore()->getFrontendName();

        if (($product->getTypeId() == 'configurable' && $this->configHelper->showConfigurable())
                || ($product->getTypeId() == 'grouped' && $this->configHelper->showGrouped())
            ) {
            if ($product->getTypeId() == 'configurable') {
                $children = $product->getTypeInstance()->getUsedProducts($product);
            }

            if ($product->getTypeId() == 'grouped') {
                $children = $product->getTypeInstance()->getAssociatedProducts($product);
            }

            if (isset($children)) {
                /** @var \Magento\Catalog\Model\Product $child */
                foreach ($children as $child) {
                    $offers[] = $this->generateOffers($child, $priceCurrency, $orgName);
                }
            }
        } else {
            $offers[] = $this->generateOffers($product, $priceCurrency, $orgName);
        }

        return $offers;
    }

    protected function unsetUnnecessaryData($offers)
    {
        if (!$this->configHelper->showAvailability()) {
            foreach ($offers as $key => $offer) {
                if (isset($offer['availability'])) {
                    unset($offers[$key]['availability']);
                }
            }
        }

        if (!$this->configHelper->showCondition()) {
            foreach ($offers as $key => $offer) {
                if (isset($offer['itemCondition'])) {
                    unset($offers[$key]['itemCondition']);
                }
            }
        }

        return $offers;
    }

    /**
     * @param $product
     * @return array
     */
    protected function getRating($product)
    {
        $rating = [];
        if ($this->configHelper->showRating()) {
            $ratingSummary = $product->getRatingSummary();
            
            if (isset($ratingSummary['reviews_count'])) {
                $rating =
                    [
                        '@type' => 'AggregateRating',
                        'ratingValue' => $ratingSummary['rating_summary'],
                        'bestRating' => 100,
                        'reviewCount' => $ratingSummary['reviews_count']
                    ];
            }
        }

        return $rating;
    }

    /**
     * @param $product
     * @param $priceCurrency
     * @param $orgName
     * @return array
     */
    protected function generateOffers($product, $priceCurrency, $orgName)
    {
        $isInStock = $this->stockRegistry->getProductStockStatus($product->getId());
        $offers  = [
            '@type' => 'Offer',
            'priceCurrency' => $priceCurrency,
            'price' => $product->getFinalPrice(),
            'availability' =>
                $isInStock ? self::IN_STOCK : self::OUT_OF_STOCK,
            'itemCondition' => self::NEW_CONDITION,
            'seller' => [
                '@type' => 'Organization',
                'name' => $orgName
            ]
        ];

        return $offers;
    }
}

