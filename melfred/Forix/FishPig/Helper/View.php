<?php

namespace Forix\FishPig\Helper;
use Magento\Framework\Registry;

class View extends \FishPig\WordPress\Helper\View
{
	protected $registry;

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\FishPig\WordPress\Model\Config $config,
		\Magento\Framework\View\Layout $layout,
		\Magento\Framework\App\Request\Http $request,
		Registry $registry
	)
	{
		parent::__construct($context,$config,$layout,$request);
		$this->registry = $registry;
	}

	public function applyPageConfigData($pageConfig, $entity)
	{
		if (!$pageConfig || !$entity) {
			return $this;
		}

		$term = $this->registry->registry("wordpress_term");
		$pageConfig->getTitle()->set($entity->getPageTitle());
		$pageConfig->setDescription($entity->getMetaDescription());
		$pageConfig->setKeywords($entity->getMetaKeywords());

		#TODO: Hook this up so it displays on page
		$pageConfig->setRobots($entity->getRobots());

		$pageMainTitle = $this->_layout->getBlock('page.main.title');


		if ($pageMainTitle) {
			if ($term!=null) {
				$pageMainTitle->setPageTitle($term->getData("name"));
			} else {
				$pageMainTitle->setPageTitle("Tooling Blog");
			}
		}

		if ($entity->getCanonicalUrl()) {
            if($this->_request->getFullActionName() == 'wordpress_post_view') {
                $url = $entity->getCanonicalUrl();
                $url = $this->getUrlExternalBlogDetail($url);
                $pageConfig->addRemotePageAsset($url, 'canonical', ['attributes' => ['rel' => 'canonical']]);
            }
		}
		return $this;
	}


	public function getConfigValue($value)
	{
		return $this->scopeConfig->getValue($value);
	}


	public function getUrlExternalBlogDetail($url) {
		$linkRss = $this->getConfigValue("blog/general/link");
		$link    = str_replace("feed","",$linkRss);
		$link    = rtrim($link,"/");
		$need    =  strpos($url,"/blog")+6;
		$string  = substr($url,$need);
		return "$link/".$string;
	}

	public function getUrlInternalBlogDetail($url) {
		$arr = explode("/", $url);
		array_shift($arr);array_shift($arr);array_shift($arr);
		return "/blog/".implode("/", $arr);
	}



}