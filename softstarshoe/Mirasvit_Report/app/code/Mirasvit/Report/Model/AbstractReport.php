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
use Mirasvit\Report\Api\Data\CollectionInterface;
use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Api\Data\Query\TableInterface;
use Mirasvit\Report\Api\Data\ReportInterface;
use Mirasvit\Report\Ui\Context as UiContext;

abstract class AbstractReport implements ReportInterface
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var UiContext
     */
    private $uiContext;

    /**
     * @var TableInterface
     */
    private $baseTable;

    /**
     * @var ColumnInterface[]
     */
    private $fastFilters = [];

    /**
     * @var ColumnInterface[]
     */
    private $requiredColumns = [];

    /**
     * @var ColumnInterface[]
     */
    private $defaultColumns = [];

    /**
     * @var ColumnInterface[]
     */
    private $availableColumns = [];

    /**
     * @var ColumnInterface[]
     */
    private $availableFilters = [];

    /**
     * @var ColumnInterface
     */
    private $defaultDimension = null;

    /**
     * @var ColumnInterface[]
     */
    private $availableDimensions = [];

    /**
     * @var array
     */
    private $defaultFilters = [];

    /**
     * @var array
     */
    private $gridConfig = [];

    /**
     * @var array
     */
    private $chartConfig = [];

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        $code = str_replace('Mirasvit\Reports\Reports\\', '', get_class($this));

        return strtolower(str_replace(['\Interceptor', '\\'], ['', '_'], $code));
    }

    /**
     * @return $this
     */
    public function afterInit()
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        $collection = $this->context->getCollection();

        $collection->setBaseTable($this->baseTable);

        foreach ($this->getRequiredColumns() as $column) {
            $collection->addColumnToSelect($column);
        }

        $collection->addColumnToGroup($this->getDefaultDimension());

        foreach ($this->defaultFilters as $filter) {
            $column = $this->context->getMapRepository()->getColumn($filter[0]);

            $collection->addColumnToFilter(
                $column,
                $filter[1]
            );
        }
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function reset()
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        $this->context->initCollection();
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $this;
    }

    /**
     * @param \Mirasvit\Report\Ui\Context $context
     * @return $this
     */
    public function setUiContext($context)
    {
        $this->uiContext = $context;

        return $this;
    }

    /**
     * @return UiContext
     */
    public function getUiContext()
    {
        return $this->uiContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseTable()
    {
        return $this->baseTable;
    }

    /**
     * {@inheritdoc}
     */
    public function setBaseTable($table)
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        if (!$table instanceof TableInterface) {
            $table = $this->context->getMapRepository()->getTable($table);
        }

        $this->baseTable = $table;
        \Magento\Framework\Profiler::stop(__METHOD__);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFastFilters()
    {
        return $this->fastFilters;
    }

    /**
     * {@inheritdoc}
     */
    public function addFastFilters($columns)
    {
        $this->fastFilters = array_merge_recursive(
            $this->fastFilters,
            $this->toColumns($columns)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setFastFilters($columns)
    {
        $this->fastFilters = $this->toColumns($columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultColumns()
    {
        return $this->defaultColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function addDefaultColumns($columns)
    {
        $this->defaultColumns = array_merge_recursive(
            $this->defaultColumns,
            $this->toColumns($columns)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultColumns($columns)
    {
        $this->defaultColumns = $this->toColumns($columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableColumns()
    {
        return $this->availableColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function addAvailableColumns($columns)
    {
        $this->availableColumns = array_merge_recursive(
            $this->availableColumns,
            $this->toColumns($columns)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableColumns($columns)
    {
        $this->availableColumns = $this->toColumns($columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultDimension()
    {
        return $this->defaultDimension;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultDimension($column)
    {
        $this->defaultDimension = $this->toColumn($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableDimensions()
    {
        return $this->availableDimensions;
    }

    /**
     * {@inheritdoc}
     */
    public function addAvailableDimensions($columns)
    {
        $this->availableDimensions = array_merge_recursive(
            $this->availableDimensions,
            $this->toColumns($columns)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setAvailableDimensions($columns)
    {
        $this->availableDimensions = $this->toColumns($columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function resetDimensions()
    {
        $this->availableDimensions = [];
        $this->defaultDimension = null;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addAvailableFilters($columns)
    {
        $this->availableFilters = array_merge_recursive(
            $this->availableFilters,
            $this->toColumns($columns)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableFilters()
    {
        return $this->availableFilters;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredColumns()
    {
        return $this->requiredColumns;
    }

    /**
     * {@inheritdoc}
     */
    public function addRequiredColumns($columns)
    {
        $this->requiredColumns = array_merge_recursive(
            $this->requiredColumns,
            $this->toColumns($columns)
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequiredColumns($columns)
    {
        $this->requiredColumns = $this->toColumns($columns);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultFilters()
    {
        return $this->defaultFilters;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultFilters(array $filters)
    {
        $this->defaultFilters = $filters;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setGridConfig($config)
    {
        $this->gridConfig = $config;

        return $this;
    }

    /**
     * @param string $key
     * @return DataObject|string
     */
    public function getGridConfig($key = null)
    {
        $conf = new DataObject($this->gridConfig);

        if ($key) {
            return $conf->getData($key);
        }

        return $conf;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function setChartConfig($config)
    {
        $this->chartConfig = $config;

        return $this;
    }

    /**
     * @return DataObject
     */
    public function getChartConfig()
    {
        return new DataObject($this->chartConfig);
    }
    //
    /**
     * {@inheritdoc}
     */
    public function getActions($item)
    {
        return [];
    }

    /**
     * @param string[]|ColumnInterface[] $columns
     * @return ColumnInterface[]
     */
    private function toColumns($columns)
    {
        $result = [];

        if (!is_array($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $column) {
            $column = $this->toColumn($column);
            $result[] = $column;
        }

        return $result;
    }

    /**
     * @param ColumnInterface|string $column
     * @return ColumnInterface
     */
    private function toColumn($column)
    {
        if (!$column instanceof ColumnInterface) {
            $column = $this->context->getMapRepository()->getColumn($column);
        }

        return $column;
    }

    /**
     * @return CollectionInterface
     */
    public function getCollection()
    {
        $collection = $this->context->getCollection();

        return $collection;
    }

    /**
     * @return bool
     */
    public function hasActions()
    {
        $reflector = new \ReflectionMethod($this, 'getActions');
        $isProto = ($reflector->getDeclaringClass()->getName() !== get_class($this));

        return !$isProto;
    }

    /**
     * @param Column $column
     * @param string $value
     * @param array  $row
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function prepareValue($column, $value, $row)
    {
        return $value;
    }

    /**
     * @param string $report
     * @param array  $filters
     * @return string
     */
    public function getReportUrl($report, $filters = [])
    {
        return $this->context->urlManager->getUrl(
            'reports/report/view',
            [
                'report' => $report,
                '_query' => [
                    'filters' => $filters,
                ],
            ]
        );
    }
}
