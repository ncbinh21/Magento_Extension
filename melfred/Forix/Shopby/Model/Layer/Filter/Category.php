<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Shopby\Model\Layer\Filter;

use Magento\Catalog\Model\Layer\Filter\DataProvider\Category as CategoryDataProvider;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;
use Forix\Base\Helper\Data as baseHelper;

/**
 * Layer category filter
 */
class Category extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
    /**
     * Active Category Id
     *
     * @var int
     */
    protected $_categoryId;

    /**
     * Applied Category
     *
     * @var \Magento\Catalog\Model\Category
     */
    protected $_appliedCategory;

    /**
     * Core data
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var CategoryDataProvider
     */
    private $dataProvider;

    protected $baseHelper;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param CategoryFactory $categoryDataProviderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\Escaper $escaper,
        CategoryFactory $categoryDataProviderFactory,
        baseHelper $basehelper,
        array $data = []
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder, $data);
        $this->_escaper = $escaper;
        $this->_requestVar = 'cat';
        $this->dataProvider = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->baseHelper = $basehelper;
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
     * Apply category filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $categoryId = (int)$request->getParam($this->getRequestVar());
        if (!$categoryId) {
            return $this;
        }

        $this->dataProvider->setCategoryId($categoryId);

        if ($this->dataProvider->isValid()) {
            $category = $this->dataProvider->getCategory();
            $this->getLayer()->getProductCollection()->addCategoryFilter($category);
            $this->getLayer()->getState()->addFilter($this->_createItem($category->getName(), $categoryId));
        }

        return $this;
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
     * Get data array for building category filter items
     *
     * @return array
     */
    protected function _getItemsData()
    {
        $category = $this->dataProvider->getCategory();
        $categories = $category->getChildrenCategories();
        $this->getLayer()->getProductCollection()->addCountToCategories($categories);
        $ids = $this->baseHelper->getConfigValue('forix_catalog/feature_category/ids');
        if ($ids!="") {
            $ids = explode(",", $ids);
        } else {
            $ids = [];
        }

        if ($category->getIsActive()) {
            foreach ($categories as $category) {
                $id = $category->getId();
                if (!in_array($id, $ids)) {
                    if ($category->getIsActive() && $category->getData('product_count')) {
                        $this->itemDataBuilder->addItemData(
                            $this->_escaper->escapeHtml($category->getName()),
                            $category->getId(),
                            $category->getData('product_count')
                        );
                    }
                }
            }
        }
        return $this->itemDataBuilder->build();
    }
}
