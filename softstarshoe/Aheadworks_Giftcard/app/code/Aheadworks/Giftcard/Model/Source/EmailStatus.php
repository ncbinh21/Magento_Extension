<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Model\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class EmailStatus
 *
 * @package Aheadworks\Giftcard\Model\Source
 */
class EmailStatus implements ArrayInterface
{
    /**#@+
     * Email Status action values
     */
    const SENT = 1;
    const AWAITING = 2;
    const NOT_SEND = 3;
    const FAILED = 4;
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SENT,
                'label' => __('Sent')
            ],
            [
                'value' => self::AWAITING,
                'label' => __('Awaiting')
            ],
            [
                'value' => self::NOT_SEND,
                'label' => __('Not Send')
            ],
            [
                'value' => self::FAILED,
                'label' => __('Failed')
            ]
        ];
    }

    /**
     * Retrieve option by value
     *
     * @param int $value
     * @return string|null
     */
    public function getOptionByValue($value)
    {
        $options = $this->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $value) {
                return $option['label'];
            }
        }
        return null;
    }
}
