<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\Source\Ticket;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Priority
 * @package Aheadworks\Helpdesk\Model\Source\Ticket
 */
class Priority implements OptionSourceInterface
{
    /**
     * Priority values
     */
    const HIGH_VALUE = 'high';
    const NORMAL_VALUE = 'normal';
    const LOW_VALUE = 'low';

    const DEFAULT_VALUE = self::NORMAL_VALUE;

    /**
     * Priority labels
     */
    const HIGH_LABEL = 'High';
    const NORMAL_LABEL = 'Normal';
    const LOW_LABEL = 'Low';

    /**
     * Get options array without translation
     *
     * @return array
     */
    public static function getOptionsArrayWithoutTranslation()
    {
        return [
            self::HIGH_VALUE => self::HIGH_LABEL,
            self::NORMAL_VALUE => self::NORMAL_LABEL,
            self::LOW_VALUE => self::LOW_LABEL
        ];
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
     * Get option label
     *
     * @param string $priority
     * @return string
     */
    public function getOptionLabelByValue($priority)
    {
        $priorities = $this->getTranslatedOptionsArray();
        $label = '';
        if (array_key_exists($priority, $priorities)) {
            $label = $priorities[$priority];
        }
        return $label;
    }
}
