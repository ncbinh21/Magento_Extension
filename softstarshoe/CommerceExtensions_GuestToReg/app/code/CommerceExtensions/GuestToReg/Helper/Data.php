<?php
/**
 * Copyright © 2016 CommerceExtensions . All rights reserved.
 */
namespace CommerceExtensions\GuestToReg\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
     * @param \Magento\Framework\App\Helper\Context $context
     */
	public function __construct(
		\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context); 
	}
	
    public function getConfig($key){
		$path = 'guesttoreg/guesttoreg/' . $key;
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
	
    public function moduleActive(){
        return $this->getConfig('disable_ext');
    }
}