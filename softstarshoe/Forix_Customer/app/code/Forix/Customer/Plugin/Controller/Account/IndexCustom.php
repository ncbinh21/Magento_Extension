<?php

namespace Forix\Customer\Plugin\Controller\Account;

class IndexCustom
{
    public function afterExecute(\Magento\Customer\Controller\Account\Index $subject, $resultPage)
    {
        $resultPage->getConfig()->getTitle()->set(__('Your Account - Softar Shoes'));
        return $resultPage;
    }
}