<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.64
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\SeoSitemap\Block;

use Magento\Store\Model\ScopeInterface;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Map extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\SeoSitemap\Model\Pager\CollectionFactory
     */
    protected $pagerCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Mirasvit\SeoSitemap\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $catalogCategory;


    /**
     * @var \Mirasvit\SeoSitemap\Helper\Data
     */
    protected $seoSitemapData;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $dbResource;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @var \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface
     */
    protected $cmsSitemapConfig;

    /**
     * @var \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface
     */
    protected $linkSitemapConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * @param \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface
     * @param \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface
     * @param \Mirasvit\SeoSitemap\Model\Pager\CollectionFactory              $pagerCollectionFactory
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory  $productCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param \Magento\Cms\Model\ResourceModel\Page\CollectionFactory         $pageCollectionFactory
     * @param \Mirasvit\SeoSitemap\Model\Config                               $config
     * @param \Magento\Framework\App\ResourceConnection                       $resource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime                     $date
     * @param \Magento\Catalog\Helper\Category                                $catalogCategory
     * @param \Mirasvit\SeoSitemap\Helper\Data                                $seoSitemapData
     * @param \Magento\Framework\Module\Manager                               $moduleManager
     * @param \Magento\Framework\App\ResourceConnection                       $dbResource
     * @param \Magento\Framework\View\Element\Template\Context                $context
     * @param \Magento\Framework\ObjectManagerInterface                       $objectManager
     * @param array                                                           $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface $cmsSitemapConfig,
        \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface $linkSitemapConfig,
        \Mirasvit\SeoSitemap\Model\Pager\CollectionFactory $pagerCollectionFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory       $categoryCollectionFactory,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory       $pageCollectionFactory,
        \Mirasvit\SeoSitemap\Model\Config $config,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Catalog\Helper\Category $catalogCategory,
        \Mirasvit\SeoSitemap\Helper\Data $seoSitemapData,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\ResourceConnection $dbResource,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        $this->cmsSitemapConfig = $cmsSitemapConfig;
        $this->linkSitemapConfig = $linkSitemapConfig;
        $this->pagerCollectionFactory = $pagerCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->config = $config;
        $this->resource = $resource;
        $this->date = $date;
        $this->catalogCategory = $catalogCategory;
        $this->seoSitemapData = $seoSitemapData;
        $this->moduleManager = $moduleManager;
        $this->dbResource = $dbResource;
        $this->request = $context->getRequest();
        $this->context = $context;
        $this->pageConfig = $context->getPageConfig();
        $this->objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    /**
     * @var array
     */
    protected $categoriesTree = [];
    /**
     * @var array
     */
    protected $itemLevelPositions = [];
    /**
     * @var bool
     */
    protected $isMagentoEe = false;

    /**
     * @var \Mirasvit\SeoSitemap\Model\Pager\Collection
     */
    protected $collection;

    /**
     * @return \Mirasvit\SeoSitemap\Api\Config\CmsSitemapConfigInterface
     */
    public function getCmsSitemapConfig()
    {
        return $this->cmsSitemapConfig;
    }

    /**
     * @return \Mirasvit\SeoSitemap\Api\Config\LinkSitemapConfigInterface
     */
    public function getLinkSitemapConfig()
    {
        return $this->linkSitemapConfig;
    }

    /**
     * @return \Mirasvit\SeoSitemap\Model\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function getExcludeLinks()
    {
        return $this->getLinkSitemapConfig()->getExcludeLinks();
    }

    /**
     * Prepare breadcrumbs
     *
     * @return void
     */
    protected function addBreadcrumbs()
    {
        /** @var \Magento\Theme\Block\Html\Breadcrumbs $breadcrumbsBlock */
        if (
            $this->_scopeConfig->getValue('web/default/show_cms_breadcrumbs', ScopeInterface::SCOPE_STORE) &&
            $breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')
        ) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb('cms_page', [
                'label' => $this->config->getFrontendSitemapMetaTitle(),
                'title' => $this->config->getFrontendSitemapMetaTitle()
            ]);
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareLayout()
    {
        $this->addBreadcrumbs();
        $this->pageConfig->getTitle()->set($this->config->getFrontendSitemapMetaTitle());
        $this->pageConfig->setKeywords($this->config->getFrontendSitemapMetaKeywords());
        $this->pageConfig->setDescription($this->config->getFrontendSitemapMetaDescription());

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            /* @fixme */
            $pageMainTitle->setPageTitle($this->escapeHtml($this->config->getFrontendSitemapH1()));
        }

        $collection = $this->pagerCollectionFactory->create();
        $collection->setCollection($this->getCategoriesTree());
        if ($this->getLimitPerPage()) {
            $pagerBlock = $this->getLayout()->createBlock('\Mirasvit\SeoSitemap\Block\Map\Pager', 'map.pager')
                            ->setShowPerPage(false)
                            ->setShowAmounts(false)
                            ;
            $pagerBlock
                ->setLimit($this->getLimitPerPage())
                ->setCollection($collection)
            ;
            $this->append($pagerBlock);
        }
        $this->collection = $collection;

        return parent::_prepareLayout();
    }

    /**
     * @return \Mirasvit\SeoSitemap\Model\Pager\Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * BEGIN LIMITED MODE FUNCTIONS
     *
     * @return bool
     */
    public function isFirstPage()
    {
        if (!$this->getLimitPerPage()) {
            return true;
        }

        return $this->getCollection()->getCurPage() == 1;
    }

    /**
     * @return int
     */
    public function getLimitPerPage()
    {
        return (int) $this->config->getFrontendLinksLimit();
    }

    /**
     * Use only in one page mode.
     *
     * @return array
     */
    public function getCategoryLimitedSortedTree()
    {
        $page = $this->request->getParam('p') ?: 1;
        $beginPageValue = ($page * $this->getLimitPerPage()) - $this->getLimitPerPage();
        $categories = $this->getCategoriesTree();
        $categories = array_splice($categories, $beginPageValue, $this->getLimitPerPage());

        return $categories;
    }
    /**
     * Use only in one page mode.
     *
     * @return \Magento\Framework\Data\Tree\Node\Collection
     */
    protected function getStoreCategories()
    {
        return $this->catalogCategory->getStoreCategories();
    }

    /**
     * @param \Magento\Framework\Data\Tree\Node|\Magento\Catalog\Model\Category     $category
     * @param int    $level
     * @return string
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getCategoriesTree($category, $level = 0)
    {
        if (!$category->getIsActive()) {
            return '';
        }

        $children = $category->getChildren();

        if (is_string($children) && $children) {
            $children = explode(',', $children);
            $children = array_map('trim', $children);
        } elseif (!is_object($children) || !$children->count()) {
            return '';
        }

        // select active children
        $activeChildren = [];
        if (is_object($children)) {
            foreach ($children as $child) {
                if ($child->getIsActive()) {
                    $activeChildren[] = $child;
                }
            }
        } elseif (is_array($children)) {
            foreach ($children as $child) {
                $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $child = $_objectManager->create('Magento\Catalog\Model\Category')
                    ->load($child);
                if ($child->getIsActive()) {
                    $activeChildren[] = $child;
                }
            }
        }

        $j = 0;
        foreach ($activeChildren as $child) {
            if (!$this->seoSitemapData->checkArrayPattern($this->getCategoryUrl($child), $this->getExcludeLinks())) {
                $this->categoriesTree[] = $child;
            } else {
                $arrKey = count($this->categoriesTree);
                if ($arrKey > 0) {
                    $arrKey = $arrKey - 1;
                }
                $this->categoriesTree[$arrKey.'-hidden'] = $child;
            }
            $this->_getCategoriesTree($child, $level + 1);
            ++$j;
        }
    }

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $categoryInstance;

    /**
     * @return \Magento\Catalog\Model\Category
     */
    protected function _getCategoryInstance()
    {
        if ($this->categoryInstance === null) {
            $this->categoryInstance = $this->categoryFactory->create();
        }

        return $this->categoryInstance;
    }

    /**
     * Get url for category data.
     *
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return string
     */
    public function getCategoryUrl($category)
    {
        if ($category instanceof \Magento\Catalog\Model\Category) {
            $url = $category->getUrl();
        } else {
            $url = $this->_getCategoryInstance()
                ->setData($category->getData())
                ->getUrl();
        }

        return $url;
    }

    /**
     * Return item position representation in menu tree.
     *
     * @param int $level
     *
     * @return string
     */
    protected function _getItemPosition($level)
    {
        if ($level == 0) {
            $zeroLevelPosition = isset($this->itemLevelPositions[$level]) ? $this->itemLevelPositions[$level] + 1 : 1;
            $this->itemLevelPositions = [];
            $this->itemLevelPositions[$level] = $zeroLevelPosition;
        } elseif (isset($this->itemLevelPositions[$level])) {
            ++$this->itemLevelPositions[$level];
        } else {
            $this->itemLevelPositions[$level] = 1;
        }

        $position = [];
        for ($i = 0; $i <= $level; ++$i) {
            if (isset($this->itemLevelPositions[$i])) {
                $position[] = $this->itemLevelPositions[$i];
            }
        }

        return implode('-', $position);
    }

    /**
     * @return void
     */
    protected function prepareCategoryTree()
    {
        $result = [];
        foreach ($this->categoriesTree as $key => $category) {
            if (!$this->excludeCategory($category)) {
                if (!$this->isHidden($key)) {
                    $category->setUrl($this->getCategoryUrl($category));
                    $result[] = $category;
                }
                if ($this->getConfig()->getIsShowProducts()) {
                    $products = $this->getSitemapProductCollection($category);
                    foreach ($products as $product) {
                        if ($product->getVisibility() != 1 && !$this->excludeProduct($product)) {
                            $product->setLevel($category->getLevel() + 1);
                            $product->setUrl($product->getProductUrl());
                            $result[] = $product;
                        }
                    }
                }
            }
        }
        $this->categoriesTree = $result;
    }

    /**
     * Render categories menu in HTML
     *
     * @param int    $level
     * @return array|string
     */
    public function getCategoriesTree($level = 0)
    {
        if ($this->categoriesTree) {
            return $this->categoriesTree;
        }

        $activeCategories = [];
        foreach ($this->getStoreCategories() as $child) {
            if ($child->getIsActive()) {
                $activeCategories[] = $child;
            }
        }
        $activeCategoriesCount = count($activeCategories);
        $hasActiveCategoriesCount = ($activeCategoriesCount > 0);

        if (!$hasActiveCategoriesCount) {
            return [];
        }

        $j = 0;
        foreach ($activeCategories as $category) {
            if (!$this->seoSitemapData->checkArrayPattern($this->getCategoryUrl($category), $this->getExcludeLinks())) {
                $this->categoriesTree[] = $category;
            } else {
                $arrKey = count($this->categoriesTree);
                if ($arrKey > 0) {
                    $arrKey = $arrKey - 1;
                }
                $this->categoriesTree[$arrKey.'-hidden'] = $category;
            }
            $this->_getCategoriesTree($category, $level);
            ++$j;
        }

        $this->prepareCategoryTree();

        return $this->categoriesTree;
    }

    /**
     * @param string $category
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    public function getSitemapProductCollection($category)
    {
        $category = $this->_getCategoryInstance()
                    ->setData($category->getData());
        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter()
            ->addCategoryFilter($category)
            ->addAttributeToFilter('visibility', ['neq' => 1])
            ->addAttributeToFilter('status', 1)
            ->addAttributeToSelect('*')
            ->addUrlRewrite();

        return $collection;
    }


    /**
     * @return string
     */
    public function getH1Title()
    {
        return $this->config->getFrontendSitemapH1();
    }

    /**
     * @param string $category
     * @return bool
     */
    public function excludeCategory($category)
    {
        return $this->seoSitemapData->checkArrayPattern($category->getUrl(), $this->getExcludeLinks());
    }

    /**
     * @param string $product
     * @return bool
     */
    public function excludeProduct($product)
    {
        return $this->seoSitemapData->checkArrayPattern($product->getProductUrl(), $this->getExcludeLinks());
    }

    /**
     * @return \Magento\Cms\Model\ResourceModel\Page\Collection
     */
    public function getCmsPages()
    {
        $ignore = $this->cmsSitemapConfig->getIgnoreCmsPages();
        $collection = $this->pageCollectionFactory->create()
                         ->addStoreFilter($this->context->getStoreManager()->getStore())
                         ->addFieldToFilter('is_active', true)
                         ->addFieldToFilter('main_table.identifier', ['nin' => $ignore]);

        return $collection;
    }

    /**
     * @param string $page
     * @return string
     */
    public function getCmsPageUrl($page)
    {
        $pageIdentifier = ($this->isMagentoEe && $page->getHierarchyRequestUrl()) ? $page->getHierarchyRequestUrl() :
            $page->getIdentifier();

        return $this->context->getUrlBuilder()->getUrl(null, ['_direct' => $pageIdentifier]);
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface[]
     */
    public function getStores()
    {
        return $this->context->getStoreManager()->getStores();
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isHidden($key)
    {
        if (!strpos($key, 'hidden')) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAwBlogInstalled()
    {
        if ($this->moduleManager->isEnabled('Aheadworks_Blog')) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->context->getStoreManager()->getStore()->getId();
    }


    /**
     * @return array
     */
    public function getAwBlogData()
    {
        $sitemapHelper = $this->objectManager->create('\Aheadworks\Blog\Helper\Sitemap');
        $urlHelper = $this->objectManager->create('Aheadworks\Blog\Helper\Url');
        $categoryCollectionFactory = $this->objectManager
            ->create('\Aheadworks\Blog\Model\ResourceModel\Category\CollectionFactory');
        $postCollectionFactory = $this->objectManager
            ->create('\Aheadworks\Blog\Model\ResourceModel\Post\CollectionFactory');

        $storeId = $this->context->getStoreManager()->getStore()->getId();
        $items = [];
        $home = $sitemapHelper->getBlogItem($storeId)->getCollection();

        if (isset($home[0]) && is_object($home[0])) {
            $items['home'] = new \Magento\Framework\DataObject(
                [
                    'name' => 'Blog Home',
                    'url' => $home[0]->getUrl()
                ]
            );
        }

        $categoryCollection = $categoryCollectionFactory->create()
            ->addEnabledFilter()
            ->addStoreFilter($storeId);
        foreach ($categoryCollection as $category) {
            $items['cat' . $category->getId()] = new \Magento\Framework\DataObject(
                [
                    'name' => $category->getName(),
                    'url' => $urlHelper->getCategoryRoute($category)
                ]
            );
        }

        $postCollection = $postCollectionFactory->create()
            ->addPublishedFilter()
            ->addStoreFilter($storeId);

        foreach ($postCollection as $post) {
            $items['post' . $post->getId()] = new \Magento\Framework\DataObject(
                [
                    'name' => $post->getTitle(),
                    'url' => $urlHelper->getPostRoute($post)
                ]
            );
        }

        return $items;
    }
}
