<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Helper;

use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\Context;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\Collection;
use Amasty\ShopbyBase\Model\ResourceModel\FilterSetting\CollectionFactory;
use Amasty\ShopbyBase\Api\Data\FilterSettingInterface;
use Magento\Store\Model\ScopeInterface;

class FilterSetting extends \Magento\Framework\App\Helper\AbstractHelper
{
    const ATTR_PREFIX = 'attr_';

    /**
     * @var  Collection
     */
    protected $collection;

    /**
     * @var  \Amasty\ShopbyBase\Model\FilterSettingFactory
     */
    protected $settingFactory;

    /**
     * FilterSetting constructor.
     * @param Context $context
     * @param CollectionFactory $settingCollectionFactory
     * @param \Amasty\ShopbyBase\Model\FilterSettingFactory $settingFactory
     */
    public function __construct(
        Context $context,
        CollectionFactory $settingCollectionFactory,
        \Amasty\ShopbyBase\Model\FilterSettingFactory $settingFactory
    ) {
        parent::__construct($context);
        $this->collection = $settingCollectionFactory->create();
        $this->settingFactory = $settingFactory;
    }

    /**
     * @param FilterInterface $layerFilter
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getSettingByLayerFilter(FilterInterface $layerFilter)
    {
        $filterCode = $this->getFilterCode($layerFilter);

        $setting = null;
        if (isset($filterCode)) {
            $setting = $this->collection->getItemByColumnValue(
                \Amasty\ShopbyBase\Model\FilterSetting::FILTER_CODE,
                $filterCode
            );
        }
        if ($setting === null) {
            $data = [FilterSettingInterface::FILTER_CODE=>$filterCode];
            $setting = $this->settingFactory->create(['data'=>$data]);
        }

        $setting->setAttributeModel($layerFilter->getData('attribute_model'));

        return $setting;
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute $attributeModel
     *
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getSettingByAttribute($attributeModel)
    {
        return $this->getSettingByAttributeCode($attributeModel->getAttributeCode());
    }

    /**
     * @param string $attributeCode
     *
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getSettingByAttributeCode($attributeCode)
    {
        $filterCode = self::ATTR_PREFIX . $attributeCode;
        $setting = $this->collection->getItemByColumnValue(\Amasty\ShopbyBase\Model\FilterSetting::FILTER_CODE, $filterCode);
        if ($setting === null) {
            $data = [FilterSettingInterface::FILTER_CODE=>$filterCode];
            $setting = $this->settingFactory->create(['data'=>$data]);
        }
        return $setting;
    }

    /**
     * @param FilterInterface $layerFilter
     * @return null|string
     */
    protected function getFilterCode(FilterInterface $layerFilter)
    {
        $attribute = $layerFilter->getData('attribute_model');
        return is_object($attribute) ? self::ATTR_PREFIX . $attribute->getAttributeCode() : null;
    }

    /**
     * @param string $filterName
     * @return array
     */
    protected function getDataByCustomFilter($filterName)
    {
        $data = [];
        $data[FilterSettingInterface::FILTER_SETTING_ID] = $filterName;
        $data[FilterSettingInterface::DISPLAY_MODE] = $this->getConfig($filterName, 'display_mode');
        $data[FilterSettingInterface::FILTER_CODE] = $filterName;
        $data[FilterSettingInterface::IS_EXPANDED] = $this->getConfig($filterName, 'is_expanded');
        $data[FilterSettingInterface::TOOLTIP] = $this->getConfig($filterName, 'tooltip');
        $data[FilterSettingInterface::BLOCK_POSITION] = $this->getConfig($filterName, 'block_position');
        return $data;
    }

    /**
     * @param string $filterName
     * @param string $configName
     * @return string
     */
    protected function getConfig($filterName, $configName)
    {
        return $this->scopeConfig->getValue(
            'amshopby/' . $filterName . '_filter/' . $configName,
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return array
     */
    public function getCustomDataForCategoryFilter()
    {
        $data = [];
        foreach ($this->getKeyValueForCategoryFilterConfig() as $key => $value) {
            $data[$key] = $this->scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES);
        }
        return $data;
    }

    /**
     * @return array
     */
    public function getKeyValueForCategoryFilterConfig()
    {
        return [
            'category_tree_depth'           => 'amshopby/category_filter/category_tree_depth',
            'subcategories_view'            => 'amshopby/category_filter/subcategories_view',
            'subcategories_expand'          => 'amshopby/category_filter/subcategories_expand',
            'render_all_categories_tree'    => 'amshopby/category_filter/render_all_categories_tree',
            'render_categories_level'       => 'amshopby/category_filter/render_categories_level',
        ];
    }
}
