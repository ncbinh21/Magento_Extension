<?php

namespace Forix\Custom\Block;

use Magento\Framework\View\Element\Template;

class Category extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Save Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categoryCollectionFactory;

    /**
     * @var \Magento\Catalog\Helper\Category
     */
    protected $categoryHelper;

    /**
     * Category constructor.
     * @param \Magento\Catalog\Helper\Category $categoryHelper
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        Template\Context $context,
        array $data = [])
    {
        $this->categoryHelper = $categoryHelper;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param bool $isActive
     * @param bool $sortBy
     * @return \Magento\Catalog\Model\ResourceModel\Category\Collection
     */
    public function getCategoryCollection($isActive = true, $sortBy = false)
    {
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addLevelFilter('3');
        // select only active categories
        if ($isActive) {
            $collection->addIsActiveFilter();
        }
        // sort categories by some value
        if ($sortBy) {
            $collection->addOrderField($sortBy);
        }
        $this->categoryCollection = $collection;
        return $collection;
    }

    /**
     * @return mixed
     */
    public function getCategoryFirst()
    {
        $collectionSave = clone($this->categoryCollection);
        $first = $collectionSave->addFieldToFilter('level', ['eq' => '2']);
        return $first->getFirstItem();
    }

    /**
     * @return mixed
     */
    public function getCategorySecond()
    {
        $collectionSave = clone($this->categoryCollection);
        $second = $collectionSave->addFieldToFilter('level', ['eq' => '2']);
        $remove = $second->getFirstItem();
        $second->removeItemByKey($remove->getId());
        return $second->getFirstItem();
    }

    /**
     * @param $parent
     * @return mixed
     */
    public function getCategoryChild($parent)
    {
        $collectionSave = clone($this->categoryCollection);
        $child = $collectionSave->addFieldToFilter('parent_id', ['eq' => $parent]);
        return $child;
    }
}