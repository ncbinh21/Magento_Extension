<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Model\Source\Category;

use Magento\Framework\Option\ArrayInterface;

class Description implements ArrayInterface
{
    const NONE = 0;

    const CATEGORY_DESCRIPTION = 1;
    const META_DESCRIPTION = 2;

    public function toOptionArray()
    {
        return [
            self::NONE => __('None'),
            self::CATEGORY_DESCRIPTION => __('Category Full Description'),
            self::META_DESCRIPTION => __('Page Meta Description'),
        ];
    }
}
