<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */

namespace Amasty\SeoRichData\Block;

use Amasty\SeoRichData\Helper\Category as CategoryHelper;
use Amasty\SeoRichData\Model\DataCollector;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Store\Model\StoreManagerInterface;
use Amasty\SeoRichData\Model\Source\Category\Description as DescriptionSource;
use Amasty\SeoRichData\Helper\Config as ConfigHelper;

class JsonLd extends AbstractBlock
{
    /**
     * @var DataCollector
     */
    protected $dataCollector;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * Core registry
     *
     * @var CategoryHelper
     */
    protected $categoryHelper = null;

    /**
     * @var \Magento\Framework\View\Page\Config
     */
    protected $pageConfig;

    /**
     * @var \Amasty\SeoRichData\Helper\Config
     */
    private $configHelper;

    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        DataCollector $dataCollector,
        Registry $coreRegistry,
        StoreManagerInterface $storeManager,
        CategoryHelper $categoryHelper,
        EncoderInterface $jsonEncoder,
        \Magento\Framework\View\Page\Config $pageConfig,
        ConfigHelper $configHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->coreRegistry = $coreRegistry;
        $this->jsonEncoder = $jsonEncoder;
        $this->dataCollector = $dataCollector;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->pageConfig = $pageConfig;
        $this->configHelper = $configHelper;
    }

    protected function prepareData()
    {
        $data = [];

        $this->addBreadcrumbsData($data);
        $this->addWebsiteName($data);
        $this->addOrganizationData($data);
        $this->addCategoryData($data);
        $this->addSearchData($data);

        return $data;
    }

    protected function addWebsiteName(&$data)
    {
        if (!$this->configHelper->forWebsiteEnabled()) {
            return;
        }

        $name = $this->configHelper->getWebsiteName();

        if ($name) {
            $this->addWebsiteData($data);
            $data['website']['name'] = $name;
        }
    }

    protected function addBreadcrumbsData(&$data)
    {
        $breadcrumbs = $this->dataCollector->getData('breadcrumbs');
        if (is_array($breadcrumbs)) {
            $items = [];
            $position = 0;
            foreach ($breadcrumbs as $breadcrumb) {
                if (!$breadcrumb['link'])
                    continue;

                $items []= [
                    '@type' => 'ListItem',
                    'position' => ++$position,
                    'item' => [
                        '@id' => $breadcrumb['link'],
                        'name' => $breadcrumb['label']
                    ]
                ];
            }

            if (sizeof($items) > 0) {
                $data['breadcrumbs'] = [
                    '@context'        => 'http://schema.org',
                    '@type'           => 'BreadcrumbList',
                    'itemListElement' => $items
                ];
            }
        }
    }

    protected function addOrganizationData(&$data)
    {
        if (!$this->configHelper->forOrganizationEnabled()) {
            return;
        }

        $data['organization'] = [
            '@context' => 'http://schema.org',
            '@type' => 'Organization',
            'url' => $this->_urlBuilder->getBaseUrl()
        ];

        if ($name = $this->configHelper->getOrganizationName()) {
            $data['organization']['name'] = $name;
        }

        if ($logoUrl = $this->configHelper->getOrganizationLogo()) {
            $data['organization']['logo'] = $logoUrl;
        }
    }

    protected function addCategoryData(&$data)
    {
        if (!$this->configHelper->forCategoryEnabled()) {
            return;
        }

        /** @var \Magento\Catalog\Model\Category $category */
        $category = $this->coreRegistry->registry('current_category');

        if (!$category)
            return;

        if ('category' != $this->_request->getControllerName())
            return;

        $data['category'] = [
            '@context' => 'http://schema.org',
            '@type' => 'Product',
            'name' => $category->getName(),
            'offers' => [
                '@type' => 'AggregateOffer',
                'priceCurrency' => $this->storeManager->getStore()->getCurrentCurrencyCode(),
                'lowPrice' => $this->categoryHelper->getMinimalPrice($category)
            ]
        ];

        $descriptionMode = $this->configHelper->getCategoryDescription();

        switch ($descriptionMode) {
            case DescriptionSource::CATEGORY_DESCRIPTION:
                if ($description = $this->stripTags($category->getDescription())) {
                    $data['category']['description'] = $description;
                }
                break;
            case DescriptionSource::META_DESCRIPTION:
                $data['category']['description'] = $this->stripTags($this->pageConfig->getDescription());
                break;
        }

        $reviewSummary = $this->categoryHelper->getReviewSummaryInfo($category);

        if ($reviewSummary) {
            $data['category']['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => round(($reviewSummary['rating'] * 5) / 100, 2),
                'reviewCount' => $reviewSummary['reviews']
            ];
        }
    }

    protected function addSearchData(&$data)
    {
        if (!$this->configHelper->forSearchEnabled()) {
            return;
        }
        $this->addWebsiteData($data);
        $data['website']['potentialAction'] = [
            '@type' => 'SearchAction',
            'target' => $this->_urlBuilder->getUrl('catalogsearch/result') . "?q={search_term_string}",
            'query-input' => 'required name=search_term_string'
        ];
    }

    protected function addWebsiteData(&$data)
    {
        if (isset($data['website']))
            return;

        $data['website'] = [
            '@context' => 'http://schema.org',
            '@type' => 'WebSite',
            'url' => $this->_urlBuilder->getBaseUrl()
        ];
    }

    protected function _toHtml()
    {
        $data = $this->prepareData();

        $result = '';
        foreach ($data as $section) {
            $result .= "<script type=\"application/ld+json\">{$this->jsonEncoder->encode($section)}</script>";
        }

        return $result;
    }
}
