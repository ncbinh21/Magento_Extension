<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBrand
 */


namespace Amasty\ShopbyBrand\Block\Widget;

use Amasty\ShopbyBase\Api\Data\OptionSettingInterface;
use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\View\Element\Template\Context;
use Amasty\ShopbyBase\Helper\OptionSetting as OptionSettingHelper;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute as FilterAttributeResource;
use Magento\Store\Model\ScopeInterface;
use \Magento\Eav\Model\Entity\Attribute\Option;
use Amasty\ShopbyBrand\Helper\Data as DataHelper;

class BrandList extends BrandListAbstract implements \Magento\Widget\Block\BlockInterface
{
    const CONFIG_VALUES_PATH = 'amshopby_brand/brands_landing';

    /**
     * @var FilterAttributeResource
     */
    protected $filterAttributeResource;

    /**
     * @var \Magento\CatalogInventory\Helper\Stock
     */
    protected $stockHelper;

    /**
     * @var \Magento\Catalog\Model\Product\Visibility
     */
    protected $catalogProductVisibility;

    /**
     * BrandList constructor.
     * @param Context $context
     * @param Repository $repository
     * @param OptionSettingHelper $optionSetting
     * @param \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $collectionProvider
     * @param \Magento\Catalog\Model\Product\Url $productUrl
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param FilterAttributeResource $filterAttributeResource
     * @param \Magento\CatalogInventory\Helper\Stock $stockHelper
     * @param \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility
     * @param DataHelper $dataHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Repository $repository,
        OptionSettingHelper $optionSetting,
        \Magento\CatalogSearch\Model\Layer\Category\ItemCollectionProvider $collectionProvider,
        \Magento\Catalog\Model\Product\Url $productUrl,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        FilterAttributeResource $filterAttributeResource,
        \Magento\CatalogInventory\Helper\Stock $stockHelper,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        DataHelper $dataHelper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $repository,
            $optionSetting,
            $collectionProvider,
            $productUrl,
            $categoryRepository,
            $dataHelper,
            $data
        );
        $this->filterAttributeResource = $filterAttributeResource;
        $this->stockHelper = $stockHelper;
        $this->catalogProductVisibility = $catalogProductVisibility;
    }

    /**
     * @return array
     */
    public function getIndex()
    {
        $items = $this->getItems();
        if (!$items) {
            return [];
        }

        $this->sortItems($items);

        $letters = $this->items2letters($items);

        $columnCount = abs((int)$this->getData('columns'));
        if (!$columnCount) {
            $columnCount = 1;
        }
        $itemsPerColumn = ceil((sizeof($items) + sizeof($letters)) / max(1, $columnCount));

        $col = 0; // current column
        $num = 0; // current number of items in column
        $index = [];
        foreach ($letters as $letter => $items) {
            $index[$col][$letter] = $items['items'];
            $num += $items['count'];
            $num++;
            if ($num >= $itemsPerColumn) {
                $num = 0;
                $col++;
            }
        }

        return $index;
    }

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\Option $option
     * @param OptionSettingInterface $setting
     * @return array
     */
    protected function getItemData(Option $option, OptionSettingInterface $setting)
    {
        $displayZero = $this->_scopeConfig->getValue(
            'amshopby_brand/brands_landing/display_zero',
            ScopeInterface::SCOPE_STORE
        );
        $count = $this->_getOptionProductCount($setting->getValue());
        if (!$displayZero && !$count) {
            return [];
        }
        return [
            'label' => $setting->getLabel(),
            'url' => $this->getBrandUrl($option),
            'img' => $setting->getImageUrl(),
            'cnt' => $count
        ];

    }

    /**
     * @param array $items
     */
    protected function sortItems(array &$items)
    {
        usort($items, [$this, '_sortByName']);
    }

    /**
     * @param array $items
     * @return array
     */
    protected function items2letters($items)
    {
        $letters = [];
        foreach ($items as $item) {
            if (function_exists('mb_strtoupper')) {
                $i = mb_strtoupper(mb_substr($item['label'], 0, 1, 'UTF-8'));
            } else {
                $i = strtoupper(substr($item['label'], 0, 1));
            }

            if (is_numeric($i)) {
                $i = '#';
            }
            if (!isset($letters[$i]['items'])) {
                $letters[$i]['items'] = [];
            }
            $letters[$i]['items'][] = $item;
            if (!isset($letters[$i]['count'])) {
                $letters[$i]['count'] = 0;
            }
            $letters[$i]['count']++;
        }

        return $letters;
    }

    /**
     * @return array
     */
    public function getAllLetters()
    {
        $brandLetters = [];
        foreach ($this->getIndex() as $letters) {
            $brandLetters = array_merge($brandLetters, array_keys($letters));
        }
        return $brandLetters;
    }

    /**
     * @return string
     */
    public function getSearchHtml()
    {
        if (!$this->getData('show_search') || !$this->getItems()) {
            return '';
        }
        $searchCollection = [];
        foreach ($this->getItems() as $item) {
            $searchCollection[$item['url']] = $item['label'];
        }
        $searchCollection = json_encode($searchCollection);
        /** @var \Magento\Framework\View\Element\Template $block */
        $block = $this->getLayout()->createBlock(\Magento\Framework\View\Element\Template::class, 'ambrands.search')
            ->setTemplate('Amasty_ShopbyBrand::brand_search.phtml')
            ->setBrands($searchCollection);
        return $block->toHtml();
    }

    /**
     * Get brand product count
     *
     * @param int $optionId
     * @return int
     */
    protected function _getOptionProductCount($optionId)
    {
        if ($this->productCount === null) {
            $rootCategoryId = $this->_storeManager->getStore()->getRootCategoryId();
            $category = $this->categoryRepository->get($rootCategoryId);
            /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection */
            $collection = $this->collectionProvider->getCollection($category);
            $attrCode = $this->_scopeConfig->getValue(
                'amshopby_brand/general/attribute_code',
                ScopeInterface::SCOPE_STORE
            );

            $this->productCount = $collection
                ->addAttributeToFilter($attrCode, ['nin' => 1])
                ->setVisibility([2,4])
                ->getFacetedData($attrCode);
        }

        return isset($this->productCount[$optionId]) ? $this->productCount[$optionId]['count'] : 0;
    }

    /**
     * @return string
     */
    protected function getConfigValuesPath()
    {
        return self::CONFIG_VALUES_PATH;
    }
}
