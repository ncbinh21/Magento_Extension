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
 * @package   mirasvit/module-report
 * @version   1.2.27
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Report\Model\Query;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Report\Api\Data\Query\SelectInterface;
use Mirasvit\Report\Api\Data\Query\RelationInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Magento\Framework\DB\Select\SelectRenderer;
use Mirasvit\Report\Api\Service\SelectServiceInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Select extends \Magento\Framework\DB\Select implements SelectInterface
{
    /**
     * @var ColumnInterface[]
     */
    private $usedColumnsPool = [];

    /**
     * @var string[]
     */
    private $joinedTablesPool = [];

    /**
     * @var \Magento\Framework\Module\Resource
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var ColumnInterface[]
     */
    private $filteredColumns = [];

    /**
     * @var Table
     */
    private $baseTable;

    /**
     * @var MapRepositoryInterface
     */
    private $mapRepository;

    /**
     * @var SelectServiceInterface
     */
    private $selectService;

    public function __construct(
        ResourceConnection $resource,
        MapRepositoryInterface $mapRepository,
        SelectServiceInterface $selectService,
        SelectRenderer $selectRenderer
    ) {
        $this->mapRepository = $mapRepository;
        $this->selectService = $selectService;
        $this->resource = $resource;

        /** @var \Magento\Framework\DB\Adapter\Pdo\Mysql $adapter */
        $adapter = $resource->getConnection();

        parent::__construct($adapter, $selectRenderer);
    }

    /**
     * @param TableInterface $table
     * @return $this
     */
    public function setBaseTable($table)
    {
        $this->baseTable = $table;
        $this->connection = $this->resource->getConnection($this->baseTable->getConnectionName());

        $this->joinedTablesPool[] = $table->getName();

        $this->from(
            [$table->getName() => $this->resource->getTableName($table->getName())],
            [new \Zend_Db_Expr('1')]
        );

        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function addColumnToSelect(ColumnInterface $column)
    {
        $column->join($this);

        foreach ($column->getFields() as $field) {
            $field->join($this);
        }

        $this->columns([
            $column->getName() => $column->toDbExpr(),
        ]);


        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @param string $param
     * @return $this
     */
    public function addPkColumnToSelect(ColumnInterface $column, $param = null)
    {
        foreach ($column->getFields() as $field) {
            $field->join($this);
        }

        $this->columns([
            'pk' => $column->toDbExpr($param),
        ]);


        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function addColumnToGroup(ColumnInterface $column)
    {
        $this->usedColumnsPool[] = $column;

        $this->group($column->toDbExpr());

        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function setColumnToGroup(ColumnInterface $column)
    {
        $this->usedColumnsPool[] = $column;

        $this->reset(\Zend_Db_Select::GROUP);
        $this->group($column->toDbExpr());

        return $this;
    }

    /**
     * @return ColumnInterface[]
     */
    public function getFilteredColumns()
    {
        return $this->filteredColumns;
    }

    /**
     * @param ColumnInterface $column
     * @param integer|string|array $condition
     * @return $this
     */
    public function addColumnToFilter(ColumnInterface $column, $condition)
    {
        $this->filteredColumns[] = $column;
        $this->usedColumnsPool[] = $column;

        $conditionSql = $this->connection->prepareSqlCondition($column->toDbExpr(), $condition);

        if (strpos($conditionSql, 'COUNT(') !== false
            || strpos($conditionSql, 'AVG(') !== false
            || strpos($conditionSql, 'SUM(') !== false
            || strpos($conditionSql, 'CONCAT(') !== false
            || strpos($conditionSql, 'MIN(') !== false
            || strpos($conditionSql, 'MAX(') !== false
        ) {
            $this->having($conditionSql);
        } elseif ($condition) {
            $this->where($conditionSql);
        }

        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @param integer|string|array $condition
     * @param string $param
     * @return $this
     */
    public function addPkColumnToFilter(ColumnInterface $column, $condition, $param)
    {
        $this->filteredColumns[] = $column;
        $this->usedColumnsPool[] = $column;

        $conditionSql = $this->connection->prepareSqlCondition($column->toDbExpr($param), $condition);

        $this->where($conditionSql);

        return $this;
    }

    /**
     * @param ColumnInterface $column
     * @param string $direction
     * @return $this
     */
    public function addColumnToOrder(ColumnInterface $column, $direction)
    {
        $this->usedColumnsPool[] = $column;

        $this->order(new \Zend_Db_Expr($column->toDbExpr() . ' ' . $direction));

        return $this;
    }

    /**
     * @param int $page
     * @param int $size
     * @return $this
     */
    public function setLimit($page, $size)
    {
        $this->limit = $size;
        $this->limitPage($page, $size);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function assemble()
    {
        foreach ($this->usedColumnsPool as $column) {
            foreach ($column->getFields() as $field) {
                $field->join($this);
            }
        }

        return parent::assemble();
    }

    /**
     * @param TableInterface $table
     * @return bool
     */
    public function joinTable($table)
    {
        if (in_array($table->getName(), $this->joinedTablesPool)) {
            return true;
        }

        $relations = $this->joinWay($table);
        $relations = array_reverse($relations);

        /** @var Relation $relation */
        foreach ($relations as $relation) {
            if (!in_array($relation->getRightTable()->getName(), $this->joinedTablesPool)) {
                $this->doJoinTable($relation->getRightTable(), $relation);
            }

            if (!in_array($relation->getLeftTable()->getName(), $this->joinedTablesPool)) {
                $this->doJoinTable($relation->getLeftTable(), $relation);
            }
        }

        if ($relations) {
            return true;
        }

        return false;
    }

    /**
     * Join $tbl to current select based on relation condition.
     *
     * @param TableInterface $tbl
     * @param RelationInterface $relation
     *
     * @return $this
     */
    private function doJoinTable(TableInterface $tbl, RelationInterface $relation)
    {
        $this->selectService->replicateTable($tbl, $this->baseTable);

        $this->joinLeft(
            [$tbl->getName() => $this->resource->getTableName($tbl->getName())],
            $relation->getCondition(),
            []
        );

        return $this;
    }

    public function joinLeft($name, $cond, $cols = '*', $schema = null)
    {
        $n = implode('', array_merge(array_keys($name), array_values($name)));

        if (!in_array($n, $this->joinedTablesPool)) {
            $this->joinedTablesPool[] = $n;

            return parent::joinLeft($name, $cond, $cols, $schema);
        }

        return $this;
    }

    /**
     * @param TableInterface $table
     * @param RelationInterface[] $relations
     * @return RelationInterface[]
     */
    protected function joinWay($table, $relations = [])
    {
        if (in_array($table->getName(), $this->joinedTablesPool)) {
            return $relations;
        }


        // check direct relation
        foreach ($this->mapRepository->getRelations() as $relation) {
            if (in_array($relation, $relations)) {
                continue;
            }

            // Direct relation
            if ($relation->getLeftTable() === $table
                && in_array($relation->getRightTable()->getName(), $this->joinedTablesPool)) {
                return array_merge($relations, [$relation]);
            }

            // Direct relation
            if ($relation->getRightTable() === $table
                && in_array($relation->getLeftTable()->getName(), $this->joinedTablesPool)) {
                return array_merge($relations, [$relation]);
            }

        }

        foreach ($this->mapRepository->getRelations() as $relation) {
            if (in_array($relation, $relations)) {
                continue;
            }

            if ($relation->getLeftTable() === $table) {
                if ($result = $this->joinWay($relation->getRightTable(), array_merge($relations, [$relation]))) {
                    return $result;
                }
            }

            if ($relation->getRightTable() === $table) {
                if ($result = $this->joinWay($relation->getLeftTable(), array_merge($relations, [$relation]))) {
                    return $result;
                }
            }
        }

        return [];
    }
}
