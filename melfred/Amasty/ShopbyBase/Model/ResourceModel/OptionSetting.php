<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Model\ResourceModel;

class OptionSetting extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * OptionSetting protected constructor
     */
    protected function _construct()
    {
        $this->_init('amasty_amshopby_option_setting', 'option_setting_id');
    }
}
