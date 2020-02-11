<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Customer\Block\Account;

class AuthorizationLinkCustom extends \Magento\Customer\Block\Account\AuthorizationLink
{
    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->isLoggedIn() ? __('Sign Out') : __('Login');
    }
}
