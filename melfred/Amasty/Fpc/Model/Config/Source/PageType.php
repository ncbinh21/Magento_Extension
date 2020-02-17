<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model\Config\Source;

class PageType implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_INDEX    = 'index';
    const TYPE_CMS      = 'cms';
    const TYPE_PRODUCT  = 'product';
    const TYPE_CATEGORY = 'category';

    public function toOptionArray()
    {
        $options = [
            [
                'label' => __('Home Page'),
                'value' => self::TYPE_INDEX
            ],
            [
                'label' => __('CMS pages'),
                'value' => self::TYPE_CMS
            ],
            [
                'label' => __('Product pages'),
                'value' => self::TYPE_PRODUCT
            ],
            [
                'label' => __('Category pages'),
                'value' => self::TYPE_CATEGORY
            ],
        ];

        return $options;
    }

    public function toArray()
    {
        $options = $this->toOptionArray();

        $result = array_combine(
            array_column($options, 'value'),
            array_column($options, 'label')
        );

        return $result;
    }
}
