<?php
/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

namespace Aheadworks\Giftcard\Block\Checkout\Cart;

use Magento\Framework\View\Element\Template;

/**
 * Class Giftcard
 *
 * @package Aheadworks\Giftcard\Block\Checkout\Cart
 */
class Giftcard extends Template
{
    /**
     * Retrieve action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('awgiftcard/cart/apply');
    }

    /**
     * Retrieve check Gift Card code url
     *
     * @return string
     */
    public function getCheckCodeUrl()
    {
        return $this->getUrl('awgiftcard/card/checkCode');
    }
}
