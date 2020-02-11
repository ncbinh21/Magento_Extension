<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Paction
 */
namespace Amasty\Paction\Model\Source;

class Rounding implements \Magento\Framework\Option\ArrayInterface
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
                'value' => 'fixed',
                'label' => __('To specific value')
            ),
            array(
                'value' => 'math',
                'label' => __('By rules of mathematical rounding')
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
        return ['fixed' => __('To specific value'),
                'math'  => __('By rules of mathematical rounding')
        ];
    }
}