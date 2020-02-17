<?php

namespace Forix\Megamenu\Plugin\Ves\Megamenu\Model;

class Menu
{
	public function beforeSave($subject)
	{
		$objectMaganer = \Magento\Framework\App\ObjectManager::getInstance();
		$helper = $objectMaganer->create('Magento\Framework\Json\Helper\Data');
		$params = $subject->getParams();
		$baseUrl = $this->getBaseUrl();
		if ($params!="") {
			$params = $helper->jsonDecode($params);
			if (is_array($params)) {
				foreach ($params as $key=>$item) {
					if ($params[$key]['image_hover']!="") {
						$imgHover = $params[$key]['image_hover'];
						$start    = strpos($imgHover,'/media');
						$imgHover = substr($imgHover, $start);
						$params[$key]['image_hover'] = $imgHover;
					}
					// for children
					if (isset($item['children'])) {
						foreach ($item['children'] as $k=>$child) {
							if ($child['image_hover']!="") {
								$imgHoverChild = $child['image_hover'];
								$start    = strpos($imgHoverChild,'/media');
								$imgHoverChild = substr($imgHoverChild, $start);
								$params[$key]['children'][$k]['image_hover'] = $imgHoverChild;
							}
						}
					}
				}
			}
			$newsParams = $helper->jsonEncode($params);
			$subject->setParams($newsParams);
		}
		return $subject;
	}

	public function getBaseUrl()
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getBaseUrl();
	}

}