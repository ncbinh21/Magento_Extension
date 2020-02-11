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


namespace Mirasvit\Report\Api\Service;

interface DateServiceInterface
{
    const TODAY            = 'today';
    const YESTERDAY        = 'yesterday';
    const THIS_WEEK        = 'week';
    const PREVIOUS_WEEK    = 'prev_week';
    const THIS_MONTH       = 'month';
    const PREVIOUS_MONTH   = 'prev_month';
    const THIS_QUARTER     = 'quarter';
    const PREVIOUS_QUARTER = 'prev_quarter';
    const THIS_YEAR        = 'year';
    const PREVIOUS_YEAR    = 'prev_year';

    const LAST_24H = 'last_24h';
    const LAST_7D  = 'last_7d';
    const LAST_30D = 'last_30d';
    const LAST_3M  = 'last_3m';
    const LAST_12M = 'last_12m';

    const LIFETIME = 'lifetime';
    const CUSTOM   = 'custom';

    /**
     * @param bool $includeTimeIntervals 24h, 7d, 30d, 3m, 12m
     * @param bool $addHint
     * @return string[]
     */
    public function getIntervals($includeTimeIntervals = false, $addHint = false);

    /**
     * @param string $code
     * @return string
     */
    public function getIntervalHint($code);

    /**
     * @param string $code
     * @param bool   $inStoreTZ
     * @return IntervalInterface
     */
    public function getInterval($code, $inStoreTZ = false);

    /**
     * @param string $code
     * @param int    $offsetDays
     * @param bool   $inStoreTZ
     * @return IntervalInterface
     */
    public function getPreviousInterval($code, $offsetDays = 0, $inStoreTZ = false);
}

interface IntervalInterface
{
    /**
     * @return \Zend_Date
     */
    public function getFrom();

    /**
     * @return \Zend_Date
     */
    public function getTo();
}