<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Model\Source\Product;

use Magento\Framework\Option\ArrayInterface;

class Description implements ArrayInterface
{
    const NONE = 0;

    const SHORT_DESCRIPTION = 1;
    const FULL_DESCRIPTION = 2;
    const META_DESCRIPTION = 3;

    public function toOptionArray()
    {
        return [
            self::NONE => __('None'),
            self::SHORT_DESCRIPTION => __('Product Short Description'),
            self::FULL_DESCRIPTION => __('Product Full Description'),
            self::META_DESCRIPTION => __('Page Meta Description'),
        ];
    }
}
