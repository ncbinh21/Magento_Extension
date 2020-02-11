<?php

/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft StartShoes
 * Date: 1/27/18
 * Time: 11:25 AM
 */

namespace Forix\ProductListTemplate\Block\Category;

use \Magento\Eav\Model\Entity\Collection\AbstractCollection;
use \Magento\Eav\Model\Entity\Attribute\Source\Boolean as SourceBoolean;


/**
 * Class Tabs
 * Lấy những category con của category hiện tại ra,
 * sau đó sắp xếp theo thứ tự position.
 *
 * @package Forix\ProductListTemplate\Block\Category
 */
class Tabs extends \Magento\Catalog\Block\Category\View
{
    protected $_mainCategory;
    
    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Model\Layer\Resolver $layerResolver, \Magento\Framework\Registry $registry, \Magento\Catalog\Helper\Category $categoryHelper, array $data = [])
    {
        parent::__construct($context, $layerResolver, $registry, $categoryHelper, $data);
    }

    protected function _initMainCategory()
    {
        if (!$this->_mainCategory) {
            $currentCategory = $this->getCurrentCategory();
            $_currentCategory = clone($currentCategory);
            if($parent_tab_id = $currentCategory->getData('sss_parent_tab_id')){
                $_currentCategory->load($parent_tab_id, 'entity_id');
            }else if (3 == $currentCategory->getLevel()) {
                $_currentCategory = $_currentCategory->getParentCategory();
            }
            $this->_mainCategory = $_currentCategory;
        }
        return $this->_mainCategory;
    }

    public function getMainCategory()
    {
        return $this->_initMainCategory();
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isHasActiveAllProductTab()
    {
        $children = $this->getChildCategories();
        if (count($children)) {
            foreach ($children as $child) {
                if ($this->isCurrentCategory($child)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @return bool
     */
    public function isCurrentCategory(\Magento\Catalog\Model\Category $category)
    {
        if ($this->getCurrentCategory()) {
            if ($category) {
                return $this->getCurrentCategory()->getId() == $category->getId();
            }
        }
        return false;
    }

    /**
     * get all child category of current category, sort by position
     *
     * @return \Magento\Catalog\Model\Category[]|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getChildCategories()
    {
        if (!$this->hasData('category_tabs')) {
            $currentCategory = $this->getMainCategory();
            $results = [];
            if ($currentCategory) {
                $tabIds = $currentCategory->getData('sss_category_tab_ids');
                if (!is_array($tabIds)) {
                    $tabIds = explode(',', $tabIds);
                }
                /**
                 * Enable Catalog Category Flat ... 
                 * @var $tabCollection \Magento\Catalog\Model\ResourceModel\Category\Flat\Collection
                 */
                if ($currentCategory->getUseFlatResource()) {
                    $tabCollection = $currentCategory->getCollection();

                    $tabCollection
                        ->addIsActiveFilter()
                        ->addAttributeToSelect('include_in_menu')
                        ->addAttributeToSelect('sss_custom_tab_title')
                        ->addAttributeToSelect('position')
                        ->addAttributeToSelect('sss_tab_position')
                        ->addAttributeToSelect('name')
                        ->addAttributeToFilter('sss_visible_in_tab', SourceBoolean::VALUE_YES)
                        ->addFieldToFilter('parent_id', ['eq' => $currentCategory->getId()]);

                    $tabCollection->getSelect()
                        ->orWhere('main_table.entity_id in (?)', $tabIds, \Magento\Framework\DB\Select::TYPE_CONDITION);

                    $results = $tabCollection->getItems();
                } else {
                    $tabCollection = $currentCategory->getChildrenCategories();
                    if ($tabCollection instanceof AbstractCollection) {
                        $tabCollection->setLoadProductCount(false);
                        $tabCollection->addAttributeToSelect('sss_custom_tab_title');
                        $tabCollection->addLevelFilter(3);
                        try {
                            $tabCollection->addAttributeToFilter('sss_visible_in_tab', SourceBoolean::VALUE_YES);
                        } catch (\Exception $e) {
                        }
                        $results = $tabCollection->getItems();
                    } else {
                        foreach ($tabCollection as $childCategory) {
                            if (3 == $childCategory->getLevel()) {
                                $results[] = $childCategory;
                            }
                        }
                    }
                }
                usort($results, function ($first, $second) {
                    if ($first->getData('sss_tab_position') || $second->getData('sss_tab_position')) {
                        return $first->getData('sss_tab_position') >= $second->getData('sss_tab_position');
                    } else {
                        return $first->getData('position') >= $second->getData('position');
                    }
                });
            }
            $this->setData('category_tabs', $results);
        }
        return $this->getData('category_tabs');
    }
}