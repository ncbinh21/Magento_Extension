<?php

/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 10/31/2016
 * Time: 11:21 AM
 */
namespace Forix\Base\Plugin;

class AfterGetRobots
{
	/**
	 * @var \Forix\Base\Helper\Data
	 */
	protected $_helper;

	/**
	 * AfterGetRobots constructor.
	 *
	 * @param \Forix\Base\Helper\Data $helper
	 */
	public function __construct(
		\Forix\Base\Helper\Data $helper
	) {
		$this->_helper = $helper;
	}

	/**
	 * @param $source
	 * @param $result
	 *
	 * @return string
	 */
	public function afterGetRobots($source, $result)
	{
		$content = 'NOINDEX,FOLLOW';
		$customRobotPages = $this->_helper->getConfigValue('fcatalog/setting/custom_robots');
		$customRobotPages = explode(PHP_EOL, $customRobotPages);
		$baseUrl = rtrim($this->_helper->getBaseUrl(), '/');
		$fullActionName = str_replace($baseUrl, "", $this->_helper->getCurrentUrl());

		if(count($customRobotPages)) {
			foreach ($customRobotPages as $page) {
				$page = trim(preg_replace('/\s\s+/', '', $page));
				if (strpos($page, '*') !== false) { //or contains a string
					$page = str_replace("*", "", $page);
					if (strpos($fullActionName, $page) !== false) {
						return $content;
					}
				}
				else {
					if(strpos($page, $fullActionName) !== false) {
						return $content;
					}
				}
			}
		}

		return $result;
	}
}