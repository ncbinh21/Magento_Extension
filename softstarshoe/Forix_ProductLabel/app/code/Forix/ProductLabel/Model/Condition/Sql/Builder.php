<?php
namespace Forix\ProductLabel\Model\Condition\Sql;

use Magento\Framework\DB\Select;
use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Combine;
use \Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Store\Model\Store;

/**
 * Class Builder
 * @package Forix\ProductLabel\Model\Condition\Sql
 */
class Builder
{
    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * @var array
     */
    protected $_conditionOperatorMap = [
        '=='    => ':field = ?',
        '!='    => ':field <> ?',
        '>='    => ':field >= ?',
        '>'     => ':field > ?',
        '<='    => ':field <= ?',
        '<'     => ':field < ?',
        '{}'    => ':field IN (?)',
        '!{}'   => ':field NOT IN (?)',
        '()'    => ':field IN (?)',
        '!()'   => ':field NOT IN (?)',
    ];

    protected $_conditionOperation = [
        '=='    => ':field = ?',
        '!='    => ':field <> ?',
        '>='    => ':field >= ?',
        '>'     => ':field > ?',
        '<='    => ':field <= ?',
        '<'     => ':field < ?',
        '{}'    => ':field LIKE "%?%"',
        '!{}'   => ':field NOT LIKE "%?%"',
        '()'    => ':field LIKE "%?%"',
        '!()'   => ':field NOT LIKE "%?%"',
    ];

    /**
     * @var ExpressionFactory
     */
    protected $_expressionFactory;

    /**
     * @var EavConfig
     */
    protected $eavConfig;

    /**
     * Builder constructor.
     *
     * @param ExpressionFactory $expressionFactory
     * @param EavConfig $eavConfig
     */
    public function __construct(
        ExpressionFactory $expressionFactory,
        EavConfig $eavConfig
    ) {
        $this->_expressionFactory = $expressionFactory;
        $this->eavConfig = $eavConfig;
    }

    /**
     * @param AbstractCollection $collection
     * @param Combine $combine
     * @throws \Zend_Db_Select_Exception
     */
    public function attachConditionToCollection(
        AbstractCollection $collection,
        Combine $combine
    ) {
        $this->_connection = $collection->getResource()->getConnection();
        $data = $this->_getCombineTablesToJoin($combine);
        $this->_joinTablesToCollection($collection, $data);
        $this->addColumnToSelect($collection);

        $whereExpression = (string)$this->_getMappedSqlCombination($combine);
        if (!empty($whereExpression)) {
            // Select ::where method adds braces even on empty expression
            $collection->getSelect()->where($whereExpression);
        }
    }

    /**
     * @param Combine $combine
     * @return array
     */
    protected function _getCombineTablesToJoin(Combine $combine)
    {
        $data = [];

        /** @var \Forix\ProductLabel\Model\Rule\Condition\Product $condition */
        foreach ($combine->getConditions() as $condition) {
            if ($condition instanceof Combine) {
                $data[] = ['sub' => $this->_getCombineTablesToJoin($condition)];
            } else {
                $attribute = $condition->getAttributeObject();
                $joinTable = [];
                if ($attribute->getId()) {
                    if ($attribute->getAttributeCode() == 'quantity_and_stock_status') {
                        $alias = 'stock_status';
                        $joinTable = [
                            'alias' => $alias,
                            'table' => 'cataloginventory_stock_status',
                            'column' => 'stock_status',
                            'conditions' => [
                                'e.entity_id = stock_status.product_id'
                            ]
                        ];
                    } else if ($attribute->getBackendType() != 'static') {
                        $alias = 'at_' . $attribute->getAttributeCode();
                        $joinTable = [
                            'alias' => $alias,
                            'table' => 'catalog_product_entity_' . $attribute->getBackendType(),
                            'column' => $this->checkIfExistColumn($alias) . ' AS ' . $attribute->getAttributeCode(),
                            'conditions' => [
                                '(' . $alias . '.`entity_id` = e.`entity_id`' . ')',
                                $this->_connection->quoteInto('(' . $alias . '.attribute_id = ?)', $attribute->getId()),
                                $this->_connection->quoteInto('(' . $alias . '.store_id = ?)', Store::DEFAULT_STORE_ID),
                            ]
                        ];
                    }

                    $data[] = $joinTable;
                }
            }
        }

        return $data;
    }

    /**
     * @param $alias
     * @return Expression
     */
    protected function checkIfExistColumn($alias)
    {
        $out = 'IF(' . $alias . '.value_id > 0, '. $alias .'.value, null)';

        return $this->_expressionFactory->create(['expression' => $out]);
    }

    /**
     * Join tables from conditions combination to collection
     *
     * @param \Magento\Eav\Model\Entity\Collection\AbstractCollection $collection
     * @param array $data
     * @param array $joined
     * @return void
     */
    protected function _joinTablesToCollection(AbstractCollection $collection, $data, $joined = [])
    {
        if (is_array($data)) {
            foreach ($data as $table) {
                if (isset($table['sub'])) {
                    $this->_joinTablesToCollection($collection, $table['sub'], $joined);
                } else {
                    if (isset($table['alias'])
                        && !in_array($table['alias'], $joined)
                        && !empty($table)
                    ) {
                        $cond = implode(' AND ', $table['conditions']);
                        $tableName = $this->_connection->getTableName($table['table']);
                        $collection->getSelect()->joinLeft([$table['alias'] => $tableName], $cond)
                            ->columns($table['column'], $table['alias']);
                        $joined[] = $table['alias'];
                    }
                }
            }
        }
    }

