<?php namespace Forix\Download\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Catalog\Block\Product\Context as productContext;


class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    protected $_scopeConfig;
    protected $_storeManager;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
		productContext $productContext

    ) {
		$this->_storeManager  = $productContext->getStoreManager();
        $this->_scopeConfig   = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * get config value
     * @param $value
     * @return string
     */
    public function getConfigValue($value) {
        if ($value!="") {
            return $this->_scopeConfig->getValue(
                $value,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }

	public function getMediaUrl() {
		$currentStore = $this->_storeManager->getStore();
		return $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
	}

}