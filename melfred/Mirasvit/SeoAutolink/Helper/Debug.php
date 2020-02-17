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
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoAutolink\Helper;

class Debug extends \Magento\Framework\App\Helper\AbstractHelper
{
    const TIMER_TAG = 'SeoAutolink_';
    const LOG_PATH = '/var/log/seo_autolink_timer.log';
    /**
     * @param string $timer
     * @return $this
     */
    public function startTimer($timer)
    {
        $_SERVER[self::TIMER_TAG . $timer] = microtime(true);

        return $this;
    }

    /**
     * @param string $timer
     * @return $this
     */
    public function stopTimer($timer)
    {
        if (isset($_SERVER[self::TIMER_TAG . $timer])) {
            $_SERVER[self::TIMER_TAG . $timer . '_RESULT'] = microtime(true) - $_SERVER[self::TIMER_TAG . $timer];
        }

        return $this;
    }

    /**
     * @param string $timer
     * @return string
     */
    public function getTimer($timer)
    {
        if (isset($_SERVER[self::TIMER_TAG . $timer . '_RESULT'])) {
            return $this->getConvertedTime($_SERVER[self::TIMER_TAG . $timer . '_RESULT']) . 'ms';
        }

        return 'No data';
    }

    /**
     * @param string $timer
     * @return void
     */
    public function logTimer($timer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . self::LOG_PATH);
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($this->getTimer($timer));
    }

    /**
     * Convert seconds to milliseconds
     * @param float $time
     * @return float
     */
    public function getConvertedTime($time)
    {
        return round($time * 1000, 1);
    }
}
