<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin;

use Magento\Catalog\Model\CategoryRepository;

class CategoryRepositoryPlugin
{
    /**
     * Categories filter multiselect
     *
     * @param CategoryRepository $subject
     * @param $categoryId
     * @param null $storeId
     * @return array
     */
    public function beforeGet(CategoryRepository $subject, $categoryId, $storeId = null)
    {
        !is_array($categoryId) ?: $categoryId = array_shift($categoryId);
        return [$categoryId, $storeId];
    }
}