    /**
     * @param AbstractCondition $condition
     * @param string $out
     * @return string
     */
    protected function _getMappedSqlCondition(AbstractCondition $condition, $out = '')
    {
        /** @var \Forix\ProductLabel\Model\Rule\Condition\Product $condition */
        $inCondition = ['{}', '!{}', '()','!()'];
        $attribute = $condition->getAttributeObject();
        $value = $condition->getValue();
        $operator = $condition->getOperator();
        if ($attribute->getId()) {
            $alias = 'at_' . $attribute->getAttributeCode();
            $column = $this->checkIfExistColumn($alias);
            if ($attribute->getAttributeCode() == 'quantity_and_stock_status') {
                $column = 'stock_status.stock_status';
            }
            if ($attribute->getBackendType() != 'static') {
                if (is_array($value) && $attribute->getFrontendInput() == 'multiselect') {
                    if ($operator == '()' || $operator == '!()') {
                        $cond = ' OR ';
                    } else {
                        $cond = ' AND ';
                    }
                    $where = ' FIND_IN_SET(?, ' . $column . ')';
                    if ($operator == '!()' || $operator == '!{}') {
                        $where = ' NOT FIND_IN_SET(?, ' . $column . ')';
                    }
                    $multiValue = [];
                    foreach ($value as $val) {
                        $multiValue[] = $this->_connection->quoteInto($where, $val);
                    }
                    $out .= implode($cond, $multiValue);
                } else if (is_string($value) && isset($this->_conditionOperatorMap[$operator])) {
                    if (isset($this->_conditionOperatorMap[$operator])) {
                        $where = str_replace(':field', $column, $this->_conditionOperatorMap[$operator]);
                        if (in_array($operator, $inCondition)) {
                            $value = explode(',', $value);
                        }
                        $out .= $this->_connection->quoteInto($where, $value);
                    }
                }
            } else if ($attribute->getAttributeCode() == 'sku') {

                $where = str_replace(':field', 'sku', $this->_conditionOperatorMap[$operator]);
                if (in_array($operator, $inCondition)) {
                    $value = $condition->getValueParsed();
                }
                $out .= $this->_connection->quoteInto($where, $value);
            } else if ($attribute->getAttributeCode() == 'category_ids') {
                $conditionCategory = $this->_connection->select()
                    ->from(
                        $this->_connection->getTableName('catalog_category_product'),
                        ['product_id']
                    )->where(
                        'category_id IN (?)',
                        $condition->getValueParsed()
                    )->__toString();
                if ($operator == '==') {
                    $operator = '{}';
                }
                if ($operator == '!=') {
                    $operator = '!{}';
                }
                $where = str_replace(':field', 'e.entity_id', $this->_conditionOperatorMap[$operator]);
                $where = str_replace('?', $conditionCategory, $where);
                $out .= $this->_connection->quoteInto($where, $conditionCategory);
            }

        }

        return $out;
    }

    /**
     * @param Combine $combine
     * @param string $value
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getMappedSqlCombination(Combine $combine, $value = '')
    {
        $out = (!empty($value) ? $value : '');
        $value = ($combine->getValue() ? '' : ' NOT ');
        $getAggregator = $combine->getAggregator();
        $conditions = $combine->getConditions();
        foreach ($conditions as $key => $condition) {
            /** @var $condition AbstractCondition|Combine */
            $con = ($getAggregator == 'any' ? Select::SQL_OR : Select::SQL_AND);
            $con = (isset($conditions[$key+1]) ? $con : '');
            if ($condition instanceof Combine) {
                $out .= $this->_getMappedSqlCombination($condition, $value);
            } else {
                $out .= ' ' . $this->_getMappedSqlCondition($condition, $value);
            }
            $out .=  $out ? (' ' . $con) : '';
        }
        return $this->_expressionFactory->create(['expression' => $out]);
    }

    /**
     * @param AbstractCollection $collection
     * @return $this
     * @throws \Zend_Db_Select_Exception
     */
    protected function addColumnToSelect($collection)
    {
        $columns = $collection->getSelect()->getPart('columns');
        $collection->getSelect()->reset('columns');
        foreach ($columns as $column) {
            list($tableAlias, $cols, $aliasColumn) = $column;
            if ($cols == '*' && $tableAlias != 'e') {
                continue;
            }
            if ($cols instanceof \Zend_Db_Expr) {
                $cols = (string)$cols;
            } else {
                $cols = $tableAlias . '.' . $cols;
            }
            if (strlen($aliasColumn) > 0) {
                $cols .= ' AS ' . $aliasColumn;
            }
            $collection->getSelect()->columns($cols, $tableAlias);
        }

        return $this;
    }
}
