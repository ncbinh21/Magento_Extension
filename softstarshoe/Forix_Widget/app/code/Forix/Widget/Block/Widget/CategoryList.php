<?php
/**
 * Copyright ©. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Forix\Widget\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;

class CategoryList extends Template implements BlockInterface
{
    /**
     * @var Save Collection
     */
    protected $categoryCollection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $_template = 'widget/categorylist.phtml';

    /**
     * CategoryList constructor.
     * @param Template\Context $context
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->categoryRepository = $categoryRepository;
        parent::__construct($context, $data);
    }

    public function getListCategory()
    {
        $collection = $this->collectionFactory->create();
        $this->categoryCollection = $collection;
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('level', ['eq' => '2']);
        return $collection;
    }

    public function getListChildCategory($idParent)
    {
        $collection = clone($this->categoryCollection);
//        die(var_dump($collectionSave->getData()));
        $collection->addFieldToFilter('is_active', 1)
            ->addFieldToFilter('parent_id', ['eq' => $idParent]);

        return $collection;
    }

    public function getInfoCategory($idCategory)
    {
        return $this->categoryRepository->get($idCategory, $this->_storeManager->getStore()->getId());
    }
}