<?php

namespace Forix\Checkout\Block\Onepage;

class Success extends \Magento\Checkout\Block\Onepage\Success
{
    public function getCurrentOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}