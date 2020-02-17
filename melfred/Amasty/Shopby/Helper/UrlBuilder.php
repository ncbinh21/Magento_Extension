<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Helper;

use Amasty\Shopby\Model\Layer\Filter\Price;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Amasty\Shopby\Model\Layer\Filter\Category as CategoryFilter;
use Magento\Catalog\Model\Layer\Filter\FilterInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Registry;
use Amasty\Shopby\Model\Source\DisplayMode;

class UrlBuilder extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    private $filterSettingHelper;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var \Magento\Framework\Url\QueryParamsResolverInterface
     */
    private $queryParamsResolver;

    /**
     * @var
     */
    private $filter;

    /**
     * @var Category
     */
    private $categoryHelper;

    public function __construct(
        Context $context,
        Registry $registry,
        CategoryRepositoryInterface $categoryRepository,
        \Amasty\Shopby\Helper\FilterSetting $filterSettingHelper,
        \Magento\Framework\Url\QueryParamsResolverInterface $queryParamsResolver,
        Category $categoryHelper
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->categoryRepository = $categoryRepository;
        $this->filterSettingHelper = $filterSettingHelper;
        $this->queryParamsResolver = $queryParamsResolver;
        $this->categoryHelper = $categoryHelper;
    }

    /**
     * @param FilterInterface $filter
     * @param string|array $optionValue
     * @return string
     */
    public function buildUrl(FilterInterface $filter, $optionValue)
    {
        $this->filter = $filter;
        if ($this->isGetDefaultUrl($filter)) {
            $category = $this->categoryRepository->get($optionValue);
            if ($category) {
                return $category->getUrl();
            }
        }

        if ($filter instanceof Price && is_array($optionValue)) {
            $optionValue = implode('-', $optionValue);
        }

        $currentValues = $this->getCurrentValues();
        $resultValue = $this->calculateResultValue($optionValue, $currentValues);

        $query = $this->buildQuery($filter, $resultValue);

        $query['shopbyAjax'] = null;
        $query['_'] = null;

        $params = ['_current' => true, '_use_rewrite' => true, '_query' => $query];
        //fix urls like catalogsearch/result/index/price/10-20/?price=10-60&q=bag
        $params['price'] = null;

        return $this->_urlBuilder->getUrl('*/*/*', $params);
    }

    /**
     * @param FilterInterface $filter
     * @return bool
     */
    public function isGetDefaultUrl($filter)
    {
        return $filter instanceof CategoryFilter
            && !$filter->isMultiselect()
            && $this->_request->getFullActionName() != 'catalogsearch_result_index';
    }

    /**
     * @param $route
     * @param array $params
     * @return string
     */
    public function getUrl($route, $params = [])
    {
        return parent::_getUrl($route, $params);
    }

    /**
     * @param FilterInterface $filter
     * @param $resultValue
     * @return array|mixed
     */
    public function buildQuery(FilterInterface $filter, $resultValue)
    {
        $query = $this->registry->registry('amasty_shopby_seo_parsed_params');
        if (!is_array($query)) {
            $query = [];
        }
        $query[$filter->getRequestVar()] = $resultValue;

        return $query;
    }

    /**
     * @return array
     */
    protected function getCurrentValues()
    {
        $sliderOrFromTo = DisplayMode::MODE_FROM_TO_ONLY || DisplayMode::MODE_SLIDER;
        $useBasePrice = !$sliderOrFromTo
            && $this->filter->getRequestVar() == \Magento\Catalog\Api\Data\ProductAttributeInterface::CODE_PRICE;
        $filterCode = $useBasePrice
            ? \Amasty\Shopby\Model\Layer\Filter\Price::AM_BASE_PRICE
            : $this->filter->getRequestVar();
        $data = $this->_request->getParam($filterCode);

        if (empty($data)) {
            $params = $this->_request->getParams();
            $data = isset($params['amshopby'][$filterCode]) ? $params['amshopby'][$filterCode] : null;
        }

        if (!empty($data)) {
            $values = is_array($data) ? $data : explode(',', $data);
            foreach ($values as $key => $val) {
                if (empty($val)) {
                    unset($values[$key]);
                }
            }
        } else {
            $values = [];
        }

        return $values;
    }

    /**
     * @param $optionValue
     * @param array $currentValues
     * @return string|null
     */
    protected function calculateResultValue($optionValue, array $currentValues)
    {
        if ($optionValue === null || is_array($optionValue)) {
            return null;
        }
        $key = array_search($optionValue, $currentValues);

        if ($this->isMultiselectAllowed()) {
            $result = $currentValues;
            if ($key !== false) {
                unset($result[$key]);
            } else {
                if ($this->filter instanceof CategoryFilter && $this->categoryHelper->isCategoryFilterExtended()) {
                    $parents = $this->filter->getItems()->getParentsAndChildrenByItemId($optionValue);
                    $result = array_diff($result, $parents);
                }
                $result[] = $optionValue;
            }
        } else {
            if ($key !== false) {
                $result = [];
            } else {
                $result = [$optionValue];
            }
        }

        $value = $result ? implode(',', $result) : null;
        return $value;
    }

    /**
     * @return bool
     */
    protected function isMultiselectAllowed()
    {
        $setting = $this->filterSettingHelper->getSettingByLayerFilter($this->filter);
        return $setting->isMultiselect();
    }
}
