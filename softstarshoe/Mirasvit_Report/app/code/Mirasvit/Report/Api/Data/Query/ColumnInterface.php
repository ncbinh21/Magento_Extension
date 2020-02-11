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


namespace Mirasvit\Report\Api\Data\Query;

use Mirasvit\Report\Model\Query\Select;

interface ColumnInterface
{
    const TYPE_EXPRESSION  = 'expression';
    const TYPE_AGGREGATION = 'aggregation';
    const TYPE_SIMPLE      = 'simple';

    const AGGREGATION_AVG  = 'avg';
    const AGGREGATION_SUM  = 'sum';
    const AGGREGATION_NONE = 'none';

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getDataType();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return string|bool
     */
    public function getFilter();

    /**
     * @return string
     */
    public function getAggregationType();

    /**
     * @return string
     * @deprecated
     */
    public function getColor();

    /**
     * @return int
     */
    public function getPercent();

    /**
     * @return array
     */
    public function getOptions();

    /**
     * @param string $value
     * @return string
     */
    public function prepareValue($value);

    /**
     * @param string $param
     * @return \Zend_Db_Expr
     */
    public function toDbExpr($param = null);

    /**
     * @return FieldInterface[]
     */
    public function getFields();

    /**
     * @return array
     * @deprecated
     */
    public function getJsConfig();

    /**
     * @param Select $select
     * @return $this
     */
    public function join(Select $select);

    /**
     * Add column-specific filter to $dataProvider with given $value
     *
     * @param \Mirasvit\Report\Ui\DataProvider $dataProvider
     * @param mixed $value
     *
     * @return $this
     */
    public function filter(\Mirasvit\Report\Ui\DataProvider $dataProvider, $value);
}