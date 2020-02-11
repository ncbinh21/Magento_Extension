<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/


namespace Aheadworks\Helpdesk\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Secure
 * @package Aheadworks\Helpdesk\Model\Source\Gateway
 */
class Secure implements OptionSourceInterface
{
    const TYPE_NONE_VALUE = '0';
    const TYPE_SSL_VALUE  = 'SSL';
    const TYPE_TLS_VALUE  = 'TLS';

    const TYPE_NONE_LABEL = 'None';
    const TYPE_SSL_LABEL  = 'SSL';
    const TYPE_TLS_LABEL  = 'TLS';

    /**
     * Get options array without translation
     *
     * @return array
     */
    public static function getOptionsArrayWithoutTranslation()
    {
        return [
            self::TYPE_NONE_VALUE => self::TYPE_NONE_LABEL,
            self::TYPE_SSL_VALUE => self::TYPE_SSL_LABEL,
            self::TYPE_TLS_VALUE => self::TYPE_TLS_LABEL
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
        foreach ($this->getOptionsArrayWithoutTranslation() as $value => $label) {
            $optionsArray[] = ['value' => $value, 'label' => __($label)];
        }
        return $optionsArray;
    }
}
