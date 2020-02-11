<?php
/**
 * Created by: Bruce
 * Date: 2/2/16
 * Time: 16:55
 */

namespace Forix\SearchNoResult\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

	/**
	 * @return bool
	 */
	public function isEnabled() {
		return (bool)$this->scopeConfig->getValue("search_no_result/setting/is_active", \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @param $value
	 * @return mixed
	 */
    public function getConfigValue($value) {
		return $this->scopeConfig->getValue('search_no_result/setting/'.$value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

}