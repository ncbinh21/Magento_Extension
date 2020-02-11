<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
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

        $loader = '<img src="' . $subject->getViewFileUrl('images/loader-1.gif') . '"
                 alt="' . __('Loading...') . '" style="top: 100px;left: 45%;display: block;position: absolute;">';

        $topOffset = $this->hasTopFilters ? '140px' : '0px';
        $style  = '
        style="
            background-color: #FFFFFF;
            height: calc(100% - ' . $topOffset .');
            left: 0;
            opacity: 0.5;
            filter: alpha(opacity = 50);
            position: absolute;
            top: ' . $topOffset . ';
            width: 100%;
            z-index: 555;
            display:none;
        "
        ';
        return
            '<div id="amasty-shopby-product-list" style="position:relative">'
            . $result
            . '<div id="amasty-shopby-overlay" '.$style.'>' . $loader . '</div></div>';
    }
}
