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


namespace Mirasvit\Report\Api\Data;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;

interface ReportInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return $this
     */
    public function init();

    /**
     * @return $this
     */
    public function afterInit();

    /**
     * @return $this
     */
    public function reset();

    /**
     * @return CollectionInterface
     */
    public function getCollection();

    /**
     * @param TableInterface|string $table
     * @return $this
     */
    public function setBaseTable($table);

    /**
     * @return TableInterface
     */
    public function getBaseTable();

    /**
     * @return ColumnInterface[]
     */
    public function getFastFilters();

    /**
     * @param ColumnInterface[] $columns
     * @return $this
     */
    public function addFastFilters($columns);

    /**
     * @param ColumnInterface[] $columns
     * @return $this
     */
    public function setFastFilters($columns);

    /**
     * @param ColumnInterface[] $columns
     * @return $this
     */
    public function addAvailableFilters($columns);

    /**
     * @return ColumnInterface[]
     */
    public function getAvailableFilters();

    /**
     * @return ColumnInterface[]
     */
    public function getDefaultColumns();

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function addDefaultColumns($columns);

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function setDefaultColumns($columns);

    /**
     * @return ColumnInterface[]
     */
    public function getAvailableColumns();

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function addAvailableColumns($columns);

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function setAvailableColumns($columns);

    /**
     * @return ColumnInterface
     */
    public function getDefaultDimension();

    /**
     * @param ColumnInterface|string $column
     * @return $this
     */
    public function setDefaultDimension($column);

    /**
     * @return ColumnInterface[]
     */
    public function getAvailableDimensions();

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function addAvailableDimensions($columns);

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function setAvailableDimensions($columns);

    /**
     * @return $this
     */
    public function resetDimensions();

    /**
     * @return ColumnInterface[]
     */
    public function getRequiredColumns();

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function addRequiredColumns($columns);

    /**
     * @param ColumnInterface[]|string[] $columns
     * @return $this
     */
    public function setRequiredColumns($columns);

    /**
     * @return array
     */
    public function getDefaultFilters();

    /**
     * @param array $filters
     * @return $this
     */
    public function setDefaultFilters(array $filters);

    /**
     * @param string|ColumnInterface $column
     * @param string|int $value
     * @param array $row
     * @return string
     */
    public function prepareValue($column, $value, $row);
}