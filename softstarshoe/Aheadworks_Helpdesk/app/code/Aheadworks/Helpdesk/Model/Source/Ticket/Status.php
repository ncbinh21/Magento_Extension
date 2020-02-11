<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Helpdesk\Model\Source\Ticket
 */
class Status implements OptionSourceInterface
{
    /**
     * Status values
     */
    const OPEN_VALUE = 'open';
    const PENDING_VALUE = 'pending';
    const SOLVED_VALUE = 'solved';

    /**
     * Status labels
     */
    const OPEN_LABEL = 'Open';
    const PENDING_LABEL = 'Pending';
    const SOLVED_LABEL = 'Solved';

    const DEFAULT_STATUS = self::PENDING_VALUE;

    /**
     * Get options array without translation
     *
     * @return array
     */
    public static function getOptionsArrayWithoutTranslation()
    {
        return [
            self::OPEN_VALUE => self::OPEN_LABEL,
            self::PENDING_VALUE => self::PENDING_LABEL,
            self::SOLVED_VALUE => self::SOLVED_LABEL
        ];
    }

    /**
     *  Get translated options array
     *
     * @return array
     */
    public function getTranslatedOptionsArray()
    {
        $translatedOptions = [];
        foreach ($this->getOptionsArrayWithoutTranslation() as $value => $label) {
            $translatedOptions[$value] =  __($label);
        }
        return $translatedOptions;
    }

    /**
     * To option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionsArray = [];
        foreach ($this->getTranslatedOptionsArray() as $value => $label) {
            $optionsArray[] = ['value' => $value, 'label' => $label];
        }
        return $optionsArray;
    }

    /**
     * Get option label
     *
     * @param $status
     * @return string
     */
    public function getOptionLabelByValue($status)
    {
        $statuses = $this->getTranslatedOptionsArray();
        $label = '';
        if (array_key_exists($status, $statuses)) {
            $label = $statuses[$status];
        }
        return $label;
    }

    /**
     * Get form option array
     * @return array
     */
    public function getFormOptionArray()
    {
        $options = $this->getTranslatedOptionsArray();
        unset($options[self::SOLVED_VALUE]);

        return $options;
    }
}
