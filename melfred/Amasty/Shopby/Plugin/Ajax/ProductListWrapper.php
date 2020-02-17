<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */

namespace Amasty\Shopby\Plugin\Ajax;

class ProductListWrapper
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    /**
     * @var bool
     */
    private $hasTopFilters = false;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Amasty\Shopby\Model\Layer\FilterList $filterListTop,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver
    ) {
        $this->request = $request;
        $layer = $layerResolver->get();
        $this->hasTopFilters = (bool) $filterListTop->getFilters($layer);
    }

    public function afterToHtml(\Magento\Framework\View\Element\Template $subject, $result)
    {
        if ($subject->getNameInLayout() !== 'category.products.list'
            && $subject->getNameInLayout() !== 'search_result_list'
        ) {
            return $result;
        }

        if ($this->request->getParam('is_scroll')) {
            return $result;
        }

        return
            '<div id="amasty-shopby-product-list">'
            . $result
            . '</div>';
    }
}
