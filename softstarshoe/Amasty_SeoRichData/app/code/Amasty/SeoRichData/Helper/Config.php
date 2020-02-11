<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_SeoRichData
 */


namespace Amasty\SeoRichData\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    private function getConfigValue($settingPath)
    {
        $value = $this->scopeConfig->getValue(
            'amseorichdata/' . $settingPath,
            ScopeInterface::SCOPE_STORE
        );

        return $value;
    }

    public function forProductEnabled()
    {
        return $this->getConfigValue('product/enabled');
    }

    public function getProductDescriptionMode()
    {
        return $this->getConfigValue('product/description');
    }

    public function showConfigurable()
    {
        return $this->getConfigValue('product/configurable');
    }

    public function showGrouped()
    {
        return $this->getConfigValue('product/grouped');
    }

    public function showAvailability()
    {
        return $this->getConfigValue('product/availability');
    }

    public function showCondition()
    {
        return $this->getConfigValue('product/condition');
    }

    public function showRating()
    {
        return $this->getConfigValue('product/rating');
    }

    public function isBreadcrumbsEnabled()
    {
        return $this->getConfigValue('breadcrumbs/enabled');
    }

    public function forWebsiteEnabled()
    {
        return $this->getConfigValue('website/enabled');
    }

    public function getWebsiteName()
    {
        return $this->getConfigValue('website/name');
    }

    public function forOrganizationEnabled()
    {
        return $this->getConfigValue('organization/enabled');
    }

    public function getOrganizationName()
    {
        return $this->getConfigValue('organization/name');
    }

    public function getOrganizationLogo()
    {
        return $this->getConfigValue('organization/logo_url');
    }

    public function forCategoryEnabled()
    {
        return $this->getConfigValue('category/enabled');
    }

    public function getCategoryDescription()
    {
        return $this->getConfigValue('category/description');
    }

    public function forSearchEnabled()
    {
        return $this->getConfigValue('search/enabled');
    }
}

