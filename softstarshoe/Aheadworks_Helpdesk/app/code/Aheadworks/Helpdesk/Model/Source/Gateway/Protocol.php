<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Helpdesk\Model\Source\Gateway;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Protocol
 * @package Aheadworks\Helpdesk\Model\Source\Gateway
 */
class Protocol implements OptionSourceInterface
{
    const POP3_VALUE = 'POP3';
    const IMAP_VALUE = 'IMAP';

    const POP3_LABEL = 'POP3';
    const IMAP_LABEL = 'IMAP';

    const POP3_INSTANCE = 'Zend_Mail_Storage_Pop3';
    const IMAP_INSTANCE = 'Zend_Mail_Storage_Imap';

    /**
     * Get options array without translation
     *
     * @return array
     */
    public static function getOptionsArrayWithoutTranslation()
    {
        return [
            self::POP3_VALUE => self::POP3_LABEL,
            self::IMAP_VALUE => self::IMAP_LABEL
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

    /**
     * Get instance by protocol
     * @param int $protocol
     * @return null | string
     */
    public function getInstanceByProtocol($protocol)
    {
        switch ($protocol) {
            case self::POP3_VALUE:
                $instance = self::POP3_INSTANCE;
                break;
            case self::IMAP_VALUE:
                $instance = self::IMAP_INSTANCE;
                break;
            default:
                $instance = null;
        }
        return $instance;
    }
}
