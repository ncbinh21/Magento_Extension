<?php
/**
 * Created by PhpStorm.
 * User: Eric
 * Date: 3/4/2016
 * Time: 3:30 PM
 */

namespace Forix\Minilogin\Block\Customer\Account;

use Magento\Framework\View\Element\Template;

class Form extends Template
{

    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

	/**
	 * @var \Magento\Framework\Data\Helper\PostHelper
	 */
	protected $_postDataHelper;

    /**
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        \Magento\Checkout\Model\CompositeConfigProvider $configProvider,
	    \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $layoutProcessors = [],
        array $data = [])
    {
        parent::__construct($context,$data);
        $this->httpContext = $httpContext;
        if (isset($data['jsLayout'])) {
            $this->jsLayout = array_merge_recursive($jsLayoutDataProvider->getData(), $data['jsLayout']);
            unset($data['jsLayout']);
        } else {
            $this->jsLayout = $jsLayoutDataProvider->getData();
        }
        $this->configProvider = $configProvider;
        $this->layoutProcessors = $layoutProcessors;
        $this->_customerUrl = $customerUrl;
	    $this->_postDataHelper = $postDataHelper;
    }
    /**
     * @return string
     */
    public function getHref()
    {
        return $this->isLoggedIn()
            ? $this->_customerUrl->getLogoutUrl()
            : $this->_customerUrl->getLoginUrl();
    }

    public function getForgotPasswordActionUrl(){

        return $this->getUrl('customer/account/forgotpasswordpost');
    }

    /**
     * Returns popup config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'customerRegisterUrl' => $this->getCustomerRegisterUrlUrl(),
            'customerForgotPasswordUrl' => $this->getCustomerForgotPasswordUrl(),
            'baseUrl' => $this->getBaseUrl()
        ];
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            $this->jsLayout = $processor->process($this->jsLayout);
        }
        return \Zend_Json::encode($this->jsLayout);
    }
    /**
     * Return base url.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get customer register url
     *
     * @return string
     */
    public function getCustomerRegisterUrlUrl()
    {
        return $this->getUrl('customer/account/create');
    }

    /**
     * Get customer forgot password url
     *
     * @return string
     */
    public function getCustomerForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    /**
     * @return mixed
     */
    public function isFEHttpsEnable() {
        return $this->_scopeConfig->getValue(\Magento\Store\Model\Store::XML_PATH_SECURE_IN_FRONTEND);
    }

	/**
	 * @return string
	 */
	public function getPostParams() {
		return $this->_postDataHelper->getPostData($this->getHref());
	}
}