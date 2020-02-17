<?php

namespace Forix\CategoryCustom\Block;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class GroundCategory extends Template implements BlockInterface
{
    /**
     * @var \Forix\CategoryCustom\Model\ResourceModel\Product\Collection
     */
    protected $_productCollection;

    protected $categoryFactory;
    protected $_coreRegistry = null;
    protected $attOptions;
    protected $_optionCollection;
    protected $_eavAttribute;
    protected $advancedImage;
    protected $_productCollectionFactory;
    protected $categoryCollectionFactory;
    protected $_productCollections;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Forix\AdvancedAttribute\Model\ResourceModel\Option\CollectionFactory $optionCollection,
        \Forix\CategoryCustom\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute $eavAttribute,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Forix\Product\Model\Source\GroundCondition $attOptions,
        \Forix\AdvancedAttribute\Model\Image $image,


        array $data = []
    )
    {
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_optionCollection = $optionCollection;
        $this->categoryFactory = $categoryFactory;
        $this->_eavAttribute = $eavAttribute;
        $this->_coreRegistry = $registry;
        $this->attOptions = $attOptions;
        $this->advancedImage = $image;
        parent::__construct($context, $data);
    }

    public function getRootUrl()
    {
        return $this->advancedImage->getBaseUrl();
    }

    /**
     * @return \Magento\Catalog\Model\Category
     */
    public function getCurrentCategory()
    {
        if (!$this->hasData('current_category')) {
            $this->setData('current_category', $this->_coreRegistry->registry('current_category'));
        }
        return $this->getData('current_category');
    }

    public function getCategory($categoryId = null)
    {
        if (!$categoryId) {
            $categoryId = $this->getCurrentCategory()->getId();
        }
        $category = $this->categoryFactory->create()->load($categoryId);
        return $category;
    }

    /**
     * @param $category
     * @return \Forix\CategoryCustom\Model\ResourceModel\Product\Collection
     */
    public function getProductsCollection($category)
    {
        if (!isset($this->_productCollections[$category->getId()])) {
            $this->_productCollections[$category->getId()] = $this->_productCollectionFactory->create()->addCategoryFilter($category)->addAttributeToSelect('*')->addGroundConditionToSelect();
        }
        return $this->_productCollections[$category->getId()];
    }

    public function getGroundOption()
    {
        return $this->attOptions->getAllOptions(false);
    }

    public function getAllOptionImages($attributeCode)
    {
        $attributeId = $this->_eavAttribute->getIdByCode('catalog_product', $attributeCode);
        $collection = $this->_optionCollection->create()->addFieldToFilter('attribute_id', ['eq' => $attributeId]);
        $dataArray = $collection->getData();
        $resData = array();
        foreach ($dataArray as $item) {
            $resData[$item['option_id']] = $item["icon_normal"];
        }
        return $resData;
    }

    public function getCurrentGroundInfo()
    {
        if (isset($this->getRequest()->getParam('amshopby')['mb_ground_condition'])) {
            $groundId = $this->getRequest()->getParam('amshopby')['mb_ground_condition'];
        } else {
            $groundId = $this->getRequest()->getParam('mb_ground_condition');
        }
        if ($groundId) {
            $data = $this->_optionCollection->create()->addOptionToFilter($groundId);
            if ($data) {
                return $data->getData()[0];
            }
        }
        return false;
    }


    public function getDescendants($category, $levels = 2)
    {
        if ((int)$levels < 1) {
            $levels = 1;
        }
        $collection = $this->categoryCollectionFactory->create()->addFieldToSelect('*')
            ->addPathsFilter($category->getPath() . '/')
            ->addLevelFilter($category->getLevel() + $levels);
        return $collection->getItems();
    }

    public function readImageContent($path)
    {
        $filename = $this->advancedImage->getBaseDir() . $path;
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
        return '';
    }

}
