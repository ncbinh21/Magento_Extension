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

use Magento\Framework\DataObject;
use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;

interface CollectionInterface
{
    /**
     * @param TableInterface $table
     * @return $this
     */
    public function setBaseTable(TableInterface $table);

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function addColumnToGroup(ColumnInterface $column);

    /**
     * @param ColumnInterface $column
     * @return $this
     */
    public function addColumnToSelect(ColumnInterface $column);

    /**
     * @param ColumnInterface $column
     * @param array           $condition
     * @return $this
     */
    public function addColumnToFilter(ColumnInterface $column, array $condition);

    /**
     * @param ColumnInterface $column
     * @param string          $direction
     * @return $this
     */
    public function addColumnToOrder(ColumnInterface $column, $direction);

    /**
     * @return DataObject
     */
    public function getTotals();

    /**
     * @return int
     */
    public function getSize();
}