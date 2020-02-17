<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Block\Navigation\Widget;

interface WidgetInterface
{
    public function setFilterSetting(\Amasty\ShopbyBase\Api\Data\FilterSettingInterface $filterSetting);
    public function getFilterSetting();
}
