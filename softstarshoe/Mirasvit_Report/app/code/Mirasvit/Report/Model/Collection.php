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


namespace Mirasvit\Report\Model;

use Magento\Framework\DataObject;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Api\Data\CollectionInterface;
use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Report\Api\Service\SelectServiceInterface;
use Mirasvit\Report\Model\Query\Select;
use Mirasvit\Report\Model\Query\SelectFactory;

class Collection extends \Magento\Framework\Data\Collection implements CollectionInterface
{
    /**
     * @var Select
     */
    private $select;

    /**
     * @var SelectServiceInterface
     */
    private $selectService;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * @var MapRepositoryInterface
     */
    protected $mapRepository;

    public function __construct(
        SelectFactory $selectFactory,
        SelectServiceInterface $selectService,
        ResourceConnection $resource,
        MapRepositoryInterface $mapRepository
    ) {
        $this->resource = $resource;
        $this->selectService = $selectService;
        $this->select = $selectFactory->create();
        $this->mapRepository = $mapRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTable(TableInterface $table)
    {
        $this->select->setBaseTable($table);

        $this->connection = $this->resource->getConnection($table->getConnectionName());

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumnToGroup(ColumnInterface $column)
    {
        $this->select->addColumnToGroup($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setColumnToGroup(ColumnInterface $column)
    {
        $this->select->setColumnToGroup($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumnToSelect(ColumnInterface $column)
    {
        $this->select->addColumnToSelect($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPkColumnToSelect(ColumnInterface $column, $param = null)
    {
        $this->select->addPkColumnToSelect($column, $param);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addPkColumnToFilter(ColumnInterface $column, array $condition, $param)
    {
        $this->select->addPkColumnToFilter($column, $condition, $param);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumnToFilter(ColumnInterface $column, array $condition)
    {
        $this->select->addColumnToFilter($column, $condition);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addColumnToOrder(ColumnInterface $column, $direction)
    {
        $this->select->addColumnToOrder($column, $direction);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurPage($page)
    {
        $this->select->setLimit($page, $this->_pageSize);

        return parent::setCurPage($page);
    }

    /**
     * {@inheritdoc}
     */
    public function setPageSize($size)
    {
        $this->select->setLimit($this->_curPage, $size);

        return parent::setPageSize($size);
    }

    /**
     * {@inheritdoc}
     */
    public function loadData($printQuery = false, $logQuery = false)
    {
        $this->selectService->applyTimeZone($this->connection);

        $rows = $this->connection->fetchAll($this->select);

        foreach ($rows as $row) {
            $this->addItem(new DataObject($row));
        }

        $this->selectService->restoreTimeZone($this->connection);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->select->__toString();
    }

    /**
     * {@inheritdoc}
     */
    public function __clone()
    {
        $this->select = clone $this->select;
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        $countSelect = clone $this->select;
        $countSelect->reset(\Zend_Db_Select::ORDER)
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET)
            ->reset(\Zend_Db_Select::COLUMNS);

        $countSelect->columns();

        $select = 'SELECT COUNT(*) FROM (' . $countSelect->__toString() . ') as cnt';

        $result = $this->connection->fetchOne($select);

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        $select = clone $this->select;
        $select->reset(\Zend_Db_Select::ORDER)
            ->reset(\Zend_Db_Select::LIMIT_COUNT)
            ->reset(\Zend_Db_Select::LIMIT_OFFSET);

        $result = new DataObject();

        $this->selectService->applyTimeZone($this->connection);
        $rows = $this->connection->fetchAll($select);
        $this->selectService->restoreTimeZone($this->connection);

        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if (!isset($result[$k])) {
                    $result[$k] = null;
                }

                //float need for PHP 7.1.9 compatibility
                $result[$k] += (float)$v;
                $result[$k] = round($result[$k], 2);
            }
        }

        $columnNames = array_keys($result->getData());
        foreach ($columnNames as $columnName) {
            if (count(explode('|', $columnName)) === 2) {
                if (!in_array($this->mapRepository->getColumn($columnName)->getDataType(), ['number', 'price'])) {
                    $result[$columnName] = '';
                }
            }
        }

        return $result;
    }
}