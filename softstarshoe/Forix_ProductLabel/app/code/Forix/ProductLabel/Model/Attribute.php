<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Model;

use Forix\ProductLabel\Model\ResourceModel\Rule\Collection;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\Product;
use Forix\ProductLabel\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\Data\CollectionFactory as DataCollectionFactory;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Forix\ProductLabel\Model\Condition\Sql\Builder as SqlConditionBuilder;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Framework\App\ResourceConnection;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Catalog\Model\Indexer\Product\Flat\Processor as IndexerProductFlat;
use Magento\Store\Model\Store;

/**
 * Class Attribute
 *
 * @package Forix\ProductLabel\Model
 */
class Attribute
{

    /**@#%
     * @const
     */
    const LABEL_ATTRIBUTE_CODE = 'forix_label_ids';
    const EDITABLE_BADGE_ATTRIBUTE_CODE = 'forix_editable_badge';

    /**
     * @var Collection
     */
    protected $_labelCollection;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var DataCollectionFactory
     */
    protected $dataCollectionFactory;

    /**
     * @var DateTime
     */
    protected $_coreDate;

    /**
     * @var SqlConditionBuilder
     */
    protected $sqlBuilder;

    /**
     * @var ProductCollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var Product
     */
    protected $_product;

    /**
     * @var Visibility
     */
    protected $catalogProductVisibility;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var int
     */
    protected $attributeId;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var IndexerProductFlat
     */
    protected $indexerProductFlat;

    /**
     * Attribute constructor.
     *
     * @param CollectionFactory $collectionFactory
     * @param DataCollectionFactory $dataCollectionFactory
     * @param DateTime $dateTime
     * @param SqlConditionBuilder $sqlBuilder
     * @param ProductCollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param EavConfig $eavConfig
     * @param IndexerProductFlat $indexerProductFlat
     * @param ResourceConnection $resource
     * @internal param CatalogProductFlatState $catalogProductFlatState
     * @internal param CatalogProductFlatState $state
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        DataCollectionFactory $dataCollectionFactory,
        DateTime $dateTime,
        SqlConditionBuilder $sqlBuilder,
        ProductCollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        EavConfig $eavConfig,
        IndexerProductFlat $indexerProductFlat,
        ResourceConnection $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->_coreDate = $dateTime;
        $this->sqlBuilder = $sqlBuilder;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->eavConfig = $eavConfig;
        $this->resource = $resource;
        $this->indexerProductFlat = $indexerProductFlat;
    }

    /**
     * @param bool $setVisibility
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function getProductCollection($setVisibility = true)
    {
        $productCollection = $this->productCollectionFactory->create();
        if ($setVisibility) {
            $productCollection
                ->setVisibility($this->catalogProductVisibility->getVisibleInSiteIds())
                ->addStoreFilter();
        }
        $productCollection->addAttributeToFilter(self::EDITABLE_BADGE_ATTRIBUTE_CODE, ['neq' => 1]);
        $productCollection->setOrder('entity_id');

        return $productCollection;
    }

    /**
     * @param $object
     * @return $this
     */
    public function setRule($object)
    {
        $this->rule = $object;
        return $this;
    }

    /**
     * @return Rule
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param Product $product
     * @return $this
     */
    public function setProduct($product)
    {
        $this->_product = $product;

        return $this;
    }

