<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Index;

use Magento\CatalogSearch\Model\Search\IndexBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;

class SearchIndexBuilder
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    protected $shopbyRequest;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper
     */
    protected $onSaleHelper;

    /**
     * @var \Amasty\Shopby\Model\Layer\Cms\Manager
     */
    protected $cmsManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        ResourceConnection $resource,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Amasty\Shopby\Model\Layer\Filter\IsNew\Helper $isNewHelper,
        \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper $onSaleHelper,
        \Amasty\Shopby\Model\Layer\Cms\Manager $cmsManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->shopbyRequest = $shopbyRequest;
        $this->resource = $resource;

        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->onSaleHelper = $onSaleHelper;
        $this->cmsManager = $cmsManager;
    }

    /**
     * @param IndexBuilder $subject
     * @param Select $result
     * @return Select mixed
     */
    public function afterBuild(\Magento\CatalogSearch\Model\Search\IndexBuilder $subject, $result)
    {
        if ($this->isEnabledShowOutOfStock() && $this->isEnabledStockFilter()) {
            if ($this->shopbyRequest->getParam('stock')) {
                $this->addStockDataToSelect($result);
            }
        }

        if ($this->isEnabledRatingFilter()) {
            $this->addRatingDataToSelect($result);
        }

        if ($this->cmsManager->isCmsPageNavigation()) {
            $this->cmsManager->addCmsPageDataToSelect($result);
        }

        return $result;
    }

    protected function addStockDataToSelect(Select $select)
    {
        $connection = $select->getConnection();

        $select->joinLeft(
            ['stock_status_filter' => $this->resource->getTableName('cataloginventory_stock_status')],
            'search_index.entity_id = stock_status_filter.product_id'
            . $connection->quoteInto(
                ' AND stock_status_filter.website_id IN (?, 0)',
                $this->storeManager->getWebsite()->getId()
            ),
            []
        );
    }

    protected function addRatingDataToSelect(Select $select)
    {
        $select->joinLeft(
            ['rating_summary_filter' => $this->resource->getTableName('review_entity_summary')],
            sprintf(
                '`rating_summary_filter`.`entity_pk_value`=`search_index`.entity_id
                AND `rating_summary_filter`.entity_type = 1
                AND `rating_summary_filter`.store_id  =  %d',
                $this->storeManager->getStore()->getId()
            ),
            []
        );
    }

    protected function isEnabledShowOutOfStock()
    {
        return $this->scopeConfig->isSetFlag(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    protected function isEnabledStockFilter()
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby/stock_filter/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    protected function isEnabledRatingFilter()
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby/rating_filter/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    protected function isEnabledIsNewFilter()
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby/is_new_filter/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    protected function isEnabledOnSaleFilter()
    {
        return $this->scopeConfig->isSetFlag(
            'amshopby/on_sale_filter/enabled',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
