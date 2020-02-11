<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 4/22/2016
 * Time: 8:55 AM
 */

namespace Forix\Base\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var Monolog|\Magento\Framework\Logger\Monolog
     */
    protected $_logger;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

	/**
	 * @var \Magento\Framework\UrlInterface
	 */
	protected $_urlinterface;

    /**
     * Data constructor.
     * @param Context $context
     * @param Monolog $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Logger\Monolog $logger,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
	    $this->_urlinterface = $context->getUrlBuilder();

        parent::__construct($context);
    }

    /**
     * Get configuration value
     * @param $value
     * @return mixed
     */
    public function getConfigValue($value) {
        return $this->scopeConfig->getValue($value);
    }

    /**
     * Check if module enabled
     * @param $module
     * @return bool
     */
    public function isModuleEnabled($module) {
        return $this->_moduleManager->isEnabled($module);
    }

    /**
     * Get base url
     * Possible types: link, direct_link, web, media, static, js
     * 
     * Example: ['_type' => 'media']
     * @param array $params
     */
    public function getBaseUrl($params = []) {
        
        return $this->_urlBuilder->getBaseUrl($params);
    }

    /**
     * Get current store id
     * @return int
     */
    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Get website id
     * @return string|int|null
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * Get Store code
     * @return string
     */
    public function getStoreCode()
    {
        return $this->_storeManager->getStore()->getCode();
    }

    /**
     * Get Store name
     * @return string
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * Get the current url of recently viewed page
     * @param bool|string $fromStore Include/Exclude from_store parameter from URL
     * @return string
     */
    public function getCurrentUrl()
    {
	    return $this->_urlinterface->getCurrentUrl();
    }

    /**
     * Check if store is active
     *
     * @return boolean
     */
    public function isStoreActive()
    {
        return $this->_storeManager->getStore()->isActive();
    }

    /**
     * Get param
     * @param $param
     * @return mixed
     */
    public function getParam($param) {
        return $this->_request->getParam($param);
    }

    /**
     * Get all params
     * @return array
     */
    public function getParams() {
        return $this->_request->getParams();
    }

    /**
     * Check if frontend URL is secure
     * @return boolean
     */
    public function isFrontUrlSecure() {
        return $this->_storeManager->getStore()->isFrontUrlSecure();
    }

    /**
     * Check if current requested URL is secure
     * @return boolean
     */
    public function isCurrentlySecure() {
        return $this->_storeManager->getStore()->isCurrentlySecure();
    }

    //Get Controller, Module, Action & Route Name
    public function getControllerModule() {
        return $this->_request->getControllerModule();
    }

    public function getFullActionName() {
        return $this->_request->getFullActionName();
    }

    public function getRouteName() {
        return $this->_request->getRouteName();
    }

    public function getActionName() {
        return $this->_request->getActionName();
    }

    public function getControllerName() {
        return $this->_request->getControllerName();
    }

    public function getModuleName() {
        return $this->_request->getModuleName();
    }

    /**
     * log
     * @param $message
     */
    public function log($message) {
        $this->_logger->log(Monolog::INFO, $message);
    }

    public function debug($message) {
        $this->_logger->debug(Monolog::DEBUG, $message);
    }

    public function critical($message) {
        $this->_logger->critical(Monolog::CRITICAL, $message);
    }

}