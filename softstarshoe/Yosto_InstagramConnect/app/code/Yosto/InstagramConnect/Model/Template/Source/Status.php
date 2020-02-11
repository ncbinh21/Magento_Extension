<?php
/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.txt for details.
 *
 */

namespace Yosto\InstagramConnect\Model\Template\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class Status
 * @package Yosto\InstagramConnect\Model\Template\Source
 */
class Status implements OptionSourceInterface
{
    const ENABLED = 1;
    const DISABLED = 0;

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => __('Enable'), 'value' => self::ENABLED],
            ['label' => __('Disable'), 'value' => self::DISABLED]
        ];

        return $options;
    }
    public function toArray()
    {
        $options = [
            self::ENABLED => __('Enable'),
            self::DISABLED => __('Disable') 
        ];

        return $options;
    }
}