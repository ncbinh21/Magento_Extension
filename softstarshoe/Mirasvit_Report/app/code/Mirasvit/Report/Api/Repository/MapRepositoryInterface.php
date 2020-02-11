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


namespace Mirasvit\Report\Api\Repository;

use Mirasvit\Report\Api\Data\Query\EavFieldInterface;
use Mirasvit\Report\Api\Data\Query\FieldInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Api\Data\Query\EavTableInterface;
use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\RelationInterface;

interface MapRepositoryInterface
{
    /**
     * @return TableInterface[]
     */
    public function getTables();

    /**
     * @param string $name
     * @return TableInterface
     */
    public function getTable($name);

    /**
     * @param string $name
     * @return ColumnInterface
     */
    public function getColumn($name);

    /**
     * @return RelationInterface[]
     */
    public function getRelations();

    /**
     * @param array $data
     * @return TableInterface
     */
    public function createTable(array $data);

    /**
     * @param array $data
     * @return EavTableInterface
     */
    public function createEavTable(array $data);

    /**
     * @param array  $data
     * @param string $class
     * @return ColumnInterface
     */
    public function createColumn(array $data, $class = '');

    /**
     * @param array $data
     * @return RelationInterface
     */
    public function createRelation(array $data);

    /**
     * @param array $data
     * @return FieldInterface
     */
    public function createField(array $data);

    /**
     * @param array $data
     * @return EavFieldInterface
     */
    public function createEavField(array $data);
}