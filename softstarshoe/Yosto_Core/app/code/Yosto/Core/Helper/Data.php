<?php
/**
 * Copyright © 2017 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

namespace Yosto\Core\Helper;


use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Data
 * @package Yosto\Core\Helper
 */
class Data extends AbstractHelper
{

    /**
     * @param $configPath
     * @param null $storeId
     * @return mixed
     */
    public function getConfig($configPath, $storeId = null) {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function getEmailSender($path)
    {
        return $this->getConfig($path);
    }

    public function getEmailTemplate($path)
    {
        return $this->getConfig($path);
    }
    public function getSystemEmailName($senderName)
    {
        return $this->getConfig("trans_email/ident_$senderName/name");
    }

    public function getSystemEmailBySenderName($senderName)
    {
        return $this->getConfig("trans_email/ident_$senderName/email");
    }
}