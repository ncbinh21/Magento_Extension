<?php

namespace Forix\CategoryCustom\Plugin;

use Amasty\Shopby\Helper\State;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\Page;

class CategoryViewAjax extends \Amasty\Shopby\Plugin\Ajax\Ajax
{

    protected $_layerResolver;
    protected $_resultRedirectFactory;
    public function __construct(
        \Amasty\Shopby\Helper\Data $helper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\RedirectFactory $resultRedirectFactory,
        State $stateHelper)
    {

        $this->_layerResolver = $layerResolver;
        $this->_resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($helper, $resultRawFactory, $stateHelper);
    }

    public function afterExecute(Action $controller, $page)
    {
        if (!$this->isAjax($controller->getRequest()) || !$page instanceof Page) {
            if(!$this->isAjax($controller->getRequest())){
                if (in_array(\Forix\CategoryCustom\Model\Category\Attribute\Source\Templates::CATEGORY_GROUND_CONDITION_TEMPLATE, $page->getLayout()->getUpdate()->getHandles()) && $page instanceof Page) {

                    $appliedItems = $this->_layerResolver->get()->getState()->getFilters();
                    if(!count($appliedItems)) {
                        return $this->_resultRedirectFactory->create()->setPath('noroute');
                    }
                }
            }
            return $page;
        }
        $responseData = $this->getAjaxResponseData($page);
        $layout = $page->getLayout();
		$topNav = $layout->getBlock('ground.categoryfilter');
		$resultTopNav = "";
		if ($topNav) {
			$resultTopNav = $topNav->toHtml();
		}

        //Todo: check in bit/reamer category
        if (in_array('category_ground_condition_template', $page->getLayout()->getUpdate()->getHandles())) {
            $responseData['categoryData'] = '';
            $responseData['url'] = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH).'?'.$_SERVER['QUERY_STRING'];
            $responseData['url'] .= "&clearUrl=".parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
            $responseData["top_nav_ajax"] = $resultTopNav;
        }
        $response = $this->prepareResponse($responseData);
        return $response;
    }
}
