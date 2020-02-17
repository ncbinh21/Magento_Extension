<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Shopby
 */


namespace Amasty\Shopby\Plugin\Ajax;

use Amasty\Shopby\Helper\State;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\Request\Http;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject\IdentityInterface;
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

        $isShopbyAjax = $request->getParam('shopbyAjax');
        return $this->helper->isAjaxEnabled() && $isAjax && $isShopbyAjax && !$isScroll;

    }

    /**
     * @param \Magento\Framework\View\Result\Page $page
     *
     * @return array
     */
    protected function getAjaxResponseData(\Magento\Framework\View\Result\Page $page)
    {
        $layout = $page->getLayout();
        $tags = [];

        $products = $layout->getBlock('category.products');
        if (!$products) {
            $products = $layout->getBlock('search.result');
        }

        $tags = $this->addXTagCache($products, $tags);

        $productList = $products->getChildBlock('product_list');
        $productsCount = $productList
            ? $productList->getLoadedProductCollection()->getSize()
            : $products->getResultCount();

        $navigation = $layout->getBlock('catalog.leftnav') ?: $layout->getBlock('catalogsearch.leftnav');
        if ($navigation) {
            $navigation->toHtml();
            $tags = $this->addXTagCache($navigation, $tags);
        }

        $applyButton = $layout->getBlock('amasty.shopby.applybutton.sidebar');
        $tags = $this->addXTagCache($applyButton, $tags);

        $jsInit = $layout->getBlock('amasty.shopby.jsinit');
        $tags = $this->addXTagCache($jsInit, $tags);

        $navigationTop = $layout->getBlock('amshopby.catalog.topnav');
        $tags = $this->addXTagCache($navigationTop, $tags);

        $applyButtonTop = $layout->getBlock('amasty.shopby.applybutton.topnav');
        $tags = $this->addXTagCache($applyButtonTop, $tags);

        $h1 = $layout->getBlock('page.main.title');
        $tags = $this->addXTagCache($h1, $tags);

        $title = $page->getConfig()->getTitle();
        $breadcrumbs = $layout->getBlock('breadcrumbs');
        $tags = $this->addXTagCache($breadcrumbs, $tags);

        $htmlCategoryData = '';
        $children = $layout->getChildNames('category.view.container');
        foreach ($children as $child) {
            $htmlCategoryData .= $layout->renderElement($child);
            $tags = $this->addXTagCache($child, $tags);
        }

        $groundClass = '';
        if (in_array('category_ground_condition_template', $layout->getUpdate()->getHandles())) {
            $groundClass = 'ground-filter';
        }
        $htmlCategoryData = '<div class="category-view '. $groundClass .'">' . $htmlCategoryData . '</div>';


        $shopbyCollapse = $layout->getBlock('catalog.navigation.collapsing');
        $shopbyCollapseHtml = '';
        if ($shopbyCollapse) {
            $shopbyCollapseHtml = $shopbyCollapse->toHtml();
            $tags = $this->addXTagCache($shopbyCollapse, $tags);
        }

        $swatchesChoose = $layout->getBlock('catalog.navigation.swatches.choose');
        $swatchesChooseHtml = '';
        if ($swatchesChoose) {
            $swatchesChooseHtml = $swatchesChoose->toHtml();
        }

        $isDisplayModePage = $productList && $productList->getLayer()
            && $productList->getLayer()->getCurrentCategory()->getDisplayMode() == Category::DM_PAGE;

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
            'tags' => implode(',', array_unique($tags + [\Magento\PageCache\Model\Cache\Type::CACHE_TAG])),
            'productsCount' => $productsCount,
            'js_init' => $jsInit->toHtml(),
            'isDisplayModePage' => $isDisplayModePage
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

    /**
     * @param mixed $element
     * @param array $tags
     * @return array
     */
    private function addXTagCache($element, array $tags)
    {
        if ($element instanceof IdentityInterface) {
            $tags = array_merge($tags, $element->getIdentities());
        }

        return $tags;
    }

    /**
     * @param array $responseData
     * @return array
     */
    private function removeEncodedAjaxParams(array $responseData)
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
        if (isset($data['tags'])) {
            $response->setHeader('X-Magento-Tags', $data['tags']);
            unset($data['tags']);
        }

        $response->setContents(json_encode($data));
        return $response;
    }
}
