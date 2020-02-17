<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\CatalogSearch\Model\Adapter\Mysql\Aggregation;

use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Catalog\Model\Product;

class DataProvider
{
    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\IsNew\Helper
     */
    protected $isNewHelper;

    /**
     * @var \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper
     */
    protected $onSaleHelper;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * AggregationDataProvider constructor.
     * @param ResourceConnection $resource
     * @param ScopeResolverInterface $scopeResolver
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param Product\Visibility $catalogProductVisibility
     * @param \Amasty\Shopby\Model\Layer\Filter\IsNew\Helper $isNewHelper
     * @param \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper $onSaleHelper
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ResourceConnection $resource,
        ScopeResolverInterface $scopeResolver,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Amasty\Shopby\Model\Layer\Filter\IsNew\Helper $isNewHelper,
        \Amasty\Shopby\Model\Layer\Filter\OnSale\Helper $onSaleHelper,
        \Magento\Eav\Model\Config $eavConfig,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->resource = $resource;
        $this->scopeResolver = $scopeResolver;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->isNewHelper = $isNewHelper;
        $this->onSaleHelper = $onSaleHelper;
        $this->scopeConfig = $scopeConfig;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject
     * @param \Closure $proceed
     * @param BucketInterface $bucket
     * @param array $dimensions
     * @param Table $entityIdsTable
     * @return \Magento\Framework\DB\Select|mixed
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundGetDataSet(
        \Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {
        if ($bucket->getField() == 'stock_status') {
            $isStockEnabled = $this->scopeConfig->isSetFlag(
                'amshopby/stock_filter/enabled',
                ScopeInterface::SCOPE_STORE
            );
            if ($isStockEnabled) {
                return $this->addStockAggregation($entityIdsTable);
            }
        }

        if ($bucket->getField() == 'rating_summary') {
            $isRatingEnabled = $this->scopeConfig->isSetFlag(
                'amshopby/rating_filter/enabled',
                ScopeInterface::SCOPE_STORE
            );
            if ($isRatingEnabled) {
                return $this->addRatingAggregation($entityIdsTable, $dimensions);
            }
        }

        if ($bucket->getField() == 'am_is_new') {
            $isNewEnabled = $this->scopeConfig->isSetFlag(
                'amshopby/am_is_new_filter/enabled',
                ScopeInterface::SCOPE_STORE
            );
            if ($isNewEnabled) {
                return $this->addIsNewAggregation($entityIdsTable, $dimensions);
            }
        }

        if ($bucket->getField() == 'am_on_sale') {
            $isOnSaleEnabled = $this->scopeConfig->isSetFlag(
                'amshopby/am_on_sale_filter/enabled',
                ScopeInterface::SCOPE_STORE
            );
            if ($isOnSaleEnabled) {
                return $this->addOnSaleAggregation($entityIdsTable, $dimensions);
            }
        }

        return $proceed($bucket, $dimensions, $entityIdsTable);
    }

    /**
     * @param Table $entityIdsTable
     * @return \Magento\Framework\DB\Select
     */
    protected function addStockAggregation(Table $entityIdsTable)
    {
        $derivedTable = $this->resource->getConnection()->select();
        $derivedTable->from(
            ['main_table' => $this->resource->getTableName('cataloginventory_stock_status')],
            [
                'value' => 'stock_status',
            ]
        )->joinInner(
            ['entities' => $entityIdsTable->getName()],
            'main_table.product_id  = entities.entity_id',
            []
        )->where('main_table.stock_id = 1');

        $select = $this->resource->getConnection()->select();
        $select->from(['main_table' => $derivedTable]);

        return $select;
    }

    /**
     * @param Table $entityIdsTable
     * @param array $dimensions
     * @return \Magento\Framework\DB\Select
     */
    protected function addRatingAggregation(
        Table $entityIdsTable,
        $dimensions
    ) {
        $currentScope = $dimensions['scope']->getValue();
        $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();
        $derivedTable = $this->resource->getConnection()->select();
        $derivedTable->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $columnRating = new \Zend_Db_Expr("
                IF(main_table.rating_summary >=100,
                    5,
                    IF(
                        main_table.rating_summary >=80,
                        4,
                        IF(main_table.rating_summary >=60,
                            3,
                            IF(main_table.rating_summary >=40,
                                2,
                                IF(main_table.rating_summary >=20,
                                    1,
                                    0
                                )
                            )
                        )
                    )
                )
            ");

        $derivedTable->joinLeft(
            ['main_table' => $this->resource->getTableName('review_entity_summary')],
            sprintf(
                '`main_table`.`entity_pk_value`=`entities`.entity_id
                AND `main_table`.entity_type = 1
                AND `main_table`.store_id  =  %d',
                $currentScopeId
            ),
            [
                //'entity_id' => 'entity_pk_value',
                'value' => $columnRating,
            ]
        );
        $select = $this->resource->getConnection()->select();
        $select->from(['main_table' => $derivedTable]);
        return $select;
    }

    /**
     * @param Table $entityIdsTable
     * @param array $dimensions
     * @return \Magento\Framework\DB\Select
     */
    protected function addIsNewAggregation(
        Table $entityIdsTable,
        $dimensions
    ) {
        $currentScope = $dimensions['scope']->getValue();
        $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection->addStoreFilter($currentScopeId);
        $this->isNewHelper->addNewFilter($collection);

        $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns('e.entity_id');

        $derivedTable = $this->resource->getConnection()->select();
        $derivedTable->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            ['am_is_new' => $collection->getSelect()],
            'am_is_new.entity_id  = entities.entity_id',
            [
                'value' => new \Zend_Db_Expr("if(am_is_new.entity_id is null, 0, 1)")
            ]
        );

        $select = $this->resource->getConnection()->select();
        $select->from(['main_table' => $derivedTable]);

        return $select;
    }

    /**
     * @param Table $entityIdsTable
     * @param array $dimensions
     * @return \Magento\Framework\DB\Select
     */
    protected function addOnSaleAggregation(
        Table $entityIdsTable,
        $dimensions
    ) {
        $currentScope = $dimensions['scope']->getValue();
        $currentScopeId = $this->scopeResolver->getScope($currentScope)->getId();

        /** @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection->addStoreFilter($currentScopeId);
        $this->onSaleHelper->addOnSaleFilter($collection);

        $collection->getSelect()->reset(\Zend_Db_Select::COLUMNS);
        $collection->getSelect()->columns('e.entity_id');

        $derivedTable = $this->resource->getConnection()->select();
        $derivedTable->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            ['am_on_sale' => $collection->getSelect()],
            'am_on_sale.entity_id  = entities.entity_id',
            [
                'value' => new \Zend_Db_Expr("if(am_on_sale.entity_id is null, 0, 1)")
            ]
        );

        $derivedTable->group('entities.entity_id');

        $select = $this->resource->getConnection()->select();
        $select->from(['main_table' => $derivedTable]);

        return $select;
    }
}
