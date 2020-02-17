<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Catalog\Model;

use Magento\Catalog\Model\CategoryRepository as MagentoCategoryRepository;

class CategoryRepository
{
    /**
     * Categories filter multiselect
     *
     * @param CategoryRepository $subject
     * @param $categoryId
     * @param null $storeId
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormatParameter)
     */
    public function beforeGet(MagentoCategoryRepository $subject, $categoryId, $storeId = null)
    {
        !is_array($categoryId) ?: $categoryId = array_shift($categoryId);
        return [$categoryId, $storeId];
    }
}
