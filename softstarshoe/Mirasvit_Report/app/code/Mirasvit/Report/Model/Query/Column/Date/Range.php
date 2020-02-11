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


namespace Mirasvit\Report\Model\Query\Column\Date;

use Mirasvit\Report\Api\Data\Query\ColumnInterface;
use Mirasvit\Report\Model\Query\Column;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Stdlib\DateTime;
use Mirasvit\Report\Model\Config;
use Mirasvit\Report\Api\Service\DateServiceInterface;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;

class Range extends Column
{
    /**
     * @var string
     */
    private $range = 'full';

    /**
     * @var DateServiceInterface
     */
    private $dateService;

    /**
     * @var Config
     */
    private $config;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        ResourceConnection $resource,
        DateServiceInterface $dateService,
        Config $config,
        MapRepositoryInterface $mapRepository,
        $name,
        $data = []
    ) {
        parent::__construct($filterBuilder, $mapRepository, $name, $data);

        $this->dateService = $dateService;
        $this->config = $config;
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    /**
     * @param string $range
     * @return $this
     */
    public function setRange($range)
    {
        $this->range = $range;

        return $this;
    }

    /**
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     * @return \Zend_Db_Expr
     */
    public function toString()
    {
        switch ($this->range) {
            case 'full':
                $this->expression = $this->getFullExpression();
                break;

            case 'day':
                $this->expression = $this->getDayExpression();
                break;

            case 'week':
                $this->expression = $this->getWeekExpression();
                break;

            case 'month':
                $this->expression = $this->getMonthExpression();
                break;

            case 'quarter':
                $this->expression = $this->getQuarterExpression();
                break;

            case 'year':
                $this->expression = $this->getYearExpression();
                break;
        }

        return parent::toString();
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function getFullExpression()
    {
        return $this->connection->getDateFormatSql('%1', '%Y-%m-%d %H:%i:%s');
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function getDayExpression()
    {
        return $this->connection->getDateFormatSql('%1', '%Y-%m-%d 00:00:00');
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function getWeekExpression()
    {
        $year = $this->connection->getDateFormatSql('%1', '%Y');
        $weekOfYear = new \Zend_Db_Expr('WEEKOFYEAR(%1)');
        $firstDay = new \Zend_Db_Expr("'Monday'");
        $contact = $this->connection->getConcatSql([$year, $weekOfYear, $firstDay], ' ');

        return $this->connection->getConcatSql(["STR_TO_DATE($contact, '%X %V %W')", "'00:00:00'"], ' ');
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function getMonthExpression()
    {
        return $this->connection->getDateFormatSql('%1', '%Y-%m-01 00:00:00');
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function getQuarterExpression()
    {
        $year = $this->connection->getDateFormatSql('%1', '%Y');
        $quarter = new \Zend_Db_Expr('QUARTER(%1)');

        return $this->connection->getConcatSql([$year, $quarter, "'01 00:00:00'"], '-');
    }

    /**
     * @return \Zend_Db_Expr
     */
    protected function getYearExpression()
    {
        return $this->connection->getDateFormatSql('%1', '%Y-01-01 00:00:00');
    }

    /**
     * @param string $value
     * @return string
     */
    public function prepareValue($value)
    {
        switch ($this->range) {
            case 'day':
                $value = date('d M, Y', strtotime($value));
                break;

            case 'week':
                $value = date('d M, Y', strtotime($value) - 7 * 24 * 60 * 60)
                    . ' - '
                    . date('d M, Y', strtotime($value)) . ' (' . (date('W', strtotime($value)) - 1) . ')';
                break;

            case 'month':
                $value = date('M, Y', strtotime($value));
                break;

            case 'quarter':
                $strVal = strtotime($value);
                $year = date('Y', $strVal);
                switch (date('n', $strVal)) {
                    case 1:
                        $value = 'Jan, '.$year.' – Mar, '.$year;
                        break;
                    case 2:
                        $value = 'Apr, '.$year.' – Jun, '.$year;
                        break;
                    case 3:
                        $value = 'Jul, '.$year.' – Sep, '.$year;
                        break;
                    case 4:
                        $value = 'Oct, '.$year.' – Dec, '.$year;
                        break;
                }
                break;

            case 'year':
                $value = date('Y', strtotime($value));
                break;
        }

        return $value;
    }


    /**
     * {@inheritdoc}
     */
    public function getJsConfig()
    {
        $intervals = [];

        foreach ($this->dateService->getIntervals() as $code => $label) {
            $interval = $this->dateService->getInterval($code);
            $intervals[$label] = [
                $interval->getFrom()->get(DateTime::DATETIME_INTERNAL_FORMAT),
                $interval->getTo()->get(DateTime::DATETIME_INTERNAL_FORMAT),
            ];
        }

        return [
            'component' => 'Mirasvit_Report/js/toolbar/filter/date-range',
            'value'     => [
                'from' => $this->dateService->getInterval('month')->getFrom()->get(DateTime::DATETIME_INTERNAL_FORMAT),
                'to'   => $this->dateService->getInterval('month')->getTo()->get(DateTime::DATETIME_INTERNAL_FORMAT),
            ],
            'intervals' => $intervals,
            'column'    => $this->getName(),
            'locale'    => $this->config->getLocaleData(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function filter(\Mirasvit\Report\Ui\DataProvider $dataProvider, $value)
    {
        if (isset($value['from']) && isset($value['to'])) {

            $from = date('Y-m-d H:i:s', strtotime($value['from']));
            $to = date('Y-m-d H:i:s', strtotime($value['to']) + 23 * 60 * 60 + 59 * 60 + 59);

            $this->filterBuilder
                ->setField($this->getName())
                ->setConditionType('gteq')
                ->setValue($from);
            $dataProvider->addRegularFilter($this->filterBuilder->create());

            if (isset($value['compareFrom']) && !empty($value['compareFrom'])) {
                $from = date('Y-m-d H:i:s', strtotime($value['compareFrom']));
                $this->filterBuilder
                    ->setField($this->getName())
                    ->setConditionType('gteq')
                    ->setValue($from);
                $dataProvider->addComparisonFilter($this->filterBuilder->create());
            }

            $this->filterBuilder
                ->setField($this->getName())
                ->setConditionType('lteq')
                ->setValue($to);
            $dataProvider->addRegularFilter($this->filterBuilder->create());

            if (isset($value['compareTo']) && !empty($value['compareTo'])) {
                $to = date('Y-m-d H:i:s', strtotime($value['compareTo']) + 23 * 60 * 60 + 59 * 60 + 59);
                $this->filterBuilder
                    ->setField($this->getName())
                    ->setConditionType('lteq')
                    ->setValue($to);
                $dataProvider->addComparisonFilter($this->filterBuilder->create());
            }
        }

        return $this;
    }
}
