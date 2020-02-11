<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Plugin\Block\Wishlist;

use Magento\Framework\View\Element\Template;

/**
 * Class Plugin
 *
 * @package Aheadworks\Giftcard\Plugin\Block\Wishlist
 */
class OptionsPlugin
{
    /**
     * Add Gift Cart options to wishlist widget
     *
     * @param Template $subject
     * @param [] $result
     * @return []
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetWishlistOptions(Template $subject, $result)
    {
        return array_merge($result, ['aw_giftcardInfo' => '[name^=aw_gc_]']);
    }
}
