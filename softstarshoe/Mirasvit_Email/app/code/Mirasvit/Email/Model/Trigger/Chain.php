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
 * @package   mirasvit/module-email
 * @version   1.1.13
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Email\Model\Trigger;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\Email\Api\Data\TriggerChainInterface;
use Mirasvit\Email\Api\Data\TriggerInterface;
use Mirasvit\Email\Helper\Data;
use Mirasvit\EmailDesigner\Model\TemplateFactory as TemplateFactory;

/**
 * @method bool   getCouponEnabled()
 * @method int    getCouponSalesRuleId()
 * @method int    getCouponExpiresDays
 * @method bool   getCrossSellsEnabled()
 * @method string getCrossSellsTypeId()
 */
class Chain extends AbstractModel implements TriggerChainInterface
{
    /**
     * @var TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Constructor
     *
     * @param Data            $helper
     * @param TemplateFactory $templateFactory
     * @param Context         $context
     * @param Registry        $registry
     */
    public function __construct(
        Data $helper,
        TemplateFactory $templateFactory,
        Context $context,
        Registry $registry
    ) {
        $this->helper = $helper;
        $this->templateFactory = $templateFactory;

        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(\Mirasvit\Email\Model\ResourceModel\Trigger\Chain::class);
    }

    /**
     * Design template
     *
     * @return \Mirasvit\EmailDesigner\Model\Template
     */
    public function getTemplate()
    {
        return $this->templateFactory->create()->load($this->getTemplateId());
    }

    /**
     * Get email chain hours.
     *
     * @param bool $inSeconds
     *
     * @return int
     */
    public function getDay($inSeconds = false)
    {
        $day = $this->getData(TriggerChainInterface::DAY);
        if ($inSeconds) {
            $day *= 60 * 60 * 24;
        }

        return $day;
    }

    /**
     * Get email chain hours.
     *
     * @param bool $inSeconds
     *
     * @return int
     */
    public function getHour($inSeconds = false)
    {
        $hours = $this->getData(TriggerChainInterface::HOUR);
        if ($inSeconds) {
            $hours *= 60 * 60;
        }

        return $hours;
    }

    /**
     * Get email chain minutes.
     *
     * @param bool $inSeconds
     *
     * @return int
     */
    public function getMinute($inSeconds = false)
    {
        $minutes = $this->getData(TriggerChainInterface::MINUTE);
        if ($inSeconds) {
            $minutes *= 60;
        }

        return $minutes;
    }

    /**
     * @todo RF
     *
     * {@inheritdoc}
     */
    public function getScheduledAt($time)
    {
        $scheduledAt = $time;
        $excludeDays = $this->getExcludeDays();
        $frequency   = $this->getDay(true);
        $hours       = $this->getHour(true);
        $minutes     = $this->getMinute(true);
        $sendFrom    = $this->getSendFrom();
        $sendTo      = $this->getSendTo();

        /*if ($sendFrom && $sendTo) {
            // @todo calculate scheduledAt in a way to send an email between $sendFrom and $sendTo times
            $frequency = ($frequency === 0) ? 86400 : $frequency;
            $scheduledAt = $time + (($frequency - ($time - strtotime('00:00', $time))) + $hours + $minutes);
            $scheduledAt = strtotime($this->helper->convertTz(date('Y-m-d H:i:s', $scheduledAt)));
        } else {*/
            $scheduledAt = $time + $frequency + $hours + $minutes;
        //}

        $scheduledAt = $scheduledAt + $this->addExcludedDays($scheduledAt, $excludeDays) * 86400;

        return $scheduledAt;
    }

    /**
     * Add excluded days.
     *
     * @param int   $time
     * @param array $excludeDaysOfWeek
     * @return int
     */
    protected function addExcludedDays($time, $excludeDaysOfWeek)
    {
        $result = 0;
        if (is_array($excludeDaysOfWeek) && (count($excludeDaysOfWeek) > 0)) {
            while (in_array(date('w', $time + $result * 86400), $excludeDaysOfWeek)) {
                ++$result;

                if ($result > 7) {
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function toString($format = '')
    {
        $delay = '';

        if ($this->getDay()) {
            $delay .= " <b>{$this->getDay()}</b>" . ($this->getDay() > 1 ? ' days' : 'day');
        }

        if ($this->getHour()) {
            $delay .= " <b>{$this->getHour()}</b>" . ($this->getHour() > 1 ? ' hours' : ' hour');
        }
        if ($this->getMinute()) {
            $delay .= " <b>{$this->getMinute()}</b>" . ($this->getMinute() > 1 ? ' mins' : ' min');
        }

        if (!$this->getDay() && !$this->getHour() && !$this->getMinute()) {
            $delay = 'immediately';
        } else {
            $delay .= ' later';
        }

        $coupon = '';
        if ($this->getCouponEnabled()) {
            $coupon = 'with coupon';
        }

        return __('Send <b>%1</b> email %2 %3',
            $this->getTemplate()->getTitle(),
            $delay,
            $coupon
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getTriggerId()
    {
        return $this->getData(TriggerInterface::ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setTriggerId($id)
    {
        $this->setData(TriggerInterface::ID, $id);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setDay($day)
    {
        $this->setData(self::DAY, $day);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setHour($hour)
    {
        $this->setData(self::HOUR, $hour);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setMinute($minute)
    {
        $this->setData(self::MINUTE, $minute);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSendFrom()
    {
        return $this->getData(self::SEND_FROM);
    }

    /**
     * {@inheritDoc}
     */
    public function getSendTo()
    {
        return $this->getData(self::SEND_TO);
    }

    /**
     * {@inheritDoc}
     */
    public function setSendFrom($sendFrom)
    {
        $this->setData(self::SEND_FROM, $sendFrom);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function setSendTo($sendTo)
    {
        $this->setData(self::SEND_TO, $sendTo);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getExcludeDays()
    {
        $excludeDays = $this->getData(self::EXCLUDE_DAYS);
        if (is_string($excludeDays)) {
            $excludeDays = explode(',', $excludeDays);
        }

        return $excludeDays;
    }

    /**
     * {@inheritDoc}
     */
    public function setExcludeDays($excludeDays)
    {
        if (is_array($excludeDays)) {
            $excludeDays = implode(',', $excludeDays);
        }

        $this->setData(self::EXCLUDE_DAYS, $excludeDays);

        return $this;
    }
}
