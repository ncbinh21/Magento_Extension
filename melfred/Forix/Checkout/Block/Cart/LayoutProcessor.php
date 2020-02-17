<?php

namespace Forix\Checkout\Block\Cart;

class LayoutProcessor extends \Magento\Checkout\Block\Cart\LayoutProcessor
{
    /**
     * Show City in Shipping Estimation
     *
     * @return bool
     * @codeCoverageIgnore
     */
    protected function isCityActive()
    {
        return true;
    }
}
