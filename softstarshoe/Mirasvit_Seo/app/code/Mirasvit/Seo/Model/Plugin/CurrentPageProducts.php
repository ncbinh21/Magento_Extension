<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   2.0.24
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Model\Plugin;

class CurrentPageProducts
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\Registry               $registry
    ) {
        $this->registry = $registry;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct                     $subject
     * @param \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $collection
     *
     * @return \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetLoadedProductCollection($subject, $collection)
    {
        $this->registry->register('category_product_for_snippets', $collection, true);

        return $collection;
    }
}
