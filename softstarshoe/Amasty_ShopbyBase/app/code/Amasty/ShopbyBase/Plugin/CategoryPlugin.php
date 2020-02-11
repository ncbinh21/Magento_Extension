<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_ShopbyBase
 */


namespace Amasty\ShopbyBase\Plugin;

use Magento\Catalog\Block\Category\View;
use Magento\Catalog\Model\Category;
use Amasty\ShopbyBase\Model\Category\Manager as CategoryManager;

class CategoryPlugin
{
    /**
     * @param Category $subject
     * @param string|null $result
     * @return string|null
     */
    public function afterGetImageUrl(Category $subject, $result)
    {
        if ($subject->hasData(CategoryManager::CATEGORY_SHOPBY_IMAGE_URL)) {
            return $subject->getData(CategoryManager::CATEGORY_SHOPBY_IMAGE_URL);
        } else {
            return $result;
        }
    }
}
