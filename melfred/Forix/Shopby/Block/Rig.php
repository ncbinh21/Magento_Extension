<?php

namespace Forix\Shopby\Block;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Forix\Shopby\Helper\Data;


class Rig extends \Magento\Framework\View\Element\Template
{
    protected $_helper;
    /**
     * @var \Magento\Catalog\Model\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryCollectionFactory;

    protected $_categoryCollection;

    /**
     * @var \Magento\Catalog\Model\Layer
     */
    protected $catalogLayer;
    /**
     * @var \Magento\Catalog\Model\Layer\FilterList
     */
    protected $filterList;


    public function __construct(
        Template\Context $context,
        StoreManagerInterface $storeManager,
        Data $helper,
        \Magento\Catalog\Model\CategoryRepository $categoryRepository,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Model\Layer\FilterList $filterList,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->categoryRepository = $categoryRepository;
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
        $this->catalogLayer = $layerResolver->get();
        $this->filterList = $filterList;
    }

    public function _prepareLayout()
    {
        $params = $this->_helper->getRequestParams();
        $params['manufacturer'] = str_replace("_", "/", $params['manufacturer']);

        $params['rig_title'] = str_replace("_", "/", $params['rig_title']);
        $this->pageConfig->getTitle()->set('Hdd tools for ' . $params['manufacturer'] . ' ' . $params['rig_title']);
        $this->pageConfig->setDescription('Hdd tools for ' . $params['manufacturer'] . ' ' . $params['rig_title']);
        return parent::_prepareLayout();
    }

    protected function getCategoryCollection()
    {
        if (!$this->_categoryCollection) {
            $idExcludes = array_map('trim', explode(',', $this->_scopeConfig->getValue('banner_rig/general/category_exclude') ?: ''));
            $idExcludes[] = 2;
            $collection = $this->_categoryCollectionFactory->create();
            $collection->addFieldToFilter('entity_id', ['nin' => $idExcludes]);
            $collection->addAttributeToSelect('icon_image');
            $collection->addAttributeToSelect('name');
            $collection->addAttributeToSelect('is_active');
            $collection->addAttributeToSelect('is_anchor');
            $collection->setOrder('position', 'ASC');
            $this->_categoryCollection = $collection;
        }
        return $this->_categoryCollection;
    }

    public function getCategory()
    {
        $finalFilters = [];
        /** @var  $layer \Magento\Catalog\Model\Layer\Category */
        $layer = $this->catalogLayer;

        $filters = $this->filterList->getFilters($layer);
        foreach ($filters as $filter) {
            $filter->apply($this->_request);
        }
        foreach ($filters as $filter) {
            if (!($filter instanceof \Forix\Shopby\Model\Layer\Filter\Category)) {
                continue;
            }
            /** @var  $filter \Magento\Catalog\Model\Layer\Filter\Category */
            $categories = $this->getCategoryCollection();
            $layer->getProductCollection()->addCountToCategories($categories);
            foreach ($categories as $category) {
                if ($category->getIsActive() && $category->getIsAnchor()) {
                    $finalFilters[] = [
                        'name' => $category->getName(),
                        'url' => $this->getFilterCateUrl($category->getUrl()),
                        'count' => $category->getData('product_count'),
                        'img' => $category->getIconImage()
                    ];
                }
            }
            break;
        }
        return $finalFilters;

    }

    protected function getCategoryUrl($filter)
    {
        $categoryId = $filter->getValue();
        $category = $this->categoryRepository->get($categoryId);
        return $category->getUrl();
    }

    protected function getIconImage($filter)
    {
        $categoryId = $filter->getValue();
        $category = $this->categoryRepository->get($categoryId);
        return $category->getIconImage();
    }


    protected function getFilterCateUrl($url)
    {
        $params = $this->_request->getParams();
        return $url . '?' . http_build_query(['mb_rig_model' => $params['rig_options']['option_id']]);

    }

}