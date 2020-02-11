<?php
/*************************************************
 * *
 *  *
 *  * Copyright © 2017 Forix. All rights reserved.
 *  * See COPYING.txt for license details.
 *
 */
namespace Forix\ProductLabel\Model\Source\Config;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Position
 *
 * @package Forix\ProductLabel\Model\Source\Config
 */
class Position implements ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['value' => 'top-left', 'label' => __('Top-Left')],
            ['value' => 'top-right', 'label' => __('Top-Right')],
            ['value' => 'bot-left', 'label' => __('Bottom-Left')],
            ['value' => 'bot-right', 'label' => __('Bottom-Right')]
        ];

        array_unshift($options, ['value' => '', 'label' => __('-- Please Select --')]);

        return $options;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'top-left' => __('Top-Left'),
            'top-right' => __('Top-Right'),
            'bottom-left' => __('Bottom-Left'),
            'bottom-right' => __('Bottom-Right')
        ];
    }
}
