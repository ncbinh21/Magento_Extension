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


namespace Mirasvit\Report\Service;

use Mirasvit\Report\Api\Service\DateServiceInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Directory\Helper\Data as DirectoryHelper;

/**
 * @SuppressWarnings(PHPMD)
 */
class DateService implements DateServiceInterface
{
    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param DateTime              $dateTime
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface  $scopeConfig
     */
    public function __construct(
        DateTime $dateTime,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->dateTime = $dateTime;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntervals($includeTimeIntervals = false, $addHint = false)
    {
        $intervals = [];

        $intervals[self::TODAY] = 'Today';
        $intervals[self::YESTERDAY] = 'Yesterday';

        $intervals[self::THIS_WEEK] = 'This week';
        $intervals[self::PREVIOUS_WEEK] = 'Previous week';

        $intervals[self::THIS_MONTH] = 'This month';
        $intervals[self::PREVIOUS_MONTH] = 'Previous month';

        $intervals[self::THIS_YEAR] = 'This year';
        $intervals[self::PREVIOUS_YEAR] = 'Previous year';

        if ($includeTimeIntervals) {
            $intervals[self::LAST_24H] = 'Last 24h hours';
            $intervals[self::LAST_7D] = 'Last 7 days';
            $intervals[self::LAST_30D] = 'Last 30 days';
            $intervals[self::LAST_3M] = 'Last 3 months';
            $intervals[self::LAST_12M] = 'Last 12 months';
        }

        $intervals[self::LIFETIME] = 'Lifetime';

        if ($addHint) {
            foreach ($intervals as $code => $label) {
                $label = __($label);

                $hint = $this->getIntervalHint($code);

                if ($hint) {
                    $label .= ' / ' . $hint;

                    $intervals[$code] = $label . '';
                }
            }
        }

        return $intervals;
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval($code, $inStoreTZ = false)
    {
        $timestamp = $this->dateTime->gmtTimestamp();
        $firstDay = (int)$this->scopeConfig->getValue('general/locale/firstday');
        $locale = $this->scopeConfig->getValue(DirectoryHelper::XML_PATH_DEFAULT_LOCALE);
        $localeTimezone = $this->scopeConfig->getValue(DirectoryHelper::XML_PATH_DEFAULT_TIMEZONE);

        if ($inStoreTZ) {
            $timestamp = $this->dateTime->date($timestamp);
        }

        $from = new \Zend_Date(
            $timestamp,
            null,
            $locale
        );

        $offset = $this->dateTime->calculateOffset($localeTimezone);
        $from->addSecond($offset);
        $to = clone $from;

        switch ($code) {
            case self::TODAY:
                $from->setTime('00:00:00');

                $to->setTime('23:59:59');

                break;

            case self::YESTERDAY:
                $from->subDay(1)
                    ->setTime('00:00:00');

                $to->subDay(1)
                    ->setTime('23:59:59');

                break;

            case self::THIS_MONTH:
                $from->setDay(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->addDay($to->get(\Zend_Date::MONTH_DAYS) - 1)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_MONTH:
                $from->setDay(1)
                    ->subMonth(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->subMonth(1)
                    ->addDay($to->get(\Zend_Date::MONTH_DAYS) - 1)
                    ->setTime('23:59:59');

                break;

            case self::THIS_QUARTER:
                $month = intval($from->get(\Zend_Date::MONTH) / 4) * 3 + 1;
                $from->setDay(1)
                    ->setMonth($month)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth($month)
                    ->addMonth(3)
                    ->subDay(1)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_QUARTER:
                $month = intval($from->get(\Zend_Date::MONTH) / 4) * 3 + 1;

                $from->setDay(1)
                    ->setMonth($month)
                    ->subMonth(3)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth($month)
                    ->addMonth(3)
                    ->subDay(1)
                    ->subMonth(3)
                    ->setTime('23:59:59');

                break;

            case self::THIS_YEAR:
                $from->setDay(1)
                    ->setMonth(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth(1)
                    ->addDay($to->get(\Zend_Date::LEAPYEAR) ? 365 : 364)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_YEAR:
                $from->setDay(1)
                    ->setMonth(1)
                    ->subYear(1)
                    ->setTime('00:00:00');

                $to->setDay(1)
                    ->setMonth(1)
                    ->addDay($to->get(\Zend_Date::LEAPYEAR) ? 365 : 364)
                    ->subYear(1)
                    ->setTime('23:59:59');

                break;

            case self::LAST_24H:
                $from->subDay(1);

                break;

            case self::THIS_WEEK:
                $weekday = $from->get(\Zend_Date::WEEKDAY_DIGIT); #0-6

                if ($weekday < $firstDay) {
                    $weekday += 7;
                }

                $from->subDay($weekday - $firstDay)
                    ->setTime('00:00:00');

                $to->addDay(6 - $weekday + $firstDay)
                    ->setTime('23:59:59');

                break;

            case self::PREVIOUS_WEEK:
                $weekday = $from->get(\Zend_Date::WEEKDAY_DIGIT); #0-6

                if ($weekday < $firstDay) {
                    $weekday += 7;
                }

                $from->subDay($weekday - $firstDay)
                    ->subWeek(1)
                    ->setTime('00:00:00');

                $to->addDay(6 - $weekday + $firstDay)
                    ->subWeek(1)
                    ->setTime('23:59:59');

                break;

            case self::LAST_7D:
                $from->subDay(7);

                break;

            case self::LAST_30D:
                $from->subDay(30);

                break;

            case self::LAST_3M:
                $from->subMonth(3);

                break;

            case self::LAST_12M:
                $from->subYear(1);

                break;

            case self::LIFETIME:
                $from->subYear(10);

                $to->addYear(10);

                break;
        }

        return new \Magento\Framework\DataObject([
            'from' => $from,
            'to'   => $to,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getIntervalHint($code)
    {
        $hint = '';

        $interval = $this->getInterval($code, true);
        $from = $interval->getFrom();
        $to = $interval->getTo();

        switch ($code) {
            case self::TODAY:
            case self::YESTERDAY:
                $hint = $from->get('MMM, d HH:mm') . ' - ' . $to->get('HH:mm');
                break;

            case self::THIS_WEEK:
            case self::PREVIOUS_WEEK:
            case self::LAST_7D:
            case self::LAST_30D:
            case self::LAST_3M:
            case self::LAST_12M:
            case self::THIS_MONTH:
            case self::PREVIOUS_MONTH:
            case self::THIS_QUARTER:
            case self::PREVIOUS_QUARTER:
                if ($from->get('YYYY') == $to->get('YYYY') && $from->get('YYYY') == date('Y')) {
                    if ($from->get('MMM') == $to->get('MMM')) {
                        $hint = $from->get('MMM, d') . ' - ' . $to->get('d');
                    } else {
                        $hint = $from->get('MMM, d') . ' - ' . $to->get('MMM, d');
                    }
                } else {
                    $hint = $from->get('MMM, d YYYY') . ' - ' . $to->get('MMM, d YYYY');
                }

                break;

            case self::THIS_YEAR:
            case self::PREVIOUS_YEAR:
                $hint = $from->get('MMM, d YYYY') . ' - ' . $to->get('MMM, d');
                break;

            case self::LAST_24H:
                $hint = $from->get('MMM, d HH:mm') . ' - ' . $to->get('MMM, d HH:mm');
                break;

            case self::LIFETIME:
                $hint = $from->get('MMM, d YYYY') . ' - ' . $to->get('MMM, d YYYY');
                break;
        }

        return $hint;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousInterval($code, $offsetDays = 0, $inStoreTZ = false)
    {
        $interval = $this->getInterval($code, $inStoreTZ);

        $now = new \Zend_Date(
            $this->dateTime->gmtTimestamp(),
            null,
            $this->storeManager->getStore()->getLocaleCode()
        );

        $diff = clone $interval->getTo();
        $diff->sub($interval->getFrom());

        if ($inStoreTZ) {
            $diff->sub($this->dateTime->getGmtOffset());
        }

        if ($interval->getTo()->getTimestamp() > $now->getTimestamp()) {
            $interval->getTo()->subTimestamp($interval->getTo()->getTimestamp() - $now->getTimestamp());
        }

        if (365 === intval($offsetDays)) {
            $interval->getFrom()->subYear(1);
            $interval->getTo()->subYear(1);
        } elseif (intval($offsetDays) > 0) {
            $interval->getFrom()->subDay($offsetDays);
            $interval->getTo()->subDay($offsetDays);
        } else {
            $interval->getFrom()->sub($diff);
            $interval->getTo()->sub($diff);
        }

        return $interval;
    }
}