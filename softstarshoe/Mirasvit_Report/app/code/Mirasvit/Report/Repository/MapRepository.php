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


namespace Mirasvit\Report\Repository;

use Mirasvit\Report\Api\Data\Query\RelationInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Api\Data\Query\TableInterfaceFactory;
use Mirasvit\Report\Api\Data\Query\EavTableInterfaceFactory;
use Mirasvit\Report\Api\Data\Query\ColumnInterfaceFactory;
use Mirasvit\Report\Api\Data\Query\RelationInterfaceFactory;
use Mirasvit\Report\Api\Data\Query\FieldInterfaceFactory;
use Mirasvit\Report\Api\Data\Query\EavFieldInterfaceFactory;
use Mirasvit\Report\Model\Config\MapFactory;
use Magento\Framework\ObjectManagerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MapRepository implements MapRepositoryInterface
{
    /**
     * @var MapFactory
     */
    protected $mapFactory;

    /**
     * @var TableInterfaceFactory
     */
    protected $tableFactory;

    /**
     * @var EavTableInterfaceFactory
     */
    protected $eavTableFactory;

    /**
     * @var ColumnInterfaceFactory
     */
    protected $columnFactory;

    /**
     * @var RelationInterfaceFactory
     */
    protected $relationFactory;

    /**
     * @var FieldInterfaceFactory
     */
    protected $fieldFactory;

    /**
     * @var EavFieldInterfaceFactory
     */
    protected $eavFieldFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var TableInterface[]
     */
    protected $tablePool = [];

    /**
     * @var RelationInterface[]
     */
    protected $relationPool = [];

    public function __construct(
        MapFactory $mapFactory,
        TableInterfaceFactory $tableFactory,
        EavTableInterfaceFactory $eavTableFactory,
        ColumnInterfaceFactory $columnFactory,
        RelationInterfaceFactory $relationFactory,
        FieldInterfaceFactory $fieldFactory,
        EavFieldInterfaceFactory $eavFieldFactory,
        ObjectManagerInterface $objectManager
    ) {
        $this->mapFactory = $mapFactory;
        $this->tableFactory = $tableFactory;
        $this->eavTableFactory = $eavTableFactory;
        $this->columnFactory = $columnFactory;
        $this->relationFactory = $relationFactory;
        $this->fieldFactory = $fieldFactory;
        $this->eavFieldFactory = $eavFieldFactory;
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getTables()
    {
        if (!$this->tablePool) {
            $this->mapFactory->create()->load();
        }

        return $this->tablePool;
    }

    /**
     * {@inheritdoc}
     */
    public function getTable($name)
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        if (!key_exists($name, $this->getTables())) {
            throw new \Exception(__("Table '%1' is not defined.", $name));
        }

        $table = $this->getTables()[$name];

        \Magento\Framework\Profiler::stop(__METHOD__);

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($name)
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        if (count(explode('|', $name)) != 2) {
            throw new \Exception(__("Malformed column name '%1'.", $name));
        }

        $table = explode('|', $name)[0];

        $column = $this->getTable($table)->getColumn($name);

        \Magento\Framework\Profiler::stop(__METHOD__);

        return $column;
    }

    /**
     * {@inheritdoc}
     */
    public function getRelations()
    {
        return $this->relationPool;
    }

    /**
     * {@inheritdoc}
     */
    public function createTable(array $data)
    {
        $table = $this->tableFactory->create($data);

        $this->tablePool[$table->getName()] = $table;

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function createEavTable(array $data)
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        $table = $this->eavTableFactory->create($data);

        $this->tablePool[$table->getName()] = $table;

        \Magento\Framework\Profiler::stop(__METHOD__);

        return $table;
    }

    /**
     * {@inheritdoc}
     */
    public function createColumn(array $data, $class = '')
    {
        if ($class) {
            return $this->objectManager->create($class, $data);
        }

        return $this->columnFactory->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createRelation(array $data)
    {
        $relation = $this->relationFactory->create($data);
        $this->relationPool[] = $relation;

        return $relation;
    }

    /**
     * {@inheritdoc}
     */
    public function createField(array $data)
    {
        return $this->fieldFactory->create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function createEavField(array $data)
    {
        return $this->eavFieldFactory->create($data);
    }
}