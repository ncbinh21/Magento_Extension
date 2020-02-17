<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\CatalogSearch\Model\Adapter\Mysql\Filter;

use Magento\Framework\Search\Request\FilterInterface;
use Magento\CatalogSearch\Model\Search\RequestGenerator;
use Magento\Framework\App\ResourceConnection;
use Amasty\Shopby\Helper\Category;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Preprocessor
{
    private $validFields = ['rating_summary'];

    private $invalidFields = ['am_on_sale', 'am_is_new'];

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    private $connection;

    /**
     * Preprocessor constructor.
     * @param ResourceConnection $resource
     */
    public function __construct(ResourceConnection $resource)
    {
        $this->connection = $resource->getConnection();
    }

    /**
     * @param $subject
     * @param callable $proceed
     * @param FilterInterface $filter
     * @param $isNegation
     * @param $query
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function aroundProcess(
        $subject,
        callable $proceed,
        FilterInterface $filter,
        $isNegation,
        $query
    ) {
        if (in_array($filter->getField(), $this->invalidFields)) {
            return '';
        }
        if (in_array($filter->getField(), $this->validFields)) {
            $alias = $filter->getField() . RequestGenerator::FILTER_SUFFIX;
            return str_replace(
                $this->connection->quoteIdentifier($filter->getField()),
                $this->connection->quoteIdentifier($alias . '.' . $filter->getField()),
                $query
            );
        } elseif ($filter->getField() === Category::STORE_CODE) {
            return $this->processQuery($query, 'store_id', 'store_id');
        } elseif ($filter->getField() === Category::ATTRIBUTE_CODE && is_array($filter->getValue())) {
            return $this->processQuery($query, 'category_ids', 'category_id');
        }
        return $proceed($filter, $isNegation, $query);
    }

    /**
     * @param string $query
     * @param string $column1
     * @param string $column2
     * @return string
     */
    private function processQuery($query, $column1, $column2)
    {
        return str_replace(
            $this->connection->quoteIdentifier($column1),
            $this->connection->quoteIdentifier('category_ids_index.' . $column2),
            $query
        );
    }
}
