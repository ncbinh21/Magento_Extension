<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\ResourceModel\Pool\Grid;

use Magento\Framework\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Aheadworks\Giftcard\Model\ResourceModel\Pool\Collection as PoolCollection;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Select;
use Aheadworks\Giftcard\Model\Source\YesNo;

/**
 * Class Collection
 *
 * @package Aheadworks\Giftcard\Model\ResourceModel\Pool\Grid
 */
class Collection extends PoolCollection implements SearchResultInterface
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param mixed|null $mainTable
     * @param AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param mixed $resourceModel
     * @param string $model
     * @param AdapterInterface|null $connection
     * @param AbstractDb $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = Document::class,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'number_codes' || $field == 'codes_left') {
            $this->addFilter($field, $condition, 'public');
            return $this;
        }
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if ($field == 'number_codes') {
            $this->joinLinkageTable(
                $this->getNumberCodesQuery(),
                'id',
                'pool_id',
                'number_codes',
                'number_codes'
            );
        }
        if ($field == 'codes_left') {
            $this->joinLinkageTable(
                $this->getCodesLeftQuery(),
                'id',
                'pool_id',
                'codes_left',
                'codes_left'
            );
        }
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            $this->getNumberCodesQuery(),
            'id',
            'pool_id',
            'number_codes',
            'number_codes'
        );
        $this->attachRelationTable(
            $this->getCodesLeftQuery(),
            'id',
            'pool_id',
            'codes_left',
            'codes_left'
        );
        return parent::_afterLoad();
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter('number_codes')) {
            $this->joinLinkageTable(
                $this->getNumberCodesQuery(),
                'id',
                'pool_id',
                'number_codes',
                'number_codes'
            );
        }
        if ($this->getFilter('codes_left')) {
            $this->joinLinkageTable(
                $this->getCodesLeftQuery(),
                'id',
                'pool_id',
                'codes_left',
                'codes_left'
            );
        }
        parent::_renderFiltersBefore();
    }

    /**
     * Retrieve number codes query
     *
     * @return Select
     */
    private function getNumberCodesQuery()
    {
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable('aw_giftcard_pool_code')],
                ['number_codes' => new \Zend_Db_Expr('COUNT(*)'), 'pool_id']
            )->group('pool_id');

        return $select;
    }

    /**
     * Retrieve number codes query
     *
     * @return Select
     */
    private function getCodesLeftQuery()
    {
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable('aw_giftcard_pool_code')],
                ['codes_left' => new \Zend_Db_Expr('COUNT(*)'), 'pool_id']
            )->where('used = ?', YesNo::NO)
            ->group('pool_id');

        return $select;
    }

    /**
     * Attach entity table data to collection items
     *
     * @param string|Select $table
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnNameRelationTable
     * @param string $fieldName
     * @return $this
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
            $select = $table instanceof Select
                ? $table
                : $connection->select()->from(['tmp_table' => $this->getTable($table)]);

            $select->where('tmp_table.' . $linkageColumnName . ' IN (?)', $ids);

            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = 0;
                $id = $item->getData($columnName);
                foreach ($connection->fetchAll($select) as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        $result = $data[$columnNameRelationTable];
                    }
                }
                $item->setData($fieldName, $result);
            }
        }
        return $this;
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string|Select $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @return $this
     */
    private function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName
    ) {
        $linkageTableName = $columnFilter . '_at';
        if (!in_array($linkageTableName, $this->linkageTableNames)) {
            $this->linkageTableNames[] = $linkageTableName;
            $table = $tableName instanceof Select
                ? new \Zend_Db_Expr('(' . $tableName . ')')
                : $this->getTable($tableName);

            $this->getSelect()->joinLeft(
                [$linkageTableName => $table],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );
        }
        $this->addFilterToMap($columnFilter, $linkageTableName . '.' . $fieldName);
        return $this;
    }
}
