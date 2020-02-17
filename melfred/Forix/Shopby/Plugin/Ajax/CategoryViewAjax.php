<?php

namespace Forix\Shopby\Plugin\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\Page;

class CategoryViewAjax extends \Amasty\Shopby\Plugin\Ajax\CategoryViewAjax
{
	public function afterExecute(Action $controller, $page)
	{
		if (!$this->isAjax($controller->getRequest()) || !$page instanceof Page) {
			return $page;
		}

		$layoutFilter = $page->getLayout()->getBlock("catalogsearch.categoryfilter");
		$responseData = $this->getAjaxResponseData($page);
		$topNav = "";
		if ($layoutFilter) {
			$topNav = $layoutFilter->toHtml();
		}
		$responseData["top_nav_ajax"] = $topNav;
		$response = $this->prepareResponse($responseData);
		return $response;
	}
}