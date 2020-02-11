<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */
namespace Amasty\Shopby\Model\Layer\Filter;

use Magento\Search\Model\SearchEngine;
use Amasty\Shopby\Model\Source\RenderCategoriesLevel;
use Amasty\Shopby\Helper\Category as CategoryHelper;
use Amasty\Shopby\Model\Layer\Filter\Traits\FilterTrait;
use Amasty\Shopby\Model\Source\CategoryTreeDisplayMode;

/**
 * Layer category filter
 */
class Category extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    use FilterTrait;

    /**
     * @var \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter
     */
    protected $aggregationAdapter;

    /**
     * @var \Amasty\ShopbyBase\Api\Data\FilterSettingInterface
     */
    private $filterSetting;

    /**
     * @var \Magento\Framework\Escaper
     */
    private $escaper;

    /**
     * @var \Magento\Catalog\Model\Layer\Filter\DataProvider\Category
     */
    private $dataProvider;

    /**
     * @var \Amasty\ShopbyBase\Model\Category\Manager\Proxy
     */
    private $categoryManager;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    private $categoryRepository;

    /**
     * @var Item\CategoryExtendedDataBuilder
     */
    private $categoryExtendedDataBuilder;

    /**
     * @var CategoryItemsFactory
     */
    private $categoryItemsFactory;

    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    private $helper;

    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $shopbyRequest;

    /**
     * @var \Amasty\Shopby\Helper\Category
     */
    private $categoryHelper;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $categoryFactory;

    /**
     * @var SearchEngine
     */
    private $searchEngine;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory
     * @param \Amasty\Shopby\Helper\FilterSetting $settingHelper
     * @param \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter
     * @param \Amasty\ShopbyBase\Model\Category\Manager\Proxy $categoryManager
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param Item\CategoryExtendedDataBuilder $categoryExtendedDataBuilder
     * @param CategoryItemsFactory $categoryItemsFactory
     * @param \Amasty\Shopby\Helper\Data $helper
     * @param \Amasty\Shopby\Model\Request $shopbyRequest
     * @param CategoryHelper $categoryHelper
     * @param SearchEngine $searchEngine
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryFactory,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory $categoryDataProviderFactory,
        \Amasty\Shopby\Helper\FilterSetting $settingHelper,
        \Amasty\Shopby\Model\Search\Adapter\Mysql\AggregationAdapter $aggregationAdapter,
        \Amasty\ShopbyBase\Model\Category\Manager\Proxy $categoryManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Amasty\Shopby\Model\Layer\Filter\Item\CategoryExtendedDataBuilder $categoryExtendedDataBuilder,
        \Amasty\Shopby\Model\Layer\Filter\CategoryItemsFactory $categoryItemsFactory,
        \Amasty\Shopby\Helper\Data $helper,
        \Amasty\Shopby\Model\Request $shopbyRequest,
        \Amasty\Shopby\Helper\Category $categoryHelper,
        SearchEngine $searchEngine,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->helper = $helper;
        $this->escaper = $escaper;
        $this->_requestVar = 'cat';
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->filterSetting = $settingHelper->getSettingByLayerFilter($this);
        $this->aggregationAdapter = $aggregationAdapter;
        $this->categoryManager = $categoryManager;
        $this->categoryRepository = $categoryRepository;
        $this->categoryExtendedDataBuilder = $categoryExtendedDataBuilder;
        $this->categoryItemsFactory = $categoryItemsFactory;
        $this->shopbyRequest = $shopbyRequest;
        $this->categoryHelper = $categoryHelper;
        $this->categoryFactory = $categoryFactory;
        $this->searchEngine = $searchEngine;
    }

    /**
     * Apply category filter to product collection
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->isApplied()) {
            return $this;
        }

        $categoryId = $this->shopbyRequest->getFilterParam($this) ?: $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }

        $categoryIds = explode(',', $categoryId);
        $categoryIds = array_unique($categoryIds);
        /** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();

        if ($this->isMultiselect() && $request->getParam('id') != $categoryId) {
            $this->setCurrentValue($categoryIds);
            $productCollection->addIndexCategoriesFilter(['in' => $categoryIds]);
            $category = $this->getLayer()->getCurrentCategory();
            $child = $category->getCollection()
                ->addFieldToFilter($category->getIdFieldName(), ['in' => $categoryIds])
                ->addAttributeToSelect('name');
            $categoriesInState = [];
            foreach ($categoryIds as $categoryId) {
                if ($currentCategory = $child->getItemById($categoryId)) {
                    $categoriesInState[$currentCategory->getId()] = $currentCategory->getName();
                }
            }
            $state = $this->_createItem(implode(", ", $categoriesInState), array_keys($categoriesInState));
            $this->getLayer()->getState()->addFilter($state);
        } else {
            $this->dataProvider->setCategoryId($categoryId);
            $category = $this->dataProvider->getCategory();
            $productCollection->addCategoryFilter($category);
            if ($request->getParam('id') != $category->getId() && $this->dataProvider->isValid()) {
                $this->getLayer()->getState()->addFilter($this->_createItem($category->getName(), $categoryId));
            }
        }

        return $this;
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed|null
     */
    public function getResetValue()
    {
        return $this->dataProvider->getResetValue();
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return __('Category');
    }

    /**
     * Get fiter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        if (!$this->categoryHelper->isCategoryFilterExtended()) {
            return parent::getItemsCount();
        }
        return $this->getItems()->getCount();
    }

    /**
     * @return $this|\Magento\Catalog\Model\Layer\Filter\AbstractFilter
     */
    protected function _initItems()
    {
        if (!$this->categoryHelper->isCategoryFilterExtended()) {
            return parent::_initItems();
        }
        $data = $this->getExtendedCategoryData();
        /** @var CategoryItems $itemsCollection */
        $itemsCollection = $this->categoryItemsFactory->create();
        $itemsCollection->setStartPath($data['startPath']);
        $itemsCollection->setCount($data['count']);
        foreach ($data['items'] as $path => $items) {
            foreach ($items as $itemData) {
                $itemsCollection->addItem(
                    $path,
                    $this->_createItem($itemData['label'], $itemData['value'], $itemData['count'])
                );
            }
        }

        $this->_items = $itemsCollection;
        return $this;
    }

    /**
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $optionsFacetedData = $this->getFacetedData();
        $category = $this->dataProvider->getCategory();
        $categories = $category->getChildrenCategories();

        if ($categories instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection) {
            $categories->addAttributeToSelect('thumbnail');
        }

        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                if ($category->getIsActive()
                    && $category->getIsAnchor()
                    && isset($optionsFacetedData[$category->getId()])
                ) {
                    $this->itemDataBuilder->addItemData(
                        $this->escaper->escapeHtml($category->getName()),
                        $category->getId(),
                        $optionsFacetedData[$category->getId()]['count']
                    );
                }
            }
        }

        $itemsData = $this->itemDataBuilder->build();

        if (count($itemsData) == 1) {
            $collectionSize = $this->getLayer()->getProductCollection()->getSize();
            if (!$this->isOptionReducesResults($itemsData[0]['count'], $collectionSize)) {
                $itemsData = [];
            }
        }

        if ($this->filterSetting->getSortOptionsBy() == \Amasty\Shopby\Model\Source\SortOptionsBy::NAME) {
            usort($itemsData, [$this, 'sortOption']);
        }

        return $itemsData;
    }

    /**
     * @param $a
     * @param $b
     * @return int
     */
    public function sortOption($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }

    /**
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getExtendedCategoryData()
    {
        $optionsFacetedData = $this->getFacetedData();
        $startCategory = $this->categoryHelper->getStartCategory();
        $startPath = $startCategory->getPath();
        $minLevel = $startCategory->getLevel();
        $isRootCategory =
            $this->getLayer()->getCurrentCategory()->getId() == $this->categoryManager->getRootCategoryId();
        $isChildrenLevel = $this->getRenderCategoriesLevel() == RenderCategoriesLevel::CURRENT_CATEGORY_CHILDREN;
        $isRenderAllCategoriesTree = $this->filterSetting->getRenderAllCategoriesTree()
            || $isRootCategory
            || $isChildrenLevel;
        $currentCategoryPath = $this->getLayer()->getCurrentCategory()->getPath();
        $currentCategoryParents = explode('/', $currentCategoryPath);

        $maxLevel = $minLevel + $this->getCategoriesTreeDept();
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $startCategory->getCollection();
        $isFlat = $collection instanceof \Magento\Catalog\Model\ResourceModel\Category\Flat\Collection;
        $mainTablePrefix = $isFlat ? 'main_table.' : '';
        $collection->addAttributeToSelect(
            'name'
        )->addAttributeToFilter(
            $mainTablePrefix . 'is_active',
            1
        )->addFieldToFilter($mainTablePrefix . 'path', ['like' => $startPath . '%'])
            ->addFieldToFilter($mainTablePrefix . 'level', ['gt' => $minLevel])
            ->addFieldToFilter($mainTablePrefix . 'level', ['lteq' => $maxLevel])
            ->setOrder(
                $mainTablePrefix . 'position',
                \Magento\Framework\DB\Select::SQL_ASC
            );
        $mainTablePrefix = $isFlat ? 'main_table.' : 'e.';
        $collection->getSelect()->joinLeft(
            ['parent' => $collection->getMainTable()],
            $mainTablePrefix . 'parent_id = parent.entity_id',
            ['parent_path' => 'parent.path']
        );

        foreach ($collection as $category) {
            if (!isset($optionsFacetedData[$category->getId()])) {
                continue;
            }
            if (!$isRenderAllCategoriesTree
                && !in_array($category->getId(), $currentCategoryParents)
                && strpos($category->getPath(), $currentCategoryPath) !== 0
            ) {
                continue;
            }
            $this->categoryExtendedDataBuilder->addItemData(
                $category->getParentPath(),
                $this->escaper->escapeHtml($category->getName()),
                $category->getId(),
                $optionsFacetedData[$category->getId()]['count']
            );
        }
        $itemsData = [];
        $itemsData['count'] = $this->categoryExtendedDataBuilder->getItemsCount();
        $itemsData['startPath'] = $startPath;
        $itemsData['items'] = $this->categoryExtendedDataBuilder->build();

        if ($this->filterSetting->getSortOptionsBy() == \Amasty\Shopby\Model\Source\SortOptionsBy::NAME) {
            foreach ($itemsData['items'] as $path => &$items) {
                usort($items, [$this, 'sortOption']);
            }
        }

        return $itemsData;
    }

    /**
     * @return array
     */
    protected function getFacetedData()
    {
        /** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();

        $alteredQueryResponse = $this->getAlteredQueryResponse();
        $optionsFacetedData = $productCollection->getFacetedData('category', $alteredQueryResponse);

        /** Uncomment to add category children with null */
        //$optionsFacetedData = $this->addChildrenCategoriesWithNull($optionsFacetedData);

        return $optionsFacetedData;
    }

    /**
     * @return \Magento\Framework\Search\ResponseInterface|null
     */
    private function getAlteredQueryResponse()
    {
        $alteredQueryResponse = null;

        if ($this->hasCurrentValue() ||
            $this->getRenderCategoriesLevel() == RenderCategoriesLevel::CURRENT_CATEGORY_LEVEL ||
            $this->getRenderCategoriesLevel() == RenderCategoriesLevel::ROOT_CATEGORY
        ) {
            /** @var \Amasty\Shopby\Model\ResourceModel\Fulltext\Collection $productCollection */
            $productCollection = $this->getLayer()->getProductCollection();
            $requestBuilder = clone $productCollection->getMemRequestBuilder();
            $requestBuilder->removePlaceholder(CategoryHelper::ATTRIBUTE_CODE);
            if ($this->isMultiselect()) {
                $requestBuilder
                    ->bind(CategoryHelper::ATTRIBUTE_CODE, $this->helper->getCurrentCategory()->getId());
            } else {
                $startCategoryForCountBucket = $this->categoryHelper->getStartCategory();
                $requestBuilder->bind(CategoryHelper::ATTRIBUTE_CODE, $startCategoryForCountBucket->getId());
            }
            $queryRequest = $requestBuilder->create();

            $alteredQueryResponse = $this->searchEngine->search($queryRequest);
        }
        return $alteredQueryResponse;
    }

    /**
     * @param $optionsFacetedData
     * @return mixed
     */
    protected function addChildrenCategoriesWithNull($optionsFacetedData)
    {
        $categories = $this->categoryFactory->create()->addAttributeToSelect('*');

        foreach ($categories as $key => $category) {
            if (!isset($optionsFacetedData[$key])) {
                $optionsFacetedData[$key] = ['value' => $key, 'count' => 0];
            }
        }

        return $optionsFacetedData;
    }

    /**
     *
     * @return int
     */
    protected function getRenderCategoriesLevel()
    {
        return $this->filterSetting->getRenderCategoriesLevel();
    }

    /**
     *
     * @return int
     */
    protected function getCategoriesTreeDept()
    {
        return $this->filterSetting->getCategoryTreeDepth();
    }

    /**
     * @return bool
     */
    public function isMultiselect()
    {
        return $this->filterSetting->isMultiselect();
    }

    /**
     * @return bool
     */
    public function useLabelsOnly()
    {
        return $this->getImageDisplayMode() == CategoryTreeDisplayMode::SHOW_LABELS_ONLY;
    }

    /**
     * @return bool
     */
    public function useLabelsAndImages()
    {
        return $this->getImageDisplayMode() == CategoryTreeDisplayMode::SHOW_LABELS_IMAGES;
    }

    /**
     * @return bool
     */
    public function useImagesOnly()
    {
        return $this->getImageDisplayMode() == CategoryTreeDisplayMode::SHOW_IMAGES_ONLY;
    }

    /**
     * @return int
     */
    public function getImageDisplayMode()
    {
        return $this->filterSetting->getCategoryTreeDisplayMode();
    }
}
