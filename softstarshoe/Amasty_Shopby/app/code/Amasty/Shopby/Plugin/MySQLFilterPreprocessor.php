<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin;

use Amasty\Shopby\Helper\Category;

class MySQLFilterPreprocessor
{
    protected $connection;

    public function __construct(\Magento\Framework\App\ResourceConnection $resourceConnection)
    {
        $this->connection = $resourceConnection->getConnection();
    }

    public function aroundProcess(
        \Magento\CatalogSearch\Model\Adapter\Mysql\Filter\Preprocessor $subject,
        \Closure $closure,
        \Magento\Framework\Search\Request\FilterInterface $filter,
        $isNegation,
        $query
    ) {
        $result = $closure($filter, $isNegation, $query);
        if ($filter->getField() === Category::STORE_CODE) {
            $result = $this->processQuery($query, 'store_id', 'store_id');
        } elseif ($filter->getField() === Category::ATTRIBUTE_CODE && is_array($filter->getValue())) {
            $result = $this->processQuery($query, 'category_ids', 'category_id');
        }
        return $result;
    }

    private function processQuery($query, $column1, $column2)
    {
        return str_replace(
            $this->connection->quoteIdentifier($column1),
            $this->connection->quoteIdentifier('category_ids_index.' . $column2),
            $query
        );
    }
}
