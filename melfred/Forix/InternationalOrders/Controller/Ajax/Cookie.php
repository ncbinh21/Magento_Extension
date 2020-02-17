<?php

namespace Forix\InternationalOrders\Controller\Ajax;

use Forix\InternationalOrders\CustomerData\CustomerInfo;

class Cookie extends \Magento\Framework\App\Action\Action

{
    public function execute()
    {
        $domesticValue = $this->getRequest()->getParam('domestic-value');
        if(null != $domesticValue) {
            setcookie(CustomerInfo::DOMESTIC_COOKIE, $domesticValue, time() + (86400 * 30), '/');
        }
    }
}

