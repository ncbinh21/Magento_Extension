<?php

namespace Forix\Widget\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	protected $_scopeConfig;

	public function __construct(
		Context $context,
		ScopeConfigInterface $scopeConfig
	)
	{
		parent::__construct($context);
		$this->_scopeConfig = $scopeConfig;
	}

	public function getDataCurlLink($link)
	{
		if ($link!="") {
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,$link);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
			$output=curl_exec($ch);
			$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			curl_close($ch);
			if($httpcode == 200){
				return $output;
			}
		}
		return null;
	}

	public function getConfigValue($value) {
		if ($value!="") {
			return $this->_scopeConfig->getValue(
				$value,
				\Magento\Store\Model\ScopeInterface::SCOPE_STORE
			);
		}
	}

}