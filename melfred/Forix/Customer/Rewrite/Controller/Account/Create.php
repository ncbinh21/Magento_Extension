<?php

namespace Forix\Customer\Rewrite\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Customer\Helper\Address;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Metadata\FormFactory;
use Magento\Customer\Model\Registration;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Escaper;
use Magento\Framework\UrlFactory;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\ObjectManager;

class Create extends \Magento\Customer\Controller\Account\Create
{
    /**
     * Customer register form page
     *
     * @return \Magento\Framework\Controller\Result\Redirect|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*');
            return $resultRedirect;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('company/account/create');
        return $resultRedirect;
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}