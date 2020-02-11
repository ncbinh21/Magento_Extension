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


namespace Mirasvit\Report\Ui;

use Magento\Framework\Profiler;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Report\Model\Collection;

class DataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var ContextInterface
     */
    private $context;

    /**
     * @var Context
     */
    private $uiContext;

    /**
     * @var MapRepositoryInterface
     */
    private $mapRepository;

    /**
     * @var array
     */
    private $comparisonFilters = [];

    /**
     * @var array
     */
    private $regularFilters = [];

    public function __construct(
        Context $uiContext,
        MapRepositoryInterface $mapRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        ContextInterface $context,
        array $meta = [],
        array $data = []
    ) {
        $this->context = $context;
        $this->mapRepository = $mapRepository;
        $this->uiContext = $uiContext;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);

        $this->reset();
    }

    /**
     * @return void
     */
    public function reset()
    {
        Profiler::start(__METHOD__);
        $this->collection = $this->uiContext->getReport()->getCollection();
        Profiler::stop(__METHOD__);
    }

    /**
     * @param array  $items
     * @param string $prefix
     * @return array
     */
    private function map($items, $prefix = '')
    {
        $result = [];
        foreach ($items as $key => $item) {
            foreach ($item as $code => $value) {
                $pk = $items[$key]['pk'];

                if ($code == '1' || $code == 'pk') {
                    $result[$pk][$code] = $value;
                    continue;
                }

                $column = $this->mapRepository->getColumn($code);
                if (!$column) {
                    continue;
                }


                $result[$pk][$prefix . $code . '_orig'] = $items[$key][$code];
                $result[$pk][$prefix . $code] = $column->prepareValue($value);
                $result[$pk][$prefix . $code] = $this->uiContext->getReport()->prepareValue(
                    $column,
                    $result[$pk][$prefix . $code],
                    $result[$pk]
                );
            }
        }

        return $result;
    }

    /**
     * @param array $to
     * @param array $from
     * @return array
     */
    private function append($to, $from)
    {
        foreach ($from as $key => $value) {
            if (isset($to[$key])) {
                $to[$key] = array_merge_recursive($to[$key], $value);
            }
        }

        return $to;
    }

    /**
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function getData()
    {
        Profiler::start(__METHOD__);

        $startTime = microtime(true);

        $searchResult = $this->getSearchResult()->toArray();

        $items = $this->map($searchResult['items']);

        if (count($this->comparisonFilters)) {
            $comparisonSearchResult = $this->getComparisonSearchResult()->toArray();
            $comparisonItems = $this->map($comparisonSearchResult['items'], 'c_');

            $items = $this->append($items, $comparisonItems);
        }

        $totals = $this->getSearchResult()->getTotals();
        $totals[$this->uiContext->getActiveDimension()] = '';
        if (count($this->comparisonFilters)) {
            $cTotals = $this->getComparisonSearchResult()->getTotals();
            foreach ($cTotals->convertToArray() as $code => $value) {
                if ($code != '1' && $code != 'pk') {
                    $column = $this->mapRepository->getColumn($code);

                    if (in_array($column->getDataType(), ['number', 'price'])) {
                        if ($column) {
                            $totals['c_' . $code . '_orig'] = $totals['c_' . $code];
                            $totals['c_' . $code] = $column->prepareValue($value);
                            $totals['c_' . $code] = $this->uiContext->getReport()
                                ->prepareValue($column, $totals['c_' . $code], $totals);
                        }
                    } else {
                        $totals['c_' . $code] = '';
                    }
                }
            }
        }

        $result = [
            'totalRecords'    => $this->getSearchResult()->getSize(),
            'items'           => array_values($items),
            'totals'          => [$totals->toArray()],
            'dimensionColumn' => $this->uiContext->getActiveDimension(),
            'comparison'      => count($this->comparisonFilters) ? true : false,
            'select'          => $this->getCollection()->__toString(),
            'time'            => round(microtime(true) - $startTime, 4),
        ];

        Profiler::stop(__METHOD__);

        return $result;
    }


    /**
     * {@inheritdoc}
     */
    public function getSearchResult()
    {
        $collection = clone $this->collection;

        $collection
            ->addColumnToSelect($this->mapRepository->getColumn($this->uiContext->getActiveDimension()));

        $param = 1;
        foreach ($this->regularFilters as $filter) {
            $column = $this->mapRepository->getColumn($filter->getField());

            $collection->addColumnToFilter(
                $column,
                [$filter->getConditionType() => $filter->getValue()]
            );

            if ($filter->getConditionType() == 'gteq') {
                $param = $filter->getValue();

            }
        }

        $collection->addPkColumnToSelect(
            $this->mapRepository->getColumn($this->uiContext->getActiveDimension()),
            $param
        );

        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function getComparisonSearchResult()
    {
        $collection = clone $this->collection;

        $collection
            ->addColumnToSelect($this->mapRepository->getColumn($this->uiContext->getActiveDimension()));

        $searchResult = $this->getSearchResult()->toArray();

        $items = $this->map($searchResult['items']);

        $keys = array_keys($items);

        foreach ($this->comparisonFilters as $filter) {
            $column = $this->mapRepository->getColumn($filter->getField());

            $collection->addColumnToFilter(
                $column,
                [$filter->getConditionType() => $filter->getValue()]
            );

            if ($filter->getConditionType() == 'gteq') {
                $param = $filter->getValue();
                $collection->addPkColumnToSelect(
                    $this->mapRepository->getColumn($this->uiContext->getActiveDimension()),
                    $filter->getValue()
                );
            }
        }

        $keys[] = 0;
        $collection->addPkColumnToFilter(
            $this->mapRepository->getColumn($this->uiContext->getActiveDimension()),
            $keys,
            $param
        );


        return $collection;
    }

    /**
     * {@inheritdoc}
     */
    public function addField($field, $alias = null)
    {
        $column = $this->mapRepository->getColumn($field);

        $this->collection->addColumnToSelect($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction)
    {
        $column = $this->mapRepository->getColumn($field);

        $this->collection->addColumnToOrder(
            $column,
            $direction
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(\Magento\Framework\Api\Filter $filter)
    {
        $column = $this->mapRepository->getColumn($filter->getField());

        // 'sales_order|products' column contains filter with own logic and condition type 'IN'
        if ('sales_order|products' === $column->getName() && 'like' === $filter->getConditionType()) {
            return $this;
        }

        if ('sales_order|orders_products' === $column->getName() && 'like' === $filter->getConditionType()) {
            return $this;
        }

        if ('quote|products' === $column->getName() && 'like' === $filter->getConditionType()) {
            return $this;
        }

        $this->collection->addColumnToFilter(
            $column,
            [$filter->getConditionType() => $filter->getValue()]
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addRegularFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->regularFilters[] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addComparisonFilter(\Magento\Framework\Api\Filter $filter)
    {
        $this->comparisonFilters[] = $filter;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setDimension($column)
    {
        if (!is_object($column)) {
            $column = $this->mapRepository->getColumn($column);
        }

        $this->collection->setColumnToGroup($column);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($page, $size)
    {
        $this->collection
            ->setPageSize($size)
            ->setCurPage($page);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigData()
    {
        $this->data['config']['params'] = [
            'report' => $this->uiContext->getReport()->getIdentifier(),
        ];

        return isset($this->data['config']) ? $this->data['config'] : [];
    }
}
