<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\LoginAsCustomer\Controller\Adminhtml\Login;

/**
 * Class Login
 * @package Magefan\LoginAsCustomer\Controller\Adminhtml\Login
 */
class Login extends \Magento\Backend\App\Action
{
    /**
     * @var \Magefan\LoginAsCustomer\Model\Login
     */
    protected $loginModel;
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession  = null;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager  = null;
    /**
     * @var \Magento\Framework\Url
     */
    protected $url = null;

    /**
     * Login constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magefan\LoginAsCustomer\Model\Login|null $loginModel
     * @param \Magento\Backend\Model\Auth\Session|null $authSession
     * @param \Magento\Store\Model\StoreManagerInterface|null $storeManager
     * @param \Magento\Framework\Url|null $url
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magefan\LoginAsCustomer\Model\Login $loginModel = null,
        \Magento\Backend\Model\Auth\Session $authSession = null,
        \Magento\Store\Model\StoreManagerInterface $storeManager = null,
        \Magento\Framework\Url $url = null
    ) {
        parent::__construct($context);
        $this->loginModel = $loginModel ?: $this->_objectManager->get(\Magefan\LoginAsCustomer\Model\Login::class);
        $this->authSession = $authSession ?: $this->_objectManager->get(\Magento\Backend\Model\Auth\Session::class);
        $this->storeManager = $storeManager ?: $this->_objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $this->url = $url ?: $this->_objectManager->get(\Magento\Framework\Url::class);
    }
    /**
     * Login as customer action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customerId = (int) $this->getRequest()->getParam('customer_id');

        $login = $this->loginModel->setCustomerId($customerId);

        $login->deleteNotUsed();

        $customer = $login->getCustomer();

        if (!$customer->getId()) {
            $this->messageManager->addError(__('Customer with this ID are no longer exist.'));
            $this->_redirect('customer/index/index');
            return;
        }

        $user = $this->authSession->getUser();
        $login->generate($user->getId());
        $customerStoreId = $this->getCustomerStoreId($customer);

        if ($customerStoreId) {
            $store = $this->storeManager->getStore($customerStoreId);
        } else {
            $store = $this->storeManager->getDefaultStoreView();
        }
        
        $redirectUrl = $this->url->setScope($store)
            ->getUrl('loginascustomer/login/index', ['secret' => $login->getSecret(), '_nosid' => true]);

        $this->getResponse()->setRedirect($redirectUrl);
    }

    /**
     * We're not using the $customer->getStoreId() method due to a bug where it returns the store for the customer's website
     * @param $customer
     * @return string
     */
    public function getCustomerStoreId(\Magento\Customer\Model\Customer $customer)
    {
        return $customer->getData('store_id');
    }

    /**
     * Check is allowed access
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magefan_LoginAsCustomer::login_button');
    }
}
