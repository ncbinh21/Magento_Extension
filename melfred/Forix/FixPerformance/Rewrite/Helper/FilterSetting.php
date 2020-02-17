<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2 - EE - Melfredborzall
 * Date: 08/05/2019
 * Time: 11:47
 */
namespace Forix\FixPerformance\Rewrite\Helper;

class FilterSetting extends \Amasty\ShopbyBase\Helper\FilterSetting
{
    protected $_settings = [];

    public function getSettingByAttributeCode($attributeCode)
    {
        if(!isset($this->_settings[$attributeCode])){
            $this->_settings[$attributeCode] = parent::getSettingByAttributeCode($attributeCode);
        }
        return $this->_settings[$attributeCode];
    }
}