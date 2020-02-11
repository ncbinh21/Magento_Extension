<?php
/**
 * Created by PhpStorm.
 * User: dotung23@yahoo.com
 * Date: 4/20/2018
 * Time: 11:40 AM
 */

namespace Forix\MailChimp\Helper;

class Data extends \Ebizmarts\MailChimp\Helper\Data
{
	const XML_PATH_LIST_BLOG  = 'mailchimp/blog/monkeylist_blog';

	/**
	 * @param null $store
	 * @return mixed
	 */
	public function getDefaultList($store = null)
	{
		$store_name = $this->_request->getParam('store_name');
		switch ($store_name){
			case 'web':
				$xml_path = self::XML_PATH_LIST;
				break;
			case 'blog':
				$xml_path = self::XML_PATH_LIST_BLOG;
				break;
			default:
				$xml_path = self::XML_PATH_LIST;
		}

		return $this->getConfigValue($xml_path, $store);
	}
}