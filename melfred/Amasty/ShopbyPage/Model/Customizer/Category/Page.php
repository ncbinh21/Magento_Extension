<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyPage
 */

namespace Amasty\ShopbyPage\Model\Customizer\Category;

use Amasty\Shopby\Helper\Data as ShopbyHelper;
use Amasty\ShopbyBase\Model\Customizer\Category\CustomizerInterface;
use \Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Config as CatalogConfig;
use Amasty\ShopbyPage\Api\PageRepositoryInterface;
use Amasty\ShopbyPage\Api\Data\PageInterface;
use Amasty\ShopbyPage\Model\Page as PageEntity;
use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;
use Magento\Framework\Registry;
use Magento\Store\Model\ScopeInterface;

class Page implements CustomizerInterface
{
    /**
     * @var \Amasty\Shopby\Model\Request
     */
    private $amshopbyRequest;

    /**
     * @var CatalogConfig
     */
    private $catalogConfig;

    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var ShopbyHelper
     */
    private $shopbyHelper;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        PageRepositoryInterface $pageRepository,
        CatalogConfig $catalogConfig,
        Registry $registry,
        \Amasty\Shopby\Model\Request $amshopbyRequest,
        ScopeConfigInterface $scopeConfig,
        ShopbyHelper $shopbyHelper
    ) {
        $this->pageRepository = $pageRepository;
        $this->catalogConfig = $catalogConfig;
        $this->registry = $registry;
        $this->amshopbyRequest = $amshopbyRequest;
        $this->shopbyHelper = $shopbyHelper;
        $this->scopeConfig = $scopeConfig;
    }

    public function prepareData(\Magento\Catalog\Model\Category $category)
    {
        $searchResults = $this->pageRepository->getList($category);

        if ($searchResults->getTotalCount() > 0) {
            foreach ($searchResults->getItems() as $pageData) {
                if ($this->matchCurrentFilters($pageData)) {
                    $this->modifyCategory($pageData, $category);
                    $this->registry->register(PageEntity::MATCHED_PAGE, $pageData);
                    break;
                }
            }
        }
    }

    /**
     * Compare page filters with selected filters
     *
     * @param PageInterface $pageData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function matchCurrentFilters(PageInterface $pageData)
    {
        $isMatched = true;
        $conditions = $pageData->getConditions();

        foreach ($conditions as $condition) {
            $attribute = $this->catalogConfig->getAttribute(Product::ENTITY, $condition['filter']);
            if ($attribute->getId()) {
                $paramValue = $this->amshopbyRequest->getParam($attribute->getAttributeCode());

                //compare with array for multiselect attributes
                if ($attribute->getFrontendInput() === 'multiselect') {
                    $paramValue = explode(',', $paramValue);

                    if (!isset($condition['value']) || !is_array($condition['value'])) {
                        $isMatched = false;
                        break;
                    }

                    if (array_diff($condition['value'], $paramValue)) {
                        $isMatched = false;
                        break;
                    }
                } else {
                    if ($paramValue !== $condition['value']) {
                        $isMatched = false;
                        break;
                    }
                }
            }
        }

        if ($isMatched) {
            $strictMatching = $this->scopeConfig
                ->getValue('amshopby_page/general/page_match_strict', ScopeInterface::SCOPE_STORE);
            if ($strictMatching && !$this->checkStrictMatch($pageData)) {
               $isMatched = false;
            }
        }

        return $isMatched;
    }

    /**
     * @param PageInterface $pageData
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function checkStrictMatch(PageInterface $pageData)
    {
        $strict = true;
        $conditions = $pageData->getConditions();
        $appliedFilters = $this->shopbyHelper->getSelectedFiltersSettings();
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
                        if (!isset($condition['value'])
                            || !is_array($condition['value'])
                            || array_diff($paramValue, $condition['value'])
                        ) {
                            break;
                        }
                    }
                    //The only chance to get strict == true is to rich this line every time.
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
    private function getModifiedCategoryData(
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
    private function modifyCategoryData(
        PageInterface $page,
        CategoryInterface $category,
        $pageKey,
        $categoryKey,
        $delimiter = null
    ) {
        $categoryValue = $category->getData($categoryKey);
        $pageValue = $page->getData($pageKey);
        $modifiedData = $this->getModifiedCategoryData($page, $pageValue, $categoryValue, $delimiter);
        if ($modifiedData) {
            $category->setData($categoryKey, $modifiedData);
        }
    }

    /**
     * @param PageInterface $page
     * @param CategoryInterface $category
     */
    private function modifyCategory(PageInterface $page, CategoryInterface $category)
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
