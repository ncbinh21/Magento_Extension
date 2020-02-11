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
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Helper;

use Mirasvit\Seo\Model\Config as Config;
use Mirasvit\Seo\Api\Config\AlternateConfigInterface as AlternateConfig;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Mirasvit\Seo\Model\SeoObject\StoreFactory
     */
    protected $objectStoreFactory;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\PagerFactory
     */
    protected $objectPagerFactory;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory
     */
    protected $objectWrapperFilterFactory;

    /**
     * @var \Mirasvit\Seo\Model\TemplateFactory
     */
    protected $templateFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var \Magento\Bundle\Model\Product\TypeFactory
     */
    protected $productTypeFactory;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Mirasvit\Seo\Model\ResourceModel\Rewrite\CollectionFactory
     */
    protected $rewriteCollectionFactory;

    /**
     * @var \Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory
     */
    protected $templateCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;

    /**
     * @var \Mirasvit\Core\Model\Url
     */
    protected $url;

    /**
     * @var \Mirasvit\Seo\Helper\Parse
     */
    protected $seoParse;

    /**
     * @var \Mirasvit\Core\Helper\String
     */
    protected $coreString;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $taxData;

    /**
     * @var \Magento\Framework\App\Helper\Context
     */
    protected $context;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Mirasvit\Seo\Model\SeoDataFactory
     */
    protected $seoDataFactory;

    /**
     * @var \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable
     */
    protected $productTypeConfigurable;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Model\Category
     */
    protected $category;

    /**
     * @var \Mirasvit\Seo\Helper\StringPrepare
     */
    protected $stringPrepare;

    /**
     * @var array
     */
    protected $parseObjects = [];
    /**
     * @var array
     */
    protected $additional = [];
    /**
     * @var int
     */
    protected $storeId = null;
    /**
     * @var bool
     */
    protected $titlePage = true;

    /**
     * @var bool
     */
    protected $descriptionPage = true;

    /**
     * @param \Mirasvit\Seo\Model\Config                                                 $config
     * @param \Mirasvit\Seo\Model\SeoObject\StoreFactory                                 $objectStoreFactory
     * @param \Mirasvit\Seo\Model\SeoObject\PagerFactory                                 $objectPagerFactory
     * @param \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory                        $objectWrapperFilterFactory
     * @param \Mirasvit\Seo\Model\TemplateFactory                                        $templateFactory
     * @param \Magento\Catalog\Model\CategoryFactory                                     $categoryFactory
     * @param \Magento\Bundle\Model\Product\TypeFactory                                  $productTypeFactory
     * @param \Magento\Directory\Model\CurrencyFactory                                   $currencyFactory
     * @param \Mirasvit\Seo\Model\ResourceModel\Rewrite\CollectionFactory                $rewriteCollectionFactory
     * @param \Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory               $templateCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory            $categoryCollectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory             $productCollectionFactory
     * @param \Magento\Catalog\Model\Layer\Resolver                                      $layerResolver
     * @param \Mirasvit\Seo\Helper\Parse                                                 $seoParse
     * @param \Mirasvit\Core\Api\TextHelperInterface                                     $coreString
     * @param \Magento\Tax\Helper\Data                                                   $taxData
     * @param \Magento\Framework\App\Helper\Context                                      $context
     * @param \Magento\Store\Model\StoreManagerInterface                                 $storeManager
     * @param \Magento\Framework\Registry                                                $registry
     * @param \Mirasvit\Seo\Model\SeoDataFactory                                         $seoDataFactory
     * @param \Magento\Framework\Stdlib\StringUtils                                      $string
     * @param \Magento\Theme\Block\Html\Header\Logo                                      $logo
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productTypeConfigurable
     * @param \Magento\Catalog\Api\ProductRepositoryInterface                            $productRepository
     * @param \Mirasvit\Seo\Helper\StringPrepare                                         $stringPrepare
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Seo\Model\Config $config,
        \Mirasvit\Seo\Model\SeoObject\StoreFactory $objectStoreFactory,
        \Mirasvit\Seo\Model\SeoObject\PagerFactory $objectPagerFactory,
        \Mirasvit\Seo\Model\SeoObject\Wrapper\FilterFactory $objectWrapperFilterFactory,
        \Mirasvit\Seo\Model\TemplateFactory $templateFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Bundle\Model\Product\TypeFactory $productTypeFactory,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Mirasvit\Seo\Model\ResourceModel\Rewrite\CollectionFactory $rewriteCollectionFactory,
        \Mirasvit\Seo\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Mirasvit\Seo\Helper\Parse $seoParse,
        \Mirasvit\Core\Api\TextHelperInterface $coreString,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Seo\Model\SeoDataFactory $seoDataFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $productTypeConfigurable,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Mirasvit\Seo\Helper\StringPrepare $stringPrepare
    ) {
        $this->config = $config;
        $this->objectStoreFactory = $objectStoreFactory;
        $this->objectPagerFactory = $objectPagerFactory;
        $this->objectWrapperFilterFactory = $objectWrapperFilterFactory;
        $this->templateFactory = $templateFactory;
        $this->categoryFactory = $categoryFactory;
        $this->productTypeFactory = $productTypeFactory;
        $this->currencyFactory = $currencyFactory;
        $this->rewriteCollectionFactory = $rewriteCollectionFactory;
        $this->templateCollectionFactory = $templateCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->layerResolver = $layerResolver;
        //        $this->url = $url;
        $this->seoParse = $seoParse;
        $this->coreString = $coreString;
        $this->taxData = $taxData;
        $this->context = $context;
        $this->storeManager = $storeManager;
        $this->registry = $registry;
        $this->request = $context->getRequest();
        $this->seoDataFactory = $seoDataFactory;
        $this->string = $string;
        $this->logo = $logo;
        $this->productTypeConfigurable = $productTypeConfigurable;
        $this->productRepository = $productRepository;
        $this->stringPrepare = $stringPrepare;
    }

    /**
     * @return mixed|string
     */
    public function getBaseUri()
    {
        if (!isset($_SERVER['REQUEST_URI'])) {
            return false;
        }

        $baseStoreUri = parse_url($this->context->getUrlBuilder()->getUrl(), PHP_URL_PATH);

        if ($baseStoreUri == '/') {
            return $_SERVER['REQUEST_URI'];
        } else {
            $requestUri = $_SERVER['REQUEST_URI'];
            $prepareUri = str_replace($baseStoreUri, '', $requestUri);
            if (substr($prepareUri, 0, 1) == '/') {
                return $prepareUri;
            } else {
                return '/' . $prepareUri;
            }
        }
    }

    /**
     * @param bool|false $info
     * @return bool
     */
    public function checkRewrite($info = false)
    {
        $uri = $this->getBaseUri();
        $collection = $this->rewriteCollectionFactory->create()
            ->addStoreFilter($this->storeManager->getStore())
            ->addEnableFilter();
        $resultRewrite = false;
        foreach ($collection as $rewrite) {
            if ($this->checkPattern($uri, $rewrite->getUrl())) {
                $resultRewrite = $rewrite;
                break;
            }
        }

        if ($info && $resultRewrite) {
            return $resultRewrite;
        } elseif ($info) {
            return false;
        }

        if ($resultRewrite) {
            $this->_addParseObjects();
            $resultRewrite->setTitle($this->seoParse->parse(
                $resultRewrite->getTitle(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $resultRewrite->setDescription($this->seoParse->parse(
                $resultRewrite->getDescription(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $resultRewrite->setMetaTitle($this->seoParse->parse(
                $resultRewrite->getMetaTitle(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $resultRewrite->setMetaKeywords(
                $this->seoParse->parse(
                    $resultRewrite->getMetaKeywords(),
                    $this->parseObjects,
                    $this->additional,
                    $this->storeId
                )
            );
            $resultRewrite->setMetaDescription(
                $this->seoParse->parse(
                    $resultRewrite->getMetaDescription(),
                    $this->parseObjects,
                    $this->additional,
                    $this->storeId
                )
            );
        }

        return $resultRewrite;
    }

    /**
     * @param string $objectName
     * @param string $variableName
     * @param string $value
     *
     * @return void
     */
    protected function setAdditionalVariable($objectName, $variableName, $value)
    {
        $this->additional[$objectName][$variableName] = $value;
        if (isset($this->parseObjects['product'])) {
            if ($objectName . '_' . $variableName == 'product_final_price_minimal') {
                $this->parseObjects['product']->setData('final_price_minimal', $value);
            }
            if ($objectName . '_' . $variableName == 'product_final_price_range') {
                $this->parseObjects['product']->setData('final_price_range', $value);
            }
        }
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _addParseObjects()
    {
        if ($this->parseObjects && $this->storeId !== null) {
            return;
        }

        $this->product = $this->registry->registry('currentproduct');
        if (!$this->product) {
            $this->product = $this->registry->registry('product');
        }
        if ($this->product) {
            $this->parseObjects['product'] = $this->product;
            $this->setAdditionalVariable('product', 'final_price', $this->product->getFinalPrice());
            $this->setAdditionalVariable('product', 'url', $this->product->getProductUrl());
            $this->setAdditionalVariable(
                'product',
                'final_price_minimal',
                $this->getCurrentProductFinalPrice($this->product)
            );
            $this->setAdditionalVariable(
                'product',
                'final_price_range',
                $this->getCurrentProductFinalPriceRange($this->product)
            );
        }

        $this->category = $this->registry->registry('current_category');

        $this->parseObjects['store'] = $this->objectStoreFactory->create();
        $this->parseObjects['pager'] = $this->objectPagerFactory->create();
        $this->parseObjects['filter'] = $this->objectWrapperFilterFactory->create();

        if ($this->category) {
            $this->parseObjects['category'] = $this->category;
            if ($this->category && $parent = $this->category->getParentCategory()) {
                $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
                if ($parent->getId() > $rootCategoryId) {
                    if (($parentParent = $parent->getParentCategory())
                            && ($parentParent->getId() > $rootCategoryId)
                        ) {
                        $this->setAdditionalVariable('category', 'parent_parent_name', $parentParent->getName());
                    }
                        $this->setAdditionalVariable('category', 'parent_name', $parent->getName());
                        $this->setAdditionalVariable('category', 'parent_url', $parent->getUrl());
                }
                $this->setAdditionalVariable('category', 'url', $this->category->getUrl());
                //alias to meta_title
                $this->setAdditionalVariable('category', 'page_title', $this->category->getMetaTitle());
            }
        }

        $this->storeId = $this->storeManager->getStore();

        return;
    }

    /**
     * @param string $productId
     * @param string $categoryId
     * @param string $item
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    protected function _getElementApplied(
        $productId,
        $categoryId,
        $item
    ) {
        if ($productId) {
            $isElementApplied = $this->templateFactory
                ->create()
                ->getRule($item->getTemplateId())
                ->isProductApplied($productId);
        } else {
            $isElementApplied = $this->templateFactory
                ->create()
                ->getRule($item->getTemplateId())
                ->isCategoryApplied($categoryId);
        }

        return $isElementApplied;
    }

    /**
     * @param string $collection
     * @param string $productId
     * @param string $categoryId
     * @param string $info
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getTempalateRule($collection, $productId, $categoryId, $info)
    {
        $seoTemplateRule = [];
        $sortOrderAppliedId = false;
        $stopRulesProcessingAppliedId = false;

        foreach ($collection as $item) {
            if ($this->_getElementApplied($productId, $categoryId, $item)) {
                $seoTemplateRule[$item->getId()] = $item;
                if ($item->getStopRulesProcessing() && !$stopRulesProcessingAppliedId) {
                    $stopRulesProcessingAppliedId = $item->getId();
                }
                if ($item->getSortOrder() && !$stopRulesProcessingAppliedId) {
                    $sortOrderAppliedId = $item->getId();
                }
            }
        }

        if ($info) {
            if ($stopRulesProcessingAppliedId) {
                $seoTemplateRule['applied'] = $stopRulesProcessingAppliedId; // stop rules processing
                $seoTemplateRule['stop_rules_processing'] = true;
            } elseif ($sortOrderAppliedId) {
                $seoTemplateRule['applied'] = $sortOrderAppliedId; // sort order
                $seoTemplateRule['sort_order'] = true;
            } elseif ($seoTemplateRule) {
                $seoTemplateRule['applied'] = key(array_slice($seoTemplateRule, -1, 1, true)); // maximal ID
            }

            return $seoTemplateRule;
        }

        if ($stopRulesProcessingAppliedId) {
            $seoTemplateRule = $seoTemplateRule[$stopRulesProcessingAppliedId]; // stop rules processing
        } elseif ($sortOrderAppliedId) {
            $seoTemplateRule = $seoTemplateRule[$sortOrderAppliedId]; // sort order
        } else {
            $seoTemplateRule = array_pop($seoTemplateRule); // maximal ID
        }

        return $seoTemplateRule;
    }

    /**
     * @param string $isProduct
     * @param string $isCategory
     * @param string $isFilter
     * @param bool   $info
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function checkTempalateRule($isProduct, $isCategory, $isFilter, $info = false)
    {
        $seoTemplateRule = [];

        if ($isProduct) {
            // echo 'PRODUCTS_RULE';
            if (!$this->registry->registry('current_product')) {
                return false;
            }

            $collectionProduct = $this->templateCollectionFactory->create()
                ->addStoreFilter($this->storeManager->getStore())
                ->addFieldToFilter('rule_type', Config::PRODUCTS_RULE)
                ->addActiveFilter()
                ->addSortOrder();

            $seoTemplateRule = $this->_getTempalateRule(
                $collectionProduct,
                $this->registry->registry('current_product')->getId(),
                false,
                $info
            );
        } elseif ($isCategory && $isFilter) {
            // echo 'RESULTS_LAYERED_NAVIGATION_RULE';
            if (!$this->registry->registry('current_category')) {
                return false;
            }

            $collectionLayeredNavigation = $this->templateCollectionFactory->create()
                ->addStoreFilter($this->storeManager->getStore())
                ->addFieldToFilter(
                    'rule_type',
                    Config::RESULTS_LAYERED_NAVIGATION_RULE
                )
                ->addActiveFilter()
                ->addSortOrder();

            $seoTemplateRule = $this->_getTempalateRule(
                $collectionLayeredNavigation,
                false,
                $this->registry->registry('current_category')->getId(),
                $info
            );
        } elseif ($isCategory) {
            // echo 'CATEGORIES_RULE';
            if (!$this->registry->registry('current_category')) {
                return false;
            }

            $categoryCollection = $this->templateCollectionFactory->create()
                ->addStoreFilter($this->storeManager->getStore())
                ->addFieldToFilter('rule_type', Config::CATEGORIES_RULE)
                ->addActiveFilter()
                ->addSortOrder();

            $seoTemplateRule = $this->_getTempalateRule(
                $categoryCollection,
                false,
                $this->registry->registry('current_category')->getId(),
                $info
            );
        }

        if ($info) {
            return $seoTemplateRule;
        }

        if ($seoTemplateRule) {
            $this->_addParseObjects();
            $seoTemplateRule->setTitle($this->seoParse->parse(
                $seoTemplateRule->getTitle(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $seoTemplateRule->setDescription($this->seoParse->parse(
                $seoTemplateRule->getDescription(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $seoTemplateRule->setMetaTitle($this->seoParse->parse(
                $seoTemplateRule->getMetaTitle(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $seoTemplateRule->setMetaKeywords($this->seoParse->parse(
                $seoTemplateRule->getMetaKeywords(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $seoTemplateRule->setMetaDescription($this->seoParse->parse(
                $seoTemplateRule->getMetaDescription(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $seoTemplateRule->setShortDescription($this->seoParse->parse(
                $seoTemplateRule->getShortDescription(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
            $seoTemplateRule->setFullDescription($this->seoParse->parse(
                $seoTemplateRule->getFullDescription(),
                $this->parseObjects,
                $this->additional,
                $this->storeId
            ));
        }

        return $seoTemplateRule;
    }

    /**
     * Возвращает сео-данные для текущей страницы.
     *
     * Возвращает объект с методами:
     * getTitle() - заголовок H1
     * getDescription() - SEO текст
     * getMetaTitle()
     * getMetaKeyword()
     * getMetaDescription()
     *
     * If the page is not SEO, it returns an empty \Magento\Framework\DataObject
     *
     * @return \Mirasvit\Seo\Model\SeoData
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function getCurrentSeo()
    {
        if ($this->storeManager->getStore()->getCode() == 'admin') {
            return $this->seoDataFactory->create();
        }

        $isCategory = $this->registry->registry('current_category') || $this->registry->registry('category');
        $isProduct = $this->registry->registry('current_product') || $this->registry->registry('product');
        $isFilter = false;

        if ($isCategory) {
            $filters = $this->layerResolver->get()->getState()->getFilters();
            $isFilter = count($filters) > 0;
        }

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        if (($seoTempalate = $this->checkTempalateRule($isProduct, $isCategory, $isFilter))
            && !$this->registry->registry('m_seo_template_data')
        ) {
            $this->registry->register('m_seo_template_data', $seoTempalate->getData());
        }

        if ($isProduct) {
            $seo = $objectManager->get('\Mirasvit\Seo\Model\SeoObject\Product');
        } elseif ($isCategory) {
            $seo = $objectManager->get('Mirasvit\Seo\Model\SeoObject\Category');
        } else {
            $seo = $this->seoDataFactory->create();
        }

        if ($seoTempalate) {
            foreach ($seoTempalate->getData() as $k => $v) {
                if ($v && $this->_isSeoTempalateUsed($k, $isProduct, $isCategory)) {
                    $seo->setData($k, $v);
                }
            }
        }

        if ($seoRewrite = $this->checkRewrite()) {
            foreach ($seoRewrite->getData() as $k => $v) {
                if ($v) {
                    $seo->setData($k, $v);
                }
            }
        }

        $storeId = $this->storeManager->getStore()->getStoreId();
        $page = $this->request->getParam('p');
        if (!$page) {
            $page = 1;
        }

        if ($isCategory && !$isProduct) {
            if ($this->titlePage) {
                switch ($this->config->getMetaTitlePageNumber($storeId)) {
                    case Config::META_TITLE_PAGE_NUMBER_BEGIN:
                        if ($page > 1) {
                            $seo->setMetaTitle(__('Page %1 | %2', $page, $seo->getMetaTitle())->render());
                            $this->titlePage = false;
                        }
                        break;
                    case Config::META_TITLE_PAGE_NUMBER_END:
                        if ($page > 1) {
                            $seo->setMetaTitle(__('%1 | Page %2', $seo->getMetaTitle(), $page)->render());
                            $this->titlePage = false;
                        }
                        break;
                    case Config::META_TITLE_PAGE_NUMBER_BEGIN_FIRST_PAGE:
                        $seo->setMetaTitle(__('Page %1 | %2', $page, $seo->getMetaTitle())->render());
                        $this->titlePage = false;
                        break;
                    case Config::META_TITLE_PAGE_NUMBER_END_FIRST_PAGE:
                        $seo->setMetaTitle(__('%1 | Page %2', $seo->getMetaTitle(), $page)->render());
                        $this->titlePage = false;
                        break;
                }
            }

            if ($this->descriptionPage) {
                switch ($this->config->getMetaDescriptionPageNumber($storeId)) {
                    case Config::META_DESCRIPTION_PAGE_NUMBER_BEGIN:
                        if ($page > 1) {
                            $seo->setMetaDescription(__('Page %1 | %2', $page, $seo->getMetaDescription())->render());
                            $this->descriptionPage = false;
                        }
                        break;
                    case Config::META_DESCRIPTION_PAGE_NUMBER_END:
                        if ($page > 1) {
                            $seo->setMetaDescription(__('%1 | Page %2', $seo->getMetaDescription(), $page)->render());
                            $this->descriptionPage = false;
                        }
                        break;
                    case Config::META_DESCRIPTION_PAGE_NUMBER_BEGIN_FIRST_PAGE:
                        $seo->setMetaDescription(__('Page %1 | %2', $page, $seo->getMetaDescription())->render());
                        $this->descriptionPage = false;
                        break;
                    case Config::META_DESCRIPTION_PAGE_NUMBER_END_FIRST_PAGE:
                        $seo->setMetaDescription(__('%1 | Page %2', $seo->getMetaDescription(), $page)->render());
                        $this->descriptionPage = false;
                        break;
                }
            }

            if ($page > 1) {
                $seo->setDescription('');
                //set an empty description for page with number > 1 (to not have a duplicate content)
            }
        }

        $seo = $this->_applyMaxLenth($storeId, $seo, $page);

        return $seo;
    }

    /**
     * Crop Meta Title, Meta Description, Product Name, Product Short Description
     *
     * @param int $storeId
     * @param Mirasvit\Seo\Model\SeoObject\Product $seo
     * @param int $page
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _applyMaxLenth($storeId, $seo, $page)
    {
        if ($metaTitleMaxLength = $this->config->getMetaTitleMaxLength($storeId)) {
            $metaTitleMaxLength = (int)$metaTitleMaxLength;
            if ($metaTitleMaxLength < Config::META_TITLE_INCORRECT_LENGTH) {
                $metaTitleMaxLength = Config::META_TITLE_MAX_LENGTH; //recommended length
            }

            $seo->setMetaTitle($this->stringPrepare
                ->getTruncatedString($seo->getMetaTitle(), $metaTitleMaxLength, $page));
        }

        if ($metaDescriptionMaxLength = $this->config->getMetaDescriptionMaxLength($storeId)) {
            $metaDescriptionMaxLength = (int)$metaDescriptionMaxLength;
            if ($metaDescriptionMaxLength < Config::META_DESCRIPTION_INCORRECT_LENGTH) {
                $metaDescriptionMaxLength = Config::META_DESCRIPTION_MAX_LENGTH; //recommended length
            }
            $seo->setMetaDescription($this->stringPrepare->getTruncatedString(
                $seo->getMetaDescription(),
                $metaDescriptionMaxLength,
                $page
            ));
        }

        $isProductPage = ($this->getFullActionCode() == 'catalog_product_view') ? true : false;

        if ($isProductPage && $productNameMaxLength = $this->config->getProductNameMaxLength($storeId)) {
            $productNameMaxLength = (int)$productNameMaxLength;
            if ($productNameMaxLength < Config::RODUCT_NAME_INCORRECT_LENGTH) {
                $productNameMaxLength = Config::PRODUCT_NAME_MAX_LENGTH; //recommended length
            }
            $seo->setTitle($this->stringPrepare->getTruncatedString(
                $seo->getTitle(),
                $productNameMaxLength,
                $page
            ));
        }

        if ($isProductPage
            && $productShortDescriptionMaxLength = $this->config->getProductShortDescriptionMaxLength($storeId)) {
                $productShortDescriptionMaxLength = (int)$productShortDescriptionMaxLength;
            if ($productShortDescriptionMaxLength < Config::PRODUCT_SHORT_DESCRIPTION_INCORRECT_LENGTH) {
                $productShortDescriptionMaxLength = Config::PRODUCT_SHORT_DESCRIPTION_MAX_LENGTH; //recommended length
            }
                $seo->setShortDescription($this->stringPrepare->getTruncatedString(
                    $seo->getShortDescription(),
                    $productShortDescriptionMaxLength,
                    $page
                ));
        }

        return $seo;
    }

    /**
     * @param string $templateKey
     * @return bool
     */
    protected function _isSeoTempalateUsed($templateKey)
    {
        $templateKeyApplied = ['meta_title', 'meta_keywords', 'meta_keyword', 'meta_description'];
        if (in_array($templateKey, $templateKeyApplied)) {
            return false;
        }

        return true;
    }

    /**
     * Get SeoShortDescription for Sphinx Search.
     *
     * @param string $product
     * @return bool|string
     */
    public function getCurrentSeoShortDescriptionForSearch($product)
    {
        if ($this->storeManager->getStore()->getCode() == 'admin') {
            return false;
        }

        $categoryIds = $product->getCategoryIds();
        $rootCategoryId = $this->storeManager->getStore()->getRootCategoryId();
        array_unshift($categoryIds, $rootCategoryId);
        $categoryIds = array_reverse($categoryIds);
        $storeId = $this->storeManager->getStore()->getStoreId();
        $seoShortDescription = false;
        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryFactory->create()->setStoreId($storeId)->load($categoryId);
            if ($seoShortDescription = $category->getProductShortDescriptionTpl()) {
                break;
            }
        }

        if ($seoShortDescription) {
            $this->parseObjects['product'] = $product;
            $seoShortDescription = $this->seoParse->parse(
                $seoShortDescription,
                $this->parseObjects,
                $this->additional,
                $storeId
            );
        }

        return $seoShortDescription;
    }

    /**
     * @param string     $string
     * @param string     $pattern
     * @param bool|false $caseSensative
     * @return bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function checkPattern($string, $pattern, $caseSensative = false)
    {
        if (!$caseSensative) {
            $string = strtolower($string);
            $pattern = strtolower($pattern);
        }

        $parts = explode('*', $pattern);
        $index = 0;

        $shouldBeFirst = true;

        foreach ($parts as $part) {
            if ($part == '') {
                $shouldBeFirst = false;
                continue;
            }

            $index = strpos($string, $part, $index);

            if ($index === false) {
                return false;
            }

            if ($shouldBeFirst && $index > 0) {
                return false;
            }

            $shouldBeFirst = false;
            $index += strlen($part);
        }

        if (count($parts) == 1) {
            return $string == $pattern;
        }

        $last = end($parts);
        if ($last == '') {
            return true;
        }

        if (strrpos($string, $last) === false) {
            return false;
        }

        if (strlen($string) - strlen($last) - strrpos($string, $last) > 0) {
            return false;
        }

        return true;
    }

    /**
     * @param string $tag
     * @return string
     */
    public function cleanMetaTag($tag)
    {
        $tag = strip_tags($tag);
        $tag = preg_replace('/\s{2,}/', ' ', $tag); //remove unnecessary spaces
        $tag = preg_replace('/\"/', ' ', $tag); //remove " because it destroys html
        $tag = trim($tag);

        return $tag;
    }

    /**
     * @param string $code
     * @return string
     */
    public function getMetaRobotsByCode($code)
    {
        switch ($code) {
            case Config::NOINDEX_NOFOLLOW:
                return 'NOINDEX,NOFOLLOW';
            case Config::NOINDEX_FOLLOW:
                return 'NOINDEX,FOLLOW';
            case Config::INDEX_NOFOLLOW:
                return 'INDEX,NOFOLLOW';
        };
    }

    /**
     * @param string $product
     * @return \Magento\Catalog\Model\Category
     */
    public function getProductSeoCategory($product)
    {
        $categoryId = $product->getSeoCategory();
        $category = $this->registry->registry('current_category');

        if ($category && !$categoryId) {
            return $category;
        }

        if (!$categoryId) {
            $categoryIds = $product->getCategoryIds();
            if (count($categoryIds) > 0) {
                //we need this for multi websites configuration
                $categoryRootId = $this->storeManager->getStore()->getRootCategoryId();
                $category = $this->categoryCollectionFactory->create()
                    ->addFieldToFilter('path', ['like' => "%/{$categoryRootId}/%"])
                    ->addFieldToFilter('entity_id', $categoryIds)
                    ->setOrder('level', 'desc')
                    ->setOrder('entity_id', 'desc')
                    ->getFirstItem();
                $categoryId = $category->getId();
            }
        }
        //load category with flat data attributes
        $category = $this->categoryFactory->create()->load($categoryId);

        return $category;
    }

    /**
     * @return array
     */
    public function getInactiveCategories()
    {
        $inactiveCategories = $this->categoryFactory->create()
            ->getCollection()
            ->setStoreId($this->storeManager->getStore()->getId())
            ->addFieldToFilter('is_active', ['neq' => '1'])
            ->addAttributeToSelect('*');
        $inactiveCat = [];
        foreach ($inactiveCategories as $inactiveCategory) {
            $inactiveCat[] = $inactiveCategory->getId();
        }

        return $inactiveCat;
    }

    /**
     * @param string $params
     * @return bool|string
     */
    public function getTagProductListUrl($params)
    {
        $request = $this->request;
        $fullActionCode = $request->getModuleName() . '_' . $request->getControllerName()
            . '_' . $request->getActionName();
        if ($fullActionCode == 'tag_product_list') {
            $urlParams = [];
            if (isset($params['p']) && $params['p'] == 1) {
                unset($params['p']);
            }
            $urlParams['_query'] = $params;
            $urlKeysArray = [
                '_nosid' => true,
                '_type'  => 'direct_link',
            ];

            $urlParams = array_merge($urlParams, $urlKeysArray);
            $path = $this->url->parseUrl($this->context->getUrlBuilder()->getCurrentUrl())->getPath();
            $path = (substr($path, 0, 1) == '/') ? substr($path, 1) : $path;

            return $this->context->getUrlBuilder()->getUrl($path, $urlParams);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getFullActionCode()
    {
        $result = strtolower($this->request->getModuleName() . '_' . $this->request->getControllerName()
            . '_' . $this->request->getActionName());

        return $result;
    }

    /**
     * @return bool
     */
    public function isOnLandingPage()
    {
        return $this->request->getParam('am_landing');
    }

    /**
     * @param string     $product
     * @param bool|false $noSymbol
     * @return bool|float
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function getCurrentProductFinalPrice($product, $noSymbol = false)
    {
        $productFinalPrice = false;
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $priceModel = $product->getPriceModel();

        if ($product->getTypeId() == 'grouped') {
            $finalPrice = $this->_getGroupedMinimalPrice($product);
        } else {
            //var1, works not for every store
            //$finalPrice = $priceModel->getFinalPrice(null, $product);
            //var2 - final price with tax
            $finalPrice = $product->getPriceInfo()->getPrice('final_price')->getAmount()->getValue();
        }

        if ($finalPrice && ($finalPrice = $this->_formatPrice($finalPrice, $noSymbol)) && $currencyCode) {
            $productFinalPrice = $finalPrice;
        }

        if ($productFinalPrice) {
            return $productFinalPrice;
        }

        return false;
    }

    /**
     * @param string $product
     * @return bool|string
     */
    public function getCurrentProductFinalPriceRange($product)
    {
        $productFinalPrice = false;
        $currencyCode = $this->storeManager->getStore()->getCurrentCurrencyCode();
        $priceModel = $product->getPriceModel();

        if ($product->getTypeId() == 'grouped') {
            $finalPrice = $this->_getGroupedPriceRange($product);
        } elseif ($product->getTypeId() == 'configurable') {
            $finalPrice = $this->_getConfigurablePriceRange($product);
        } else {
            $finalPrice = $priceModel->getFinalPrice(null, $product);
            $finalPrice = $this->_formatPrice($finalPrice, false);
        }

        if ($finalPrice && $currencyCode) {
            $productFinalPrice = $finalPrice;
        }

        if ($productFinalPrice) {
            return $productFinalPrice;
        }

        return false;
    }

    /**
     * @param string    $price
     * @param bool|true $noSymbol
     * @return bool
     */
    protected function _formatPrice($price, $noSymbol = true)
    {
        $displaySymbol = $noSymbol ? [
            'display' => \Zend_Currency::NO_SYMBOL
        ] : ['display' => \Zend_Currency::USE_SYMBOL];
        if (intval($price)) {
            $price = $this->currencyFactory->create()->format(
                $price,
                $displaySymbol,
                false
            );

            return $price;
        }

        return false;
    }

    /**
     * @param string $product
     * @return float
     */
    protected function _getGroupedMinimalPrice($product)
    {
        $product = $this->productCollectionFactory->create()
            ->addMinimalPrice()
            ->addFieldToFilter('entity_id', $product->getId())
            ->getFirstItem();

        return $product->getMinimalPrice();
    }

    /**
     * @param string $product
     * @return bool|string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     */
    protected function _getGroupedPriceRange($product)
    {
        $groupedPrices = [];
        $typeInstance = $product->getTypeInstance(true);
        $associatedProducts = $typeInstance->setStoreFilter($product->getStore(), $product)
            ->getAssociatedProducts($product);

        foreach ($associatedProducts as $childProduct) {
            if ($childProduct->isAvailable() && ($childProduct->isSaleable() || $childProduct->getIsInStock() > 0)) {
                $groupedPrices[] = $childProduct->getFinalPrice(1);
            }
        }
        if (count($groupedPrices)
            && ($minGroupedPrice = min($groupedPrices))
            && ($maxGroupedPrice = max($groupedPrices))
            && $minGroupedPrice != $maxGroupedPrice
        ) {
            $groupedPriceRange = $this->_formatPrice($minGroupedPrice, false)
                . ' - ' . $this->_formatPrice($maxGroupedPrice, false);
        } else {
            $groupedPriceRange = $this->_getGroupedMinimalPrice($product);
        }

        return $groupedPriceRange;
    }

    /**
     * @param string $product
     * @return bool|string
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _getConfigurablePriceRange($product)
    {
        $price = [];
        $childProductIds = $this->productTypeConfigurable->getChildrenIds($product->getId());

        if (isset($childProductIds[0])) {
            foreach ($childProductIds[0] as $childProductId) {
                $childProduct = $this->productRepository->getById($childProductId);
                $priceModel = $childProduct->getPriceModel();
                $price [] = $priceModel->getFinalPrice(null, $childProduct);
            }
        }

        if (count($price)
            && ($minPrice = min($price))
            && ($maxPrice = max($price))
            && $minPrice != $maxPrice
        ) {
            $priceRange = $this->_formatPrice($minPrice, false)
                . ' - ' . $this->_formatPrice($maxPrice, false);
        } elseif (count($price) && ($minPrice = min($price))) {
            $priceRange = $this->_formatPrice($minPrice, false);
        }

        if (!isset($priceRange)) {
            $priceRange = false;
        }

        return $priceRange;
    }

    /**
     * @return string
     */
    public function getLogoUrl()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * Ignored actions for controller_action_postdispatch and controller_front_send_response_before.
     *
     * @return bool
     */
    public function isIgnoredActions()
    {
        //@todo add all account pages
        $ignoredActions = ['review_product_listajax', 'customer_address_form', 'customer_address_index'];
        if (in_array($this->getFullActionCode(), $ignoredActions)) {
            return true;
        }

        if (strpos($this->getFullActionCode(), 'paypal_express') !== false) {
            return true;
        }

        if ($this->request->isAjax()) {
            return true;
        }

        return false;
    }

    /**
     * Cancel ignored actions (other extension use plugin for isIgnoredActions function)
     *
     * @return bool
     */
    public function cancelIgnoredActions()
    {
        $cancelIgnoredActions = [AlternateConfig::AMASTY_XLANDING];
        if (in_array($this->getFullActionCode(), $cancelIgnoredActions)) {
            return true;
        }

        return false;
    }

    /**
     * Ignored urls.
     *
     * @return bool
     */
    public function isIgnoredUrls()
    {
        $ignoredUrlParts = ['checkout/', 'onestepcheckout'];
        $currentUrl = $this->context->getUrlBuilder()->getCurrentUrl();
        foreach ($ignoredUrlParts as $urlPart) {
            if (strpos($currentUrl, $urlPart)) {
                return true;
            }
        }

        return false;
    }
}
