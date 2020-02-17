<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Plugin\Catalog\Block\Category;

use Magento\Catalog\Block\Category\View as CategoryViewBlock;
use Magento\Catalog\Model\Category;
use Amasty\ShopbyBase\Model\Customizer\CategoryFactory as CustomizerCategoryFactory;
use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;

class View
{
    /**
     * @var CustomizerCategoryFactory
     */
    protected $_customizerCategoryFactory;

    /**
     * @var bool
     */
    protected $_categoryModified = false;

    /**
     * @param CustomizerCategoryFactory $customizerCategoryFactory
     */
    public function __construct(
        CustomizerCategoryFactory $customizerCategoryFactory
    ) {
        $this->_customizerCategoryFactory = $customizerCategoryFactory;
    }

    /**
     * @param View $subject
     * @param Category $category
     * @return Category
     */
    public function afterGetCurrentCategory(CategoryViewBlock $subject, $category)
    {
        if ($category instanceof Category && !$this->_categoryModified) {
            $this->_customizerCategoryFactory->create()
                ->prepareData($category);

            $this->_categoryModified = true;
        }
        return $category;
    }

    /**
     * @param View $subject
     * @param bool $isMixedMode
     * @return bool
     */
    public function afterIsMixedMode(CategoryViewBlock $subject, $isMixedMode)
    {
        if (!$isMixedMode) {
            $category = $subject->getCurrentCategory();
            if ($category->getData(CategoryManager::CATEGORY_FORCE_MIXED_MODE)) {
                $isMixedMode = true;
            }
        }
        return $isMixedMode;
    }
}
