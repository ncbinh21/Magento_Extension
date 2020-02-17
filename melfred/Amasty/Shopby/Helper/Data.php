<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Helper;

use Magento\Catalog\Model\Layer;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Amasty\Shopby;
use Magento\Store\Model\ScopeInterface;
use Amasty\ShopbyBase\Helper\OptionSetting as OptionSettingHelper;

class Data extends AbstractHelper
{
    /**
     * @var FilterSetting
     */
    protected $settingHelper;

    /**
     * @var  Layer
     */
    protected $layer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var Shopby\Model\Request
     */
    protected $shopbyRequest;

    /**
     * @var  Shopby\Model\Layer\FilterList
     */
    protected $filterList;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    private $swatchHelper;
    /**
     * @var OptionSettingHelper
     */
    private $optionSettingHelper;

    public function __construct(
        Context $context,
        FilterSetting $settingHelper,
        Layer\Resolver $layerResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        Shopby\Model\Layer\FilterList $filterList,
        \Magento\Swatches\Helper\Data $swatchHelper,
        OptionSettingHelper $optionSettingHelper,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->settingHelper = $settingHelper;
        $this->layer = $layerResolver->get();
        $this->storeManager = $storeManager;
        $this->shopbyRequest = $shopbyRequest;
        $this->filterList = $filterList;
        $this->registry = $registry;
        $this->swatchHelper = $swatchHelper;
        $this->optionSettingHelper = $optionSettingHelper;
    }

    public function getSelectedFiltersSettings()
    {
        $filters = $this->filterList->getAllFilters($this->layer);
        $result = [];
        foreach ($filters as $filter) {
            /** @var Layer\Filter\AbstractFilter $filter */
            $var = $filter->getRequestVar();
            if ($this->_getRequest()->getParam($var) !== null) {
                $setting = $this->settingHelper->getSettingByLayerFilter($filter);
                $result[] = [
                    'filter' => $filter,
                    'setting' => $setting,
                ];
            }
        }
        return $result;
    }

    public function isAjaxEnabled()
    {
        return $this->scopeConfig->isSetFlag('amshopby/general/ajax_enabled', ScopeInterface::SCOPE_STORE)
            || $this->collectFilters();
    }

    public function getTooltipUrl()
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $tooltipImage = $this->scopeConfig->getValue('amshopby/tooltips/image', ScopeInterface::SCOPE_STORE);
        if (empty($tooltipImage)) {
            return '';
        }
        return $baseUrl . $tooltipImage;
    }

    public function isFilterItemSelected(\Amasty\Shopby\Model\Layer\Filter\Item $filterItem)
    {
        $data = $this->shopbyRequest->getFilterParam($filterItem->getFilter());

        if (!empty($data)) {
            $ids = explode(',', $data);
            if (in_array($filterItem->getValue(), $ids)) {
                return 1;
            }
        }
        return 0;
    }

    /**
     * @param \Magento\Catalog\Model\Layer\Filter\Item[] $activeFilters
     * @return string
     */
    public function getAjaxCleanUrl($activeFilters)
    {
        $filterState = [];

        foreach ($activeFilters as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }

        $params['_current'] = true;
        $params['_use_rewrite'] = true;
        $params['_query'] = $filterState;
        $params['_escape'] = true;
        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    public function getCurrentCategory()
    {
        return $this->layer->getCurrentCategory();
    }

    /**
     * @return string|null
     */
    public function getThumbnailPlaceholder()
    {
        return $this->scopeConfig->getValue('catalog/category_placeholder/thumbnail');
    }

    /**
     * @return string
     */
    public function getSubmitFiltersDesktop()
    {
        return $this->scopeConfig->getValue('amshopby/general/submit_filters_on_desktop', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function getSubmitFiltersMobile()
    {
        return $this->scopeConfig->getValue('amshopby/general/submit_filters_on_mobile', ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return string
     */
    public function collectFilters()
    {
        $isMobile = isset($_SERVER['HTTP_USER_AGENT']) && stristr($_SERVER['HTTP_USER_AGENT'],'mobi') !== false;

        if ($isMobile) {
            $result = $this->getSubmitFiltersMobile();
        } else {
            $result = $this->getSubmitFiltersDesktop();
        }

        return $result;
    }

    /**
     * @param array $optionIds
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute
     * @return array
     */
    public function getSwatchesFromImages($optionIds, \Magento\Catalog\Model\ResourceModel\Eav\Attribute $attribute)
    {
        $swatches = [];
        if (!$this->swatchHelper->isVisualSwatch($attribute) && !$this->swatchHelper->isTextSwatch($attribute)) {
            foreach ($optionIds as $optionId) {
                $setting = $this->optionSettingHelper->getSettingByValue(
                    $optionId,
                    FilterSetting::ATTR_PREFIX . $attribute->getAttributeCode(),
                    $this->storeManager->getStore()->getId()
                );

                $swatches[$optionId] = [
                    'type' => 'option_image',
                    'value' => $setting->getSliderImageUrl()
                ];
            }
        }

        return $swatches;
    }
}