    /**
     * @return string
     */
    private function getTableExecute()
    {
        return $this->resource->getTableName('catalog_product_entity_varchar');
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getAttributeId()
    {
        if ($this->attributeId === null) {

            $attributeModel = $this->eavConfig->getAttribute(Product::ENTITY, static::LABEL_ATTRIBUTE_CODE);
            $this->attributeId = (int)$attributeModel->getId();
        }

        return $this->attributeId;
    }

    /**
     * @return $this|mixed|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function checkBadge()
    {
        if (!$this->getRule()->getId() || !$this->getAttributeId() || $this->_product === null) {
            return $this;
        }
        $sqlBuilder = $this->sqlBuilder;
        $productCollection = $this->getProductCollection(false);
        $productCollection->addFieldToFilter('entity_id', $this->_product->getId());
        $conditions = $this->getRule()->getConditions();
        $sqlBuilder->attachConditionToCollection($productCollection, $conditions);

        if ($productCollection->getFirstItem()->getId()) {
            return $this->getRule()->getId();
        }

        return null;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Zend_Db_Select_Exception
     */
    public function saveAttributes()
    {
        if (!$this->getRule()->getId() || !$this->getAttributeId()) {
            return $this;
        }
        $sqlBuilder = $this->sqlBuilder;
        $productCollection = $this->getProductCollection();
        $conditions = $this->getRule()->getConditions();

        $sqlBuilder->attachConditionToCollection($productCollection, $conditions);
        $productCollection->setPageSize(200);
        $totalsPage = $productCollection->getLastPageNumber();
        $currentPage = 1;
        $this->deleteOldValue();
        if ($productCollection->getSize() > 0) {
            do {
                $productCollection->setCurPage($currentPage);
                $productCollection->load();
                $productIds = array_keys($productCollection->toOptionHash());
                $this->updateLabelAttribute($productIds);

                $currentPage++;
                $productCollection->clear();
            } while ($currentPage <= $totalsPage);

            $this->indexerProductFlat->markIndexerAsInvalid();
        }

        return $this;
    }

    /**
     * @param $productIds
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function updateLabelAttribute($productIds)
    {
        $attributeId = (int)$this->getAttributeId();

        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $connection = $this->resource->getConnection();
        $tableName = $this->getTableExecute();
        $select = $connection->select()
            ->from(['cpev' => $tableName], 'entity_id')
            ->where('attribute_id = ?', $attributeId)
            ->where('store_id = ?', Store::DEFAULT_STORE_ID)
            ->where('entity_id IN(?)', $productIds);

        $productIdsOld = $connection->fetchCol($select);
        if (!empty($productIdsOld)) {
            $this->updateValue($productIdsOld);
        }

        $insert = array_diff($productIds, $productIdsOld);
        if (!empty($insert)) {
            $this->addNewValue($insert);
        }

        return $this;
    }

    /**
     * @param array $productIdsOld
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function updateValue($productIdsOld)
    {
        $ruleId = (int)$this->getRule()->getId();
        $attributeId = $this->getAttributeId();
        $connection = $this->resource->getConnection();
        $tableName = $this->getTableExecute();

        $connection->update(
            $tableName,
            [
                'value' => new \Zend_Db_Expr(
                    'CONCAT(value, \',' . $ruleId .'\')'
                ),
            ],
            [
                'attribute_id = ?' => $attributeId,
                'store_id = ?' => Store::DEFAULT_STORE_ID,
                'entity_id IN(?)' => $productIdsOld,
                'value != ?' => ''
            ]
        );

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function deleteOldValue()
    {
        $ruleId = (int)$this->getRule()->getId();
        $attributeId = $this->getAttributeId();
        $connection = $this->resource->getConnection();
        $tableName = $this->getTableExecute();

        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $connection */
        $select = $connection->select()
            ->from(['cpev' => $tableName], 'entity_id')
            ->where('attribute_id = ?', $attributeId)
            ->where('store_id = ?', Store::DEFAULT_STORE_ID)
            ->where('FIND_IN_SET(?, `value`)', $ruleId);
        $productIdsOld = $connection->fetchCol($select);

        if (!empty($productIdsOld)) {
            $connection->update(
                $tableName,
                [
                    'value' => new \Zend_Db_Expr(
                        "TRIM(BOTH ',' FROM(REPLACE(CONCAT(',', `value`, ','), ',{$ruleId},',',')))"
                    ),
                ],
                [
                    'attribute_id = ?' => $attributeId,
                    'store_id = ?' => Store::DEFAULT_STORE_ID,
                    'FIND_IN_SET (?, value)' => $ruleId
                ]
            );
        }
        $connection->delete(
            $tableName,
            [
                'attribute_id = ?' => $attributeId,
                'value IN(?)' => ['', null, ',']
            ]
        );

        return $this;
    }

    /**
     * @param $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteRule($object)
    {
        $this->setRule($object);
        $this->deleteOldValue();

        return $this;
    }

    /**
     * Add new value
     *
     * @param $insert
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function addNewValue($insert)
    {
        $data = [];
        $ruleId = (int)$this->getRule()->getId();
        $attributeId = $this->getAttributeId();
        foreach ($insert as $productId) {
            if((int)$productId > 0) {
                $data[] = [
                    'attribute_id' => $attributeId,
                    'store_id' => Store::DEFAULT_STORE_ID,
                    'entity_id' => (int)$productId,
                    'value'  => $ruleId
                ];
            }
        }
        if (!empty($data)) {
            $this->resource->getConnection()->insertMultiple(
                $this->getTableExecute(),
                $data
            );
        }

        return $this;
    }
}
