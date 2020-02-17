<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Block\Navigation;

use Magento\Catalog\Model\Layer\Filter\Item as FilterItem;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Eav\Model\Entity\Attribute;
use Amasty\Shopby\Helper\FilterSetting as FilterSettingHelper;
use Magento\Eav\Model\Entity\Attribute\Option;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Swatches\Block\LayeredNavigation\RenderLayered;

class SwatchRenderer extends RenderLayered implements RendererInterface
{
    const SWATCH_TYPE_OPTION_IMAGE = 'option_image';
    const VAR_COUNT = 'amasty_shopby_count';
    const VAR_SELECTED = 'amasty_shopby_selected';

    const FILTERABLE_NO_RESULTS = '2';

    /**
     * @var \Amasty\Shopby\Helper\UrlBuilder
     */
    private $urlBuilderHelper;

    /**
     * @var FilterSettingHelper
     */
    private $filterSettingHelper;

    /**
     * @var \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    private $filterSetting;

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $helper;

    /**
     * @var string
     */
    protected $_template = 'layer/filter/swatch/default.phtml';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Attribute $eavAttribute,
        AttributeFactory $layerAttribute,
        \Magento\Swatches\Helper\Data $swatchHelper,
        \Magento\Swatches\Helper\Media $mediaHelper,
        \Amasty\Shopby\Helper\UrlBuilder $urlBuilderHelper,
        \Amasty\Shopby\Helper\Data $helper,
        FilterSettingHelper $filterSettingHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $eavAttribute,
            $layerAttribute,
            $swatchHelper,
            $mediaHelper,
            $data
        );

        $this->filterSettingHelper = $filterSettingHelper;
        $this->helper = $helper;
        $this->urlBuilderHelper = $urlBuilderHelper;
    }

    /**
     * @param string $attributeCode
     * @param int $optionId
     * @return string
     */
    public function buildUrl($attributeCode, $optionId)
    {
        return $this->urlBuilderHelper->buildUrl($this->filter, $optionId);
    }

    /**
     * @return \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    public function getFilterSetting()
    {
        if ($this->filterSetting === null) {
            $this->filterSetting = $this->filterSettingHelper->getSettingByLayerFilter($this->filter);
        }
        return $this->filterSetting;
    }

    /**
     * @return array
     */
    public function getSwatchData()
    {
        $swatchData = parent::getSwatchData();
        unset($swatchData['options']['']);
        foreach ($this->getMultiSelectSwatches(array_keys($swatchData['options'])) as $id => $value) {
            $swatchData['swatches'][$id] = $value;
        }

        if ($this->getFilterSetting()->getAttributeGroups()) {
            $swatchDataGroup = $this->getGroupSwatchData($swatchData);
            $swatchData['options'] += $swatchDataGroup['options'];
            $swatchData['swatches'] += $swatchDataGroup['swatches'];
        }

        if ($this->getFilterSetting()->getSortOptionsBy() == \Amasty\Shopby\Model\Source\SortOptionsBy::NAME) {
            uasort($swatchData['options'], [$this, 'sortSwatchData']);
        }

         return $swatchData;
    }

    /**
     * @param array $optionIds
     * @return array
     */
    private function getMultiSelectSwatches($optionIds)
    {
        $attribute = $this->getFilterSetting()->getAttributeModel();
        return $this->helper->getSwatchesFromImages($optionIds, $attribute);
    }

    /**
     * Fix Magento logic
     *
     * @param FilterItem $filterItem
     * @return bool
     */
    protected function isOptionVisible(FilterItem $filterItem)
    {
        return !$this->isOptionDisabled($filterItem) || $this->isShowEmptyResults();
    }

    /**
     * Fix Magento logic
     *
     * @return bool
     */
    protected function isShowEmptyResults()
    {
        return $this->eavAttribute->getIsFilterable() === self::FILTERABLE_NO_RESULTS;
    }

    /**
     * @param FilterItem $filterItem
     * @param Option $swatchOption
     * @return array
     */
    protected function getOptionViewData(FilterItem $filterItem, Option $swatchOption)
    {
        $data = parent::getOptionViewData($filterItem, $swatchOption);
        $data[self::VAR_COUNT] = $filterItem->getCount();
        $data[self::VAR_SELECTED] = $this->isFilterItemSelected($filterItem);

        return $data;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function sortSwatchData($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTooltipHtml()
    {
        return $this->getLayout()->createBlock(
            \Amasty\Shopby\Block\Navigation\Widget\Tooltip::class
        )
            ->setFilterSetting($this->getFilterSetting())
            ->toHtml();
    }

    /**
     * @return string
     */
    public function toHtml()
    {
        $html = parent::toHtml();

        if ($this->getFilterSetting()->isShowTooltip()) {
            $html .= $this->getTooltipHtml();
        }

        $html .= $this->filterSettingHelper->getShowMoreButtonBlock($this->getFilterSetting())->toHtml();
        return $html;
    }

    /**
     * @param \Amasty\Shopby\Model\Layer\Filter\Item $filterItem
     * @return int
     */
    public function isFilterItemSelected(\Amasty\Shopby\Model\Layer\Filter\Item $filterItem)
    {
        return $this->helper->isFilterItemSelected($filterItem);
    }

    /**
     * @return bool
     */
    public function collectFilters()
    {
        return $this->helper->collectFilters();
    }

    /**
     * @return \Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @param $data
     * @return array
     */
    private function getGroupSwatchData($data)
    {
        if ($collection = $this->getFilterSetting()->getGroupCollection()) {
            $attributeOptions = [];
            $attributeSwatches = [];
            $showNoResults = (int)$this->getFilterSetting()->getAttributeModel()->getIsFilterable()
                != AbstractFilter::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;
            foreach ($collection as $option) {
                if ($currentOption = $this->getFilterOptionGroup($this->filter->getItems(), $option)) {
                    $attributeOptions[$option->getGroupCode()] = $currentOption;
                } elseif ($showNoResults) {
                    $attributeOptions[$option->getGroupCode()] = $this->getUnusedOptionGroup($option);
                }
                $attributeSwatches[$option->getGroupCode()] = $this->getUnusedSwatchGroup($option);
            }
            $data['options'] = $attributeOptions;
            $data['swatches'] = $attributeSwatches;
        }

        return $data;
    }

    /**
     * @param $swatchOption
     * @return array
     */
    protected function getUnusedOptionGroup($swatchOption)
    {
        $customStyle = '';
        $linkToOption = $this->buildUrl($this->eavAttribute->getAttributeCode(), $swatchOption->getGroupCode());
        return [
            'label' => $swatchOption->getName(),
            'link' => $linkToOption,
            'custom_style' => $customStyle,
            self::VAR_COUNT => 0,
            self::VAR_SELECTED => 0
        ];
    }

    /**
     * @param $swatchOption
     * @return array
     */
    protected function getUnusedSwatchGroup($swatchOption)
    {
        return [
            "option_id" => $swatchOption->getId(),
             "type" => $swatchOption->getType(),
             "value" => $swatchOption->getVisual()
        ];
    }

    /**
     * @param $filterItems
     * @param $option
     * @return array|bool
     */
    private function getFilterOptionGroup($filterItems, $option)
    {
        $resultOption = false;
        $filterItem = $this->getFilterItemById($filterItems, $option->getGroupCode());
        if ($filterItem && $this->isOptionVisible($filterItem)) {
            $resultOption = $this->getOptionViewDataGroup($filterItem, $option);
        }

        return $resultOption;
    }

    /**
     * @param FilterItem $filterItem
     * @param $option
     * @return array
     */
    private function getOptionViewDataGroup(FilterItem $filterItem, $option)
    {
        $data = $this->getUnusedOptionGroup($option);
        $data[self::VAR_COUNT] = $filterItem->getCount();
        $data[self::VAR_SELECTED] = $this->isFilterItemSelected($filterItem);

        return $data;
    }

    /**
     * @return string
     */
    public function getSearchForm()
    {
        return $this->getLayout()->createBlock(
            \Amasty\Shopby\Block\Navigation\Widget\SearchForm::class
        )
            ->assign('filterCode', $this->getFilterSetting()->getFilterCode())
            ->setFilter($this->filter)
            ->toHtml();
    }
}
