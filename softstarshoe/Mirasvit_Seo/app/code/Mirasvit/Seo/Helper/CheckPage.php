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



namespace Mirasvit\Seo\Helper;

class CheckPage extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Catalog\Model\Layer\Resolver
     */
    protected $layerResolver;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @param \Magento\Catalog\Model\Layer\Resolver        $layerResolver
     * @param \Magento\Framework\Registry                  $registry
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Registry $registry
    ) {
        $this->layerResolver = $layerResolver;
        $this->registry = $registry;
    }

    /**
     * @return bool|int
     */
    public function isFilterPage()
    {
        $isCategory = $this->registry->registry('current_category') || $this->registry->registry('category');
        $isFilter = false;

        if ($isCategory) {
            $filters = $this->layerResolver->get()->getState()->getFilters();
            $isFilter = count($filters) > 0;
        }

        return $isFilter;
    }
}
