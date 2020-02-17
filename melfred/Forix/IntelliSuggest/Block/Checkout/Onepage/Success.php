<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\IntelliSuggest\Block\Checkout\Onepage;


class Success extends \Magento\Checkout\Block\Onepage\Success
{
    public function getCurrentOrder()
    {
        return $this->_checkoutSession->getLastRealOrder();
    }
}