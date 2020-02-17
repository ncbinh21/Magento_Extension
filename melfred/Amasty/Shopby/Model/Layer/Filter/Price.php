<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;

use Magento\Framework\Exception\StateException;
use Magento\Search\Model\SearchEngine;
use Amasty\Shopby\Model\Layer\Filter\Traits\FromToDecimal;
use Amasty\Shopby\Model\Source\DisplayMode;
use Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory;
use Amasty\Shopby\Api\Data\FromToFilterInterface;

class Price extends \Magento\CatalogSearch\Model\Layer\Filter\Price implements FromToFilterInterface
{
    const NUMBERS_AFTER_POINT = 4;

    const AM_BASE_PRICE = 'am_base_price';

    use FromToDecimal;

    /**
     * @var \Amasty\Shopby\Helper\FilterSetting
     */
    private $settingHelper;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var string
     */
    private $currencySymbol;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Price
     */
    private $dataProvider;

    /**
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    private $aggregationAdapter;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Framework\Registry|null
     */
    private $_coreRegistry = null;

    /**
     * @var \Amasty\Shopby\Helper\Group
     */
    private $groupHelper;

    /**
     * @var SearchEngine
     */
    private $searchEngine;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    public function __construct(
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Layer\Filter\Price $resource,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Search\Dynamic\Algorithm $priceAlgorithm,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Catalog\Model\Layer\Filter\Dynamic\AlgorithmFactory $algorithmFactory,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\PriceFactory $dataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Amasty\Shopby\Helper\Group $groupHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        SearchEngine $searchEngine,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->settingHelper = $settingHelper;
        $this->currencySymbol = $priceCurrency->getCurrencySymbol();
        $this->dataProvider = $dataProviderFactory->create(['layer' => $layer]);
        $this->aggregationAdapter = $aggregationAdapter;
        $this->shopbyRequest = $shopbyRequest;
        $this->groupHelper = $groupHelper;
        $this->scopeConfig = $scopeConfig;
        $this->priceCurrency = $priceCurrency;
        $this->searchEngine = $searchEngine;
        $this->messageManager = $messageManager;
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $resource,
            $customerSession,
            $priceAlgorithm,
            $priceCurrency,
            $algorithmFactory,
            $dataProviderFactory,
            $data
        );
    }

    /**
     * @return array
     */
    public function getFromToConfig()
    {
        return $this->getConfig('price');
    }

    /**
     * @return int
     */
    public function getItemsCount()
    {
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        $ignoreRanges = $filterSetting->getDisplayMode() == DisplayMode::MODE_FROM_TO_ONLY
            || $filterSetting->getDisplayMode() == DisplayMode::MODE_SLIDER;
        $itemsCount = $ignoreRanges ? 0 : parent::getItemsCount();
        if ($itemsCount == 0) {
            /**
             * show up filter event don't have any option
             */
            $fromToConfig = $this->getFromToConfig();
            if ($fromToConfig && $fromToConfig['min'] != $fromToConfig['max']) {
                return 1;
            }
        }

        return $itemsCount;
    }

    /**
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $facets = $this->getFacetedData();

        $data = [];
        if (count($facets) > 1) { // two range minimum
            foreach ($facets as $key => $aggregation) {
                $count = $aggregation['count'];
                if (strpos($key, '_') === false) {
                    continue;
                }
                $data[] = $this->prepareData($key, $count, $data);
            }
        }

        if (count($this->getFromToConfig()) && count($data) == 1) {
            $data = [];
        }

        return $data;
    }

    /**
     * @param string $key
     * @param int $count
     * @return array
     */
    private function prepareData($key, $count)
    {
        list($from, $to) = explode('_', $key);
        if ($from == '*') {
            $from = $this->getFrom($to);
        }
        if ($to == '*') {
            $to = '';
        }

        $label = $this->_renderRangeLabel(
            empty($from) ? 0 : $from,
            $to ? $to - 0.01 : $to
        );
        $value = $from . '-' . $to . $this->dataProvider->getAdditionalRequestData();
        $data = [
            'label' => $label,
            'value' => $value,
            'count' => $count,
            'from' => $from,
            'to' => $to,
        ];

        return $data;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->isApplied()) {
            return $this;
        }

        $filter = $this->shopbyRequest->getFilterParam($this);
        $noValidate = 0;
        $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
        $isSlider = $filterSetting->getDisplayMode() == DisplayMode::MODE_SLIDER;

        if (!empty($filter) && !is_array($filter)) {
            $copyFilter = $filter;
            $filter = explode('-', $filter);

            $toValue = isset($filter[1]) && $filter[1] ? $filter[1] : '';
            $filter = $filter[0] . '-' . $toValue;
            $validateFilter = $this->dataProvider->validateFilter($copyFilter);

            if (!$validateFilter) {
                $noValidate = 1;
            } else {
                $this->setFromTo($validateFilter[0], $validateFilter[1]);
            }
        }

        $request->setParam($this->getRequestVar(), $filter);
        $request->setPostValue(self::AM_BASE_PRICE, isset($copyFilter) ? $copyFilter : $filter);
        $apply = parent::apply($request);

        if ($noValidate) {
            return $this;
        }

        if (!empty($filter) && !is_array($filter)) {
            if ($isSlider) {
                $this->getLayer()->getProductCollection()->addFieldToFilter('price', $filter);
            }
            if ($this->groupHelper->getAttributeGroupsValues($this->getAttributeModel()->getAttributeId())) {
                $this->getLayer()->getProductCollection()->addFieldToFilter(
                    'price',
                    [
                        'from' => $this->getCurrentFrom(),
                        'to' => $this->getCurrentTo()
                    ]
                );
            }
        }

        return $apply;
    }

    /**
     * @return \Magento\Framework\Search\Response\QueryResponse|\Magento\Framework\Search\ResponseInterface|null
     */
    private function getAlteredQueryResponse()
    {
        $alteredQueryResponse = null;
        if ($this->hasCurrentValue()) {
            $productCollection = $this->getLayer()->getProductCollection();
            $requestBuilder = clone $productCollection->getMemRequestBuilder();
            $filterSetting = $this->settingHelper->getSettingByLayerFilter($this);
            $rangeCalculation = $this->scopeConfig->getValue(AlgorithmFactory::XML_PATH_RANGE_CALCULATION);
            if ($rangeCalculation != AlgorithmFactory::RANGE_CALCULATION_IMPROVED || $this->isUnical($filterSetting)) {
                $requestBuilder->removePlaceholder($this->getAttributeModel()->getAttributeCode() . '.from');
                $requestBuilder->removePlaceholder($this->getAttributeModel()->getAttributeCode() . '.to');
            }
            $queryRequest = $requestBuilder->create();
            $alteredQueryResponse = $this->searchEngine->search($queryRequest);
        }

        return $alteredQueryResponse;
    }

    /**
     * @return mixed
     */
    private function getFacetedData()
    {
        if ($this->_coreRegistry->registry('price_facets') === null) {
            /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
            $productCollection = $this->getLayer()->getProductCollection();
            $alteredQueryResponse = $this->getAlteredQueryResponse();
            try {
                $facets = $productCollection->getFacetedData(
                    $this->getAttributeModel()->getAttributeCode(),
                    $alteredQueryResponse
                );
            } catch (StateException $e) {
                if (!$this->messageManager->hasMessages()) {
                    $this->messageManager->addErrorMessage(
                        __('Make sure that "%1" attribute can be used in layered navigation',
                            $this->getAttributeModel()->getAttributeCode()
                        )
                    );
                }
                $facets = [];
            }

            $this->_coreRegistry->register('price_facets', $facets);
        }

        return $this->_coreRegistry->registry('price_facets');
    }

    /**
     * @param $filterSetting
     * @return bool
     */
    public function isUnical($filterSetting)
    {
        return ($filterSetting->getDisplayMode() == \Amasty\Shopby\Model\Source\DisplayMode::MODE_SLIDER ||
        $filterSetting->getDisplayMode() == \Amasty\Shopby\Model\Source\DisplayMode::MODE_FROM_TO_ONLY ||
        $filterSetting->getAddFromToWidget() === '1');
    }

    /**
     * @param float|string $fromPrice
     * @param float|string $toPrice
     * @return float|\Magento\Framework\Phrase
     */
    protected function _renderRangeLabel($fromPrice, $toPrice)
    {
        $fromPrice = round($fromPrice * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT);
        if (!$toPrice) {
            $toPrice = 0;
        }
        if ($this->getCurrencyRate() != 1.0) {
            $toPrice = round($toPrice * $this->getCurrencyRate(), self::NUMBERS_AFTER_POINT);
        }

        $ranges = $this->groupHelper->getRanges($this->getAttributeModel()->getAttributeId());
        if ($ranges) {
            if (isset($ranges[$fromPrice . '-' . $toPrice])) {
                return __($ranges[$fromPrice . '-' . $toPrice]);
            }
        }
        $formattedFromPrice = $this->priceCurrency->format($fromPrice);
        if (!$toPrice) {
            return __('%1 and above', $formattedFromPrice);
        } elseif ($fromPrice == $toPrice && $this->dataProvider->getOnePriceIntervalValue()) {
            return $formattedFromPrice;
        } else {
            return __('%1 - %2', $formattedFromPrice, $this->priceCurrency->format($toPrice));
        }
    }
}
