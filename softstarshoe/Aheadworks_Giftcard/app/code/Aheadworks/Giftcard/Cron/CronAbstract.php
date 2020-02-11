<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Cron;

use Aheadworks\Giftcard\Api\GiftcardManagementInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Giftcard\Api\GiftcardRepositoryInterface;
use Aheadworks\Giftcard\Model\Flag;
use Aheadworks\Giftcard\Model\FlagFactory;
use Aheadworks\Giftcard\Api\Data\Giftcard\HistoryActionInterfaceFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CronAbstract
 *
 * @package Aheadworks\Giftcard\Cron
 */
abstract class CronAbstract
{
    /**
     * Cron run interval in seconds
     */
    const RUN_INTERVAL = 50;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var GiftcardRepositoryInterface
     */
    protected $giftcardRepository;

    /**
     * @var GiftcardManagementInterface
     */
    protected $giftcardManagement;

    /**
     * @var HistoryActionInterfaceFactory
     */
    protected $historyActionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var Flag
     */
    private $flag;

    /**
     * @param DateTime $dateTime
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GiftcardRepositoryInterface $giftcardRepository
     * @param GiftcardManagementInterface $giftcardManagement
     * @param HistoryActionInterfaceFactory $historyActionFactory
     * @param StoreManagerInterface $storeManager
     * @param TimezoneInterface $localeDate
     * @param FlagFactory $flagFactory
     */
    public function __construct(
        DateTime $dateTime,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GiftcardRepositoryInterface $giftcardRepository,
        GiftcardManagementInterface $giftcardManagement,
        HistoryActionInterfaceFactory $historyActionFactory,
        StoreManagerInterface $storeManager,
        TimezoneInterface $localeDate,
        FlagFactory $flagFactory
    ) {
        $this->dateTime = $dateTime;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->giftcardRepository = $giftcardRepository;
        $this->giftcardManagement = $giftcardManagement;
        $this->historyActionFactory = $historyActionFactory;
        $this->storeManager = $storeManager;
        $this->localeDate = $localeDate;
        $this->flag = $flagFactory->create();
    }

    /**
     * Main cron job entry point
     *
     * @return $this
     */
    abstract public function execute();

    /**
     * Is cron job locked
     *
     * @param string $flag
     * @param int $interval
     * @return bool
     */
    protected function isLocked($flag, $interval = self::RUN_INTERVAL)
    {
        $now = $this->getCurrentTime();
        $lastExecTime = (int)$this->getFlagData($flag);
        return $now < $lastExecTime + $interval;
    }

    /**
     * Set flag data
     *
     * @param string $param
     * @return $this
     */
    protected function setFlagData($param)
    {
        $this->flag
            ->unsetData()
            ->setGiftcardFlagCode($param)
            ->loadSelf()
            ->setFlagData($this->getCurrentTime())
            ->save();

        return $this;
    }

    /**
     * Get current time
     *
     * @return int
     */
    private function getCurrentTime()
    {
        return $this->dateTime->timestamp();
    }

    /**
     * Get flag data
     *
     * @param string $param
     * @return mixed
     */
    private function getFlagData($param)
    {
        $this->flag
            ->unsetData()
            ->setGiftcardFlagCode($param)
            ->loadSelf();

        return $this->flag->getFlagData();
    }
}
