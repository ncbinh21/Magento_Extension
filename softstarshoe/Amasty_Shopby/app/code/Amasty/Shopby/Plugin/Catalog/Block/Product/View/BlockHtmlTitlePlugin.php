<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Catalog\Block\Product\View;

use Amasty\ShopbyBase\Helper\FilterSetting as FilterHelper;
use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Amasty\ShopbyBase\Model\OptionSetting;
use Amasty\ShopbyBase\Model\FilterSetting;
use Amasty\ShopbyBase\Plugin\Catalog\Block\Product\View\BlockHtmlTitlePluginAbstract;

class BlockHtmlTitlePlugin extends BlockHtmlTitlePluginAbstract
{
    /**
     * @return array
     */
    protected function getAttributeCodes()
    {
        $filtersToShow = $this->filterCollection
            ->addFieldToSelect(OptionSetting::FILTER_CODE)
            ->addFieldToFilter(FilterSettingInterface::SHOW_ICONS_ON_PRODUCT, true);
        $attributeCodes = [];
        foreach ($filtersToShow as $filter) {
            /** @var FilterSetting $filter */
            $attributeCode = substr($filter->getFilterCode(), strlen(FilterHelper::ATTR_PREFIX));
            if ($this->attributeConfig->canBeConfigured($attributeCode)) {
                $attributeCodes[] = $attributeCode;
            }


        }
        return $attributeCodes;
    }
}
