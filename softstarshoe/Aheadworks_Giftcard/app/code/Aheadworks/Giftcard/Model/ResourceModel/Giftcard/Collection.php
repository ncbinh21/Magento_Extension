<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Giftcard;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Giftcard\Model\Giftcard;
use Aheadworks\Giftcard\Model\ResourceModel\Giftcard as ResourceGiftcard;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Giftcard
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param MetadataPool $metadataPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param StoreManagerInterface $storeManager
     * @param AdapterInterface $connection
     * @param AbstractDb $resource
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        AttributeRepositoryInterface $attributeRepository,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->metadataPool = $metadataPool;
        $this->attributeRepository = $attributeRepository;
        $this->storeManager = $storeManager;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Giftcard::class, ResourceGiftcard::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFilterToMap('created_at', 'main_table.created_at');
        $this->addFilterToMap('state', 'main_table.state');
        $this->addFilterToMap('email_sent', 'main_table.email_sent');

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'order_increment_id' || $field == 'product_name') {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add not apply Gift Card codes in quote filter to collection
     *
     * @param int $quoteId
     * @return $this
     */
    public function addNotApplyInQuoteFilter($quoteId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getTable('aw_giftcard_quote'), 'giftcard_id')
            ->where('quote_id = ?', $quoteId);

        $giftcardIds = $connection->fetchCol($select);
        if (!empty($giftcardIds)) {
            $this->addFieldToFilter('id', ['nin' => $giftcardIds]);
        }
        return $this;
    }

    /**
     * Add expired filter to collection
     *
     * @param string $expiredDate
     * @return $this
     */
    public function addExpiredFilter($expiredDate)
    {
        $this
            ->addFieldToFilter('expire_at', ['notnull' => true])
            ->addFieldToFilter(new \Zend_Db_Expr('DATE(expire_at)'), ['lt' => $expiredDate]);
        return $this;
    }

    /**
     * Add check delivery date filter to collection
     *
     * @param string $deliveryDate
     * @return $this
     */
    public function addCheckDeliveryDateFilter($deliveryDate)
    {
        $this
            ->getSelect()
            ->where('(`main_table`.`delivery_date` IS NULL OR `main_table`.`delivery_date` <= ?)', $deliveryDate);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            'sales_order',
            'order_id',
            'entity_id',
            'increment_id',
            'order_increment_id'
        );
        $this->attachRelationTable(
            $this->getProductNameQuery(),
            'product_id',
            $this->getCatalogLinkField(),
            'name',
            'product_name'
        );
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            'sales_order',
            'order_id',
            'entity_id',
            'order_increment_id',
            'increment_id'
        );
        $this->joinLinkageTable(
            'product_name',
            'product_id',
            $this->getCatalogLinkField(),
            'product_name',
            'name',
            $this->getProductNameQuery()
        );
        parent::_renderFiltersBefore();
    }

    /**
     * Attach entity table data to collection items
     *
     * @param string|Select $table
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnNameRelationTable
     * @param string $fieldName
     * @return void
     */
    private function attachRelationTable(
        $table,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            if ($table instanceof Select) {
                $select = $table;
            } else {
                $select = $connection->select()->from(['tmp_table' => $this->getTable($table)]);
            }
            $select->where('tmp_table.' . $linkageColumnName . ' IN (?)', $ids);

            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = '';
                $id = $item->getData($columnName);
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        $result = $data[$columnNameRelationTable];
                    }
                }
                $item->setData($fieldName, $result);
            }
        }
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @param Select|null $subQuery
     * @return void
     */
    private function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName,
        $subQuery = null
    ) {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $tableName . '_table';
            $table = $subQuery
                ? new \Zend_Db_Expr('(' . $subQuery . ')')
                : $this->getTable($tableName);
            $this->getSelect()->joinLeft(
                [$linkageTableName => $table],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );
            $this->addFilterToMap($columnFilter, $linkageTableName . '.' . $fieldName);
        }
    }

    /**
     * Retrieve catalog link field
     *
     * @return string
     */
    private function getCatalogLinkField()
    {
        return $this->metadataPool->getMetadata(CategoryInterface::class)->getLinkField();
    }

    /**
     * Retrieve product name query
     *
     * @return Select
     */
    private function getProductNameQuery()
    {
        /* @var $attributeProductName \Magento\Catalog\Model\ResourceModel\Eav\Attribute */
        $attributeProductName = $this->attributeRepository->get('catalog_product', 'name');
        $catalogLinkField = $this->getCatalogLinkField();
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable('catalog_product_entity')],
                [
                    'tmp_table.' . $catalogLinkField,
                    'IF(at_name.value_id > 0, at_name.value, at_name_default.value) AS name'
                ]
            )
            ->join(
                ['at_name_default' => $this->getTable('catalog_product_entity_varchar')],
                'at_name_default.' . $catalogLinkField . ' = tmp_table.' . $catalogLinkField . ''
                . ' AND at_name_default.attribute_id = ' . $attributeProductName->getId()
                . ' AND at_name_default.store_id = 0',
                []
            )->joinLeft(
                ['at_name' => $this->getTable('catalog_product_entity_varchar')],
                'at_name.' . $catalogLinkField . ' = tmp_table.' . $catalogLinkField . ''
                . ' AND at_name.attribute_id = ' . $attributeProductName->getId()
                . ' AND at_name.store_id = ' . $this->storeManager->getDefaultStoreView()->getId(),
                []
            );
        return $select;
    }
}
