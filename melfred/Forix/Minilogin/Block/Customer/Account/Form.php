<?php
namespace Forix\Minilogin\Block\Customer\Account;

use Magento\Framework\View\Element\Template;

class Form extends \Magento\Customer\Block\Account\AuthenticationPopup
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
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $_serializer;

    protected $_helper;

    protected $_customerOrderData;
    /**
     * Form constructor.
     * @param Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Forix\Base\Helper\Data $helper,
        \Forix\CustomerOrder\Helper\Data $customerOrderData,
        \Magento\Framework\Serialize\Serializer\Json $serializer,
        array $data = [])
    {
        $this->_customerUrl = $customerUrl;
        $this->_postDataHelper = $postDataHelper;
        $this->httpContext = $httpContext;
        $this->_serializer = $serializer;
        $this->_helper = $helper;
        $this->_customerOrderData = $customerOrderData;
        parent::__construct($context, $data, $serializer);
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

    public function getJsLayout()
    {
        $jsLayout = $this->jsLayout;
        $jsLayout['components']['mini_login_content']['config']['isB2BEnabled'] = $this->_helper->isB2BEnabled();
        $jsLayout['components']['mini_login_content']['config']['logoutDataPost'] = $this->getPostParams();
        return $this->_serializer->serialize($jsLayout);
    }

    public function isB2BEnabled(){
        return $this->_helper->isB2BEnabled();
    }

    public function isDistributorCompany(){
        return  $this->_customerOrderData->isDistributorManager();
    }

    /**
     * @return \Magento\Customer\Model\Url
     */
    public function getUrlCustomer(){
        return $this->_customerUrl;
    }

    /**
     * Get customer register url
     *
     * @return string
     */
    public function getCustomerRegisterUrlUrl()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * Get customer forgot password url
     *
     * @return string
     */
    public function getCustomerForgotPasswordUrl()
    {
        return $this->_customerUrl->getForgotPasswordUrl();
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