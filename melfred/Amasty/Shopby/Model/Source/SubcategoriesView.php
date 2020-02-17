<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Model\Source;

class SubcategoriesView implements \Magento\Framework\Option\ArrayInterface
{
    const FOLDING = 1;
    const FLY_OUT = 2;
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::FOLDING,
                'label' => __('Folding')
            ],
            [
                'value' => self::FLY_OUT,
                'label' => __('Fly-out')
            ],
        ];
    }
}
