<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Source;

class Relate implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 0,
                'label' => __('Default')
            ),
            array(
                'value' => 1,
                'label' => __('2 Way')
            ),
            array(
                'value' => 2,
                'label' => __('Multi Way')
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
        return [0 => __('Default'),
                1 => __('2 Way'),
                2 => __('Multi Way')
        ];
    }
}