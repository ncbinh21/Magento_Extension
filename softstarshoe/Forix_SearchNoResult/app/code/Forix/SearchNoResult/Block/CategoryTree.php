<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/16/2016
 * Time: 10:57 AM
 */

namespace Forix\SearchNoResult\Block;


class CategoryTree extends \Magento\Framework\View\Element\Template
{

    private $helper;

    /**
     * SearchLayoutLoadBeforeObserver constructor.
   * @param  \Forix\SearchNoResult\Helper\Data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Forix\SearchNoResult\Helper\Data $searchNoResultHelper,
        array $data = []

    ) {
        $this->helper = $searchNoResultHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get top menu html
     *
     * @param string $outermostClass
     * @param string $childrenWrapClass
     * @param int $limit
     * @return string
     */
    public function getCategories($outermostClass = '', $childrenWrapClass = '', $limit = 100)
    {
        /** @var \Magento\Catalog\Api\CategoryManagementInterface $obj */
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $rootId = 1;
        $categoryRoot = $objectManager->create('Magento\Catalog\Model\Category')->load($rootId);
        foreach($categoryRoot->getChildrenCategories() as $item) {
            return $this->_getHtml($item, 1);
        }
    }
    protected function _getHtml($item, $level){
        if(!$this->helper->getConfigValue('show_category')){
            return '';
        }
        $html = "";
        $childs = $this->getChildrenCategories($item);

        if($childs) {
            $html = '<ul class="category-level-' . $level . '">';
            foreach ($childs as $sub) {
                if($sub->getData('is_active') && $sub->getData('include_in_menu')) {
                    $html .= '<li><a href="' . $sub->getUrl() . '">' . $sub->getName() . '</a>' . $this->_getHtml($sub, $level + 1) . '</li>';
                }
            }
            $html .= '</ul>';
        }
        return $html;
    }
    /**
     * Get children categories for particular category
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getChildrenCategories($category)
    {

        $collection = $category->getCollection();
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Category\Collection */
        $collection->addAttributeToSelect('url_key')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('all_children')
            ->addAttributeToFilter('is_active', 1)
            ->addAttributeToSelect('include_in_menu')
            ->addFieldToFilter('parent_id', $category->getId())
            ->setOrder('position', 'asc')
            ->load();

        return $collection;
    }


}