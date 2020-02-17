<?php
namespace Forix\SearchNoResult\Block\Widget;

use Magento\Catalog\Helper\Category;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\Tree\NodeFactory;
use Magento\Framework\Data\TreeFactory;
use Magento\Framework\View\Element\Template;
use Magento\Framework\Data\Collection;
use Magento\Framework\Data\Tree\Node;
use Magento\Widget\Block\BlockInterface;

class CategoryList extends Template implements BlockInterface
{
    protected $catalogCategory;
    private $collectionFactory;
    private $storeManager;
    private $layerResolver;
    protected $_currentInfo;
    protected $_menu;

    public function __construct(
        Category $catalogCategory,
        CollectionFactory $categoryCollectionFactory,
        Resolver $layerResolver,
        NodeFactory $nodeFactory,
        TreeFactory $treeFactory,
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->catalogCategory = $catalogCategory;
        $this->collectionFactory = $categoryCollectionFactory;
        $this->storeManager = $context->getStoreManager();
        $this->layerResolver = $layerResolver;
        $this->_menu = $nodeFactory->create(
            [
                'data' => [],
                'idField' => 'root',
                'tree' => $treeFactory->create()
            ]
        );
        $this->getRootCategories();
    }

    public function getRootCategories()
    {
        if($id = $this->getIdPath()){
            $id = str_replace('category/','',$id);
            if(is_numeric($id))
                return $this->getSubCategories($id);
        }
        return $this->getSubCategories();
    }

    public function getSubCategories($rootId)
    {
        if(!$rootId)
            $rootId = $this->storeManager->getStore()->getRootCategoryId();
        $storeId = $this->storeManager->getStore()->getId();
        $collection = $this->getCategoryTree($storeId, $rootId);
        $currentCategory = $this->getCurrentCategory();
        $mapping = [$rootId => $this->_menu];  // use nodes stack to avoid recursion
        $first = true;
        foreach ($collection as $category) {
            if (!isset($mapping[$category->getParentId()])) {
                if($first){
                    $this->_currentInfo = $category;
                    $first = false;
                }
                continue;
            }
            /** @var Node $parentCategoryNode */
            $parentCategoryNode = $mapping[$category->getParentId()];

            $categoryNode = new Node(
                $this->getCategoryAsArray($category, $currentCategory),
                'id',
                $parentCategoryNode->getTree(),
                $parentCategoryNode
            );
            $parentCategoryNode->addChild($categoryNode);

            $mapping[$category->getId()] = $categoryNode; //add node in stack
        }
    }

    public function getCategoryUrl($category){
        return $this->catalogCategory->getCategoryUrl($category);
    }

    public function getCurrentInfo(){
        return $this->_currentInfo;
    }

    public function getMenu(){
        return $this->_menu->getChildren();
    }

    public function isRootCategory($category) {
        if($category->getId() == $this->_storeManager->getStore()->getRootCategoryId()) {
            return true;
        }
        return false;
    }

    public function renderSubMenu($categories, $level){
        $html = '<ul class="content">';
        foreach($categories as $category) {
            $html .= '<li><a href="'.$this->getCategoryUrl($category).'"
               class="menu-heading">'.$category->getName().'</a>';
            if($level < $this->getData('level')) {
                if ($subCategory = $category->getChildren()) {
                    //$level++;
                    $html .= $this->renderSubMenu($subCategory, $html, $level);
                }
            }
            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    private function getCategoryAsArray($category, $currentCategory)
    {
        return [
            'name' => $category->getName(),
            'id' => 'category-node-' . $category->getId(),
            'url' => $this->catalogCategory->getCategoryUrl($category),
            'has_active' => in_array((string)$category->getId(), explode('/', $currentCategory->getPath()), true),
            'is_active' => $category->getId() == $currentCategory->getId()
        ];
    }

    private function getCurrentCategory()
    {
        $catalogLayer = $this->layerResolver->get();

        if (!$catalogLayer) {
            return null;
        }

        return $catalogLayer->getCurrentCategory();
    }

    private function getCategoryTree($storeId, $rootId)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->setStoreId($storeId);
        $collection->addAttributeToSelect('name');
        $collection->addFieldToFilter('path', [['like' => '%/' . $rootId], ['like' => '%/' . $rootId . '/%']]);
        $collection->addAttributeToFilter('include_in_menu', 1);
        $collection->addIsActiveFilter();
        $collection->addUrlRewriteToResult();
        $collection->addOrder('level', Collection::SORT_ORDER_ASC);
        $collection->addOrder('position', Collection::SORT_ORDER_ASC);
        $collection->addOrder('parent_id', Collection::SORT_ORDER_ASC);
        $collection->addOrder('entity_id', Collection::SORT_ORDER_ASC);

        return $collection;
    }
}