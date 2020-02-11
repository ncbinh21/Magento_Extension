<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\ShopbyPage\Model\Customizer\Category;

use Amasty\Shopby\Helper\Data;
use Amasty\ShopbyBase\Model\Customizer\Category\CustomizerInterface;
use \Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\Helper\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Config as CatalogConfig;
use Amasty\ShopbyPage\Api\PageRepositoryInterface;
use Amasty\ShopbyPage\Api\Data\PageInterface;
use Amasty\ShopbyPage\Model\Page as PageEntity;
use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;
use Magento\Framework\Registry;

class Page implements CustomizerInterface
{
    /**
     * @var \Amasty\Shopby\Model\Request
     */
    protected $amshopbyRequest;

    /**
     * @var CatalogConfig
     */
    protected $catalogConfig;

    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Data
     */
    protected $dataHelper;

    public function __construct(
        Context $context,
        PageRepositoryInterface $pageRepository,
        CatalogConfig $catalogConfig,
        Registry $registry,
        \Amasty\Shopby\Model\Request $amshopbyRequest,
        Data $data
    ) {
        $this->pageRepository = $pageRepository;
        $this->catalogConfig = $catalogConfig;
        $this->registry = $registry;
        $this->amshopbyRequest = $amshopbyRequest;
        $this->dataHelper = $data;
    }

    public function prepareData(\Magento\Catalog\Model\Category $category)
    {
        $searchResults = $this->pageRepository->getList($category);

        if ($searchResults->getTotalCount() > 0) {
            foreach ($searchResults->getItems() as $pageData) {
                if ($matchType = $this->matchCurrentFilters($pageData)) {
                    $this->modifyCategory($pageData, $category);
                    $this->registry->register(PageEntity::MATCHED_PAGE, $pageData);
                    $this->registry->register(PageEntity::MATCHED_PAGE_MATCH_TYPE, $matchType);
                    break;
                }
            }
        }
    }

    /**
     * Compare page filters with selected filters
     * @param PageInterface $pageData
     * @return int
     */
    protected function matchCurrentFilters(PageInterface $pageData)
    {
        $match = true;

        $conditions = $pageData->getConditions();

        foreach ($conditions as $condition) {
            $attribute = $this->catalogConfig->getAttribute(Product::ENTITY, $condition['filter']);
            if ($attribute->getId()) {
                $paramValue = $this->amshopbyRequest->getParam($attribute->getAttributeCode());

                //compare with array for multiselect attributes
                if ($attribute->getFrontendInput() === 'multiselect') {
                    $paramValue = explode(',', $paramValue);

                    if (!isset($condition['value']) || !is_array($condition['value'])) {
                        $match = PageEntity::MATCH_TYPE_NO;
                        break;
                    }

                    if (array_diff($condition['value'], $paramValue)) {
                        $match = PageEntity::MATCH_TYPE_NO;
                        break;
                    }
                } else {
                    if ($paramValue !== $condition['value']) {
                        $match = PageEntity::MATCH_TYPE_NO;
                        break;
                    }
                }
            }
        }

        if ($match) {
            $matchType = $this->checkStrictMatch($pageData) ?
                PageEntity::MATCH_TYPE_STRICT :
                PageEntity::MATCH_TYPE_GENERIC;
        } else {
            $matchType = PageEntity::MATCH_TYPE_NO;
        }
        return $matchType;
    }

    protected function checkStrictMatch(PageInterface $pageData)
    {
        $strict = true;
        $conditions = $pageData->getConditions();
        $appliedFilters = $this->dataHelper->getSelectedFiltersSettings();
        foreach ($appliedFilters as $item) {
            /** @var AbstractFilter $filter */
            $filter = $item['filter'];
            if (!$filter->hasData('attribute_model')) {
                //Pages can contain only attribute conditions for a while
                $strict = false;
                break;
            }

            $attribute = $filter->getAttributeModel();
            $paramValue = $this->amshopbyRequest->getParam($filter->getRequestVar());
            foreach ($conditions as $condition) {
                if ($condition['filter'] == $attribute->getAttributeId()) {
                    if ($attribute->getFrontendInput() === 'multiselect') {
                        $paramValue = explode(',', $paramValue);

                        if (!isset($condition['value']) || !is_array($condition['value'])) {
                            $strict = false;
                            break;
                        }

                        if (array_diff($paramValue, $condition['value'])) {
                            $strict = false;
                        }
                    }
                    continue(2);
                }
            }

            $strict = false;
            break;
        }
        return $strict;
    }

    /**
     * @param PageInterface|PageEntity $page
     * @param $pageValue
     * @param $categoryValue
     * @param null $delimiter
     * @return string
     */
    protected function getModifiedCategoryData(
        PageInterface $page,
        $pageValue,
        $categoryValue,
        $delimiter = null
    ) {
        if ($delimiter !== null && $page->getPosition() !== PageEntity::POSITION_REPLACE) {
            //if has a delimiter, place at the start or end
            $categoryValueArr =
                $categoryValue !== null &&
                $categoryValue !== '' ?
                    explode($delimiter, $categoryValue) :
                    [];

            if ($page->getPosition() === PageEntity::POSITION_AFTER) {
                $categoryValueArr[] = $pageValue;
            } else {
                $categoryValueArr = array_merge([$pageValue], $categoryValueArr);
            }
            $categoryValue = implode($delimiter, $categoryValueArr);
        } else {
            $categoryValue = $pageValue;
        }
        return $categoryValue;
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     * @param $pageKey
     * @param $categoryKey
     * @param null $delimiter
     */
    protected function modifyCategoryData(
        PageInterface $page,
        CategoryInterface $category,
        $pageKey,
        $categoryKey,
        $delimiter = null
    ) {
        $categoryValue = $category->getData($categoryKey);
        $pageValue = $page->getData($pageKey);
        $modifiedData = $this->getModifiedCategoryData($page, $pageValue, $categoryValue, $delimiter);
        $category->setData($categoryKey, $modifiedData);
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     */
    protected function modifyCategory(PageInterface $page, CategoryInterface $category)
    {
        $categoryName = $this->getModifiedCategoryData($page, $page->getTitle(), $category->getName(), ' ');
        $category->setName($categoryName);

        $this->modifyCategoryData($page, $category, 'description', 'description', '<br>');
        $this->modifyCategoryData($page, $category, 'meta_title', 'meta_title', ' ');
        $this->modifyCategoryData($page, $category, 'meta_description', 'meta_description', ' ');
        $this->modifyCategoryData($page, $category, 'meta_keywords', 'meta_keywords', ',');
        $this->modifyCategoryData($page, $category, 'top_block_id', 'landing_page');
        $this->modifyCategoryData($page, $category, 'url', 'url');

        if ($page->getImage()) {
            $category->setData(CategoryManager::CATEGORY_SHOPBY_IMAGE_URL, $page->getImageUrl());
        }

        if ($page->getTopBlockId()) {
            $category->setData(CategoryManager::CATEGORY_FORCE_MIXED_MODE, 1);
        }

        if ($page->getUrl()) {
            $category->setData(PageEntity::CATEGORY_FORCE_USE_CANONICAL, 1);
        }
    }
}
