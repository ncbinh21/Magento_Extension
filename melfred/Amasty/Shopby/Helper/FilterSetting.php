<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Helper;

use Magento\Framework\App\Helper\Context;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory;
use Amasty\ShopbySeo\Model\Source\IndexMode;
use Amasty\ShopbySeo\Model\Source\RelNofollow;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;

class FilterSetting extends \Amasty\ShopbyBase\Helper\FilterSetting
{
    /**
     * @var \Magento\Framework\View\Element\BlockFactory
     */
    private $blockFactory;

    public function __construct(
        Context $context,
        CollectionFactory $settingCollectionFactory,
        \Amasty\ShopbyBase\Model\FilterSettingFactory $settingFactory,
        \Amasty\ShopbyBase\Model\FilterSettingRepository $settingRepository,
        \Magento\Framework\View\Element\BlockFactory $blockFactory
    ) {
        parent::__construct($context, $settingCollectionFactory, $settingFactory, $settingRepository);
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param FilterInterface $layerFilter
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getSettingByLayerFilter(FilterInterface $layerFilter)
    {
        $filterCode = $this->getFilterCode($layerFilter);
        $setting =  $this->getFilterSettingByCode($filterCode);

        if ($setting === null) {
            $data = [FilterSettingInterface::FILTER_CODE => $filterCode];
            if ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Stock) {
                $data = $this->getDataByCustomFilter('stock');
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Rating) {
                $data = $this->getDataByCustomFilter('rating');
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\IsNew) {
                $data = $this->getDataByCustomFilter('am_is_new');
            } elseif ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\OnSale) {
                $data = $this->getDataByCustomFilter('am_on_sale');
            }
            $setting = $this->settingFactory->create(['data' => $data]);
            $setting->setIndexMode(IndexMode::MODE_NEVER);
            $setting->setFollowMode(IndexMode::MODE_NEVER);
            $setting->setRelNofollow(RelNofollow::MODE_AUTO);
        }

        if ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Category) {
            $setting->addData($this->getCustomDataForCategoryFilter());
        }

        $setting->setAttributeModel($layerFilter->getData('attribute_model'));

        return $setting;
    }

    /**
     * @param $attributeCode
     *
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getSettingByAttributeCode($attributeCode)
    {
        $filterCode = self::ATTR_PREFIX . $attributeCode;
        $setting =  $this->getFilterSettingByCode($filterCode);
        if ($setting === null) {
            $data = [FilterSettingInterface::FILTER_CODE => $filterCode];
            $setting = $this->settingFactory->create(['data' => $data]);
        }

        if ($attributeCode == \Amasty\Shopby\Helper\Category::ATTRIBUTE_CODE) {
            $setting->addData($this->getCustomDataForCategoryFilter());
        }

        return $setting;
    }

    protected function getFilterCode(FilterInterface $layerFilter)
    {
        if ($layerFilter instanceof \Amasty\Shopby\Model\Layer\Filter\Category) {
            return self::ATTR_PREFIX . \Amasty\Shopby\Helper\Category::ATTRIBUTE_CODE;
        }

        $attribute = $layerFilter->getData('attribute_model');
        return is_object($attribute) ? self::ATTR_PREFIX . $attribute->getAttributeCode() : null;
    }

    /**
     * @return string
     */
    public function getShowMoreButtonBlock($setting)
    {
        return $this->blockFactory->createBlock(\Amasty\Shopby\Block\Navigation\Widget\HideMoreOptions::class)
            ->setFilterSetting($setting);
    }
}
