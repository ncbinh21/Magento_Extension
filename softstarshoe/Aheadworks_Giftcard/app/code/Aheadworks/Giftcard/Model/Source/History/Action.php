<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Source\History;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Action
 *
 * @package Aheadworks\Giftcard\Model\Source\History
 */
class Action implements ArrayInterface
{
    /**#@+
     * History action values
     */
    const CREATED = 1;
    const UPDATED = 2;
    const USED = 3;
    const PARTIALLY_USED = 4; // Was used before version 1.1.0
    const EXPIRED = 5;
    const DEACTIVATED = 6;
    const ACTIVATED = 7;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::CREATED,
                'label' => __('Created')
            ],
            [
                'value' => self::UPDATED,
                'label' => __('Updated')
            ],
            [
                'value' => self::USED,
                'label' => __('Used')
            ],
            [
                'value' => self::EXPIRED,
                'label' => __('Expired')
            ],
            [
                'value' => self::DEACTIVATED,
                'label'=> __('Deactivated')
            ],
            [
                'value' => self::ACTIVATED,
                'label'=> __('Activated')
            ]
        ];
    }
}
