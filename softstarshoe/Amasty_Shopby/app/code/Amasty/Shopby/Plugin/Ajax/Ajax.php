<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Ajax;

use Amasty\Shopby\Helper\State;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Layout\Element;

class Ajax
{
    /**
     * @var \Amasty\Shopby\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var State
     */
    protected $stateHelper;

    public function __construct(
        \Amasty\Shopby\Helper\Data $helper,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        State $stateHelper
    ) {
        $this->helper = $helper;
        $this->resultRawFactory = $resultRawFactory;
        $this->stateHelper = $stateHelper;
    }

    protected function isAjax(RequestInterface $request)
    {
        if (!$request instanceof Http) {
            return false;
        }
        $isAjax = $request->isXmlHttpRequest() && $request->isAjax();
        $isScroll = $request->getParam('is_scroll');
        return $this->helper->isAjaxEnabled() && $isAjax && !$isScroll;
    }

    /**
     * @param \Magento\Framework\View\Result\Page $page
     *
     * @return array
     */
    protected function getAjaxResponseData(\Magento\Framework\View\Result\Page $page)
    {
        $layout = $page->getLayout();

        $products = $layout->getBlock('category.products');
        if (!$products) {
            $products = $layout->getBlock('search.result');
        }

        $navigation = $layout->getBlock('catalog.leftnav');
        if (!$navigation) {
            $navigation = $layout->getBlock('catalogsearch.leftnav');
        }
        $applyButton = $layout->getBlock('amasty.shopby.applybutton.sidebar');

        $navigationTop = $layout->getBlock('amshopby.catalog.topnav');
        $applyButtonTop = $layout->getBlock('amasty.shopby.applybutton.topnav');
        $h1 = $layout->getBlock('page.main.title');
        $title = $page->getConfig()->getTitle();
        $breadcrumbs = $layout->getBlock('breadcrumbs');

        $htmlCategoryData = '';
        $children = $layout->getChildNames('category.view.container');
        foreach ($children as $child) {
            $htmlCategoryData .= $layout->renderElement($child);
        }
        $htmlCategoryData = '<div class="category-view">' . $htmlCategoryData . '</div>';

        $shopbyCollapse = $layout->getBlock('catalog.navigation.collapsing');
        $shopbyCollapseHtml = '';
        if ($shopbyCollapse) {
            $shopbyCollapseHtml = $shopbyCollapse->toHtml();
        }

        $swatchesChoose = $layout->getBlock('catalog.navigation.swatches.choose');
        $swatchesChooseHtml = '';
        if ($swatchesChoose) {
            $swatchesChooseHtml = $swatchesChoose->toHtml();
        }

        if ($navigation) {
            $navigation->toHtml();
        }

        $responseData = [
            'categoryProducts'=> ($products ? $products->toHtml() : '') . $swatchesChooseHtml,
            'navigation' =>
                ($navigation ? $navigation->toHtml() : '')
                . $shopbyCollapseHtml
                . ($applyButton ? $applyButton->toHtml() : ''),
            'navigationTop' =>
                ($navigationTop ? $navigationTop->toHtml() : '')
                . ($applyButtonTop ? $applyButtonTop->toHtml() : ''),
            'breadcrumbs' => $breadcrumbs ? $breadcrumbs->toHtml() : '',
            'h1' => $h1 ? $h1->toHtml() : '',
            'title' => $title->get(),
            'categoryData' => $htmlCategoryData,
            'url' => $this->stateHelper->getCurrentUrl(),
        ];
        try {
            $sidebarTag = $layout->getElementProperty('div.sidebar.additional', Element::CONTAINER_OPT_HTML_TAG);
            $sidebarClass = $layout->getElementProperty('div.sidebar.additional', Element::CONTAINER_OPT_HTML_CLASS);
            $sidebarAdditional = $layout->renderNonCachedElement('div.sidebar.additional');
            $responseData['sidebar_additional'] = $sidebarAdditional;
            $responseData['sidebar_additional_alias'] = $sidebarTag . '.' . str_replace(' ', '.', $sidebarClass);
        } catch (\Exception $e) {
            //container doesn't exist
        }

        $responseData = $this->removeAjaxParam($responseData);
        $responseData = $this->removeEncodedAjaxParams($responseData);

        return $responseData;
    }

    protected function removeEncodedAjaxParams($responseData)
    {
        $pattern = '@aHR0c(Dov|HM6)[A-Za-z0-9_-]+@u';
        array_walk($responseData, function (&$html) use ($pattern) {
            // 'aHR0cDov' and 'aHR0cHM6' are the beginning of the Base64 code for 'http:/' and 'https:'
            $res = preg_replace_callback($pattern, [$this, 'removeAjaxParamFromEncodedMatch'], $html);
            if ($res !== null) {
                $html = $res;
            }
        });
        return $responseData;
    }

    /**
     * @param array $match
     * @return string
     */
    protected function removeAjaxParamFromEncodedMatch($match)
    {
        $spec64 = '+/=';
        $specUrl = '-_,';

        $originalUrl = base64_decode(strtr($match[0], $specUrl, $spec64));
        if ($originalUrl === false) {
            return $match[0];
        }
        $url = $this->removeAjaxParam($originalUrl);
        return ($originalUrl == $url) ? $match[0] : rtrim(strtr(base64_encode($url), $spec64, $specUrl), ',');
    }

    protected function removeAjaxParam($data)
    {
        $data = str_replace([
            '?shopbyAjax=1&amp;',
            '?shopbyAjax=1&',
        ], '?', $data);
        $data = str_replace([
            '?shopbyAjax=1',
            '&amp;shopbyAjax=1',
            '&shopbyAjax=1',
        ], '', $data);
        return $data;
    }

    /**
     * @param array $data
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    protected function prepareResponse(array $data)
    {
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($data));
        
        return $response;
    }
}
