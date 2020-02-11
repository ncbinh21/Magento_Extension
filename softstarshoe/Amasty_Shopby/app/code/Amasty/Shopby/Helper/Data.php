<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Helper;

use Magento\Catalog\Model\Layer;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Amasty\Shopby;
use Magento\Store\Model\ScopeInterface;

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
     * Data constructor.
     * @param Context $context
     * @param FilterSetting $settingHelper
     * @param Layer\Resolver $layerResolver
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Shopby\Model\Request $shopbyRequest
     * @param Shopby\Model\Layer\FilterList $filterList
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        Context $context,
        FilterSetting $settingHelper,
        Layer\Resolver $layerResolver,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        Shopby\Model\Layer\FilterList $filterList,
        \Magento\Framework\Registry $registry
    ) {
        parent::__construct($context);
        $this->settingHelper = $settingHelper;
        $this->layer = $layerResolver->get();
        $this->storeManager = $storeManager;
        $this->shopbyRequest = $shopbyRequest;
        $this->filterList = $filterList;
        $this->registry = $registry;
    }

    public function getSelectedFiltersSettings()
    {
        $filters = $this->filterList->getFilters($this->layer);
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
        return $this->scopeConfig->isSetFlag('amshopby/general/ajax_enabled', ScopeInterface::SCOPE_STORE);
    }

    public function getApplyButtonPosition()
    {
        return $this->scopeConfig->getValue('amshopby/general/apply_button_position', ScopeInterface::SCOPE_STORE);
    }

    public function getSubmitFiltersMode()
    {
        return $this->scopeConfig->getValue(
            \Amasty\Shopby\Block\Navigation\RendererInterface::XML_CONFIG_SUBMIT_FILTER,
            ScopeInterface::SCOPE_STORE
        );
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
        if ($this->registry->registry('amasty_shopby_seo_parsed_params')) {
            foreach ($this->registry->registry('amasty_shopby_seo_parsed_params') as $key => $item) {
                $filterState[$key] = $item;
            }
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
}
