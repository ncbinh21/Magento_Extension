<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Source;

class Append implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'before',
                'label' => __('Before Attribute Text')
            ),
            array(
                'value' => 'after',
                'label' => __('After Attribute Text')
            )
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return ['before' => __('Before Attribute Text'),
                'after'  => __('After Attribute Text')
        ];
    }
}