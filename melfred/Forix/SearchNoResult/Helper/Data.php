<?php

namespace Forix\SearchNoResult\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue("search_no_results/settings/is_active", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getConfigValue($value)
    {
        return $this->scopeConfig->getValue('search_no_results/settings/' . $value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

}