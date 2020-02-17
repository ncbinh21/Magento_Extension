<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_Fpc
 */


namespace Amasty\Fpc\Model;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Context as CustomerContext;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\ResourceModel\GroupRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

class SessionManager
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var CustomerSession
     */
    private $customerSession;
    /**
     * @var GroupRepository
     */
    private $customerGroupRepository;
    /**
     * @var HttpContext
     */
    private $httpContext;
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    private $customerDataFactory;
    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        GroupRepository $customerGroupRepository,
        HttpContext $httpContext,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->storeManager = $storeManager;
        $this->customerSession = $customerSession;
        $this->customerGroupRepository = $customerGroupRepository;
        $this->httpContext = $httpContext;
        $this->customerDataFactory = $customerDataFactory;
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
    }

    public function setCustomerGroup($customerGroupId)
    {
        try {
            $this->customerGroupRepository->getById($customerGroupId);
        } catch (NoSuchEntityException $exception) {
            $customerGroupId = Group::NOT_LOGGED_IN_ID;
        }

        $this->httpContext->setValue(
            CustomerContext::CONTEXT_GROUP,
            $customerGroupId,
            Group::NOT_LOGGED_IN_ID
        );

        if ($customerGroupId == Group::NOT_LOGGED_IN_ID) {
            $this->logout();

            return $this;
        }

        $customer = $this->getCustomerByGroup($customerGroupId);

        $this->setCustomerDataAsLoggedIn($customer);

        return $this;
    }

    public function setCurrency($currencyCode)
    {
        if (!$currencyCode) {
            $currencyCode = $this->storeManager->getWebsite()->getDefaultStore()->getDefaultCurrency()->getCode();
        }

        $this->storeManager->getStore()->setCurrentCurrencyCode($currencyCode);

        return $this;
    }

    protected function getCustomerByGroup($customerGroupId)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();

        try {
            return $this->customerRepository->get($this->getCustomerEmail($customerGroupId, $websiteId));
        } catch (NoSuchEntityException $e) {
            return $this->createUser($customerGroupId, $websiteId);
        }
    }

    /**
     * @param $customerGroup
     * @param $websiteId
     *
     * @return CustomerInterface
     */
    protected function createUser($customerGroup, $websiteId)
    {
        /** @var CustomerInterface $customerData */
        $customerData = $this->customerDataFactory->create();

        $customerData
            ->setFirstname($this->getCustomerName($customerGroup, $websiteId))
            ->setLastname('Amasty')
            ->setEmail($this->getCustomerEmail($customerGroup, $websiteId))
            ->setWebsiteId($websiteId)
            ->setGroupId($customerGroup);

        $customerData = $this->accountManagement->createAccount($customerData);

        return $customerData;
    }

    protected function getCustomerEmail($customerGroup, $websiteId)
    {
        return $this->getCustomerName($customerGroup, $websiteId) . '@example.net';
    }

    protected function getCustomerName($customerGroup, $websiteId)
    {
        return "FPC_Crawler_{$customerGroup}_{$websiteId}";
    }

    /**
     * Copy of native setCustomerDataAsLoggedIn but without event dispatch
     * @see \Magento\Customer\Model\Session::setCustomerDataAsLoggedIn
     *
     * @param $customer
     *
     * @return $this
     */
    protected function setCustomerDataAsLoggedIn($customer)
    {
        $this->httpContext->setValue(CustomerContext::CONTEXT_AUTH, true, false);
        $this->customerSession->setCustomerData($customer);

        return $this;
    }

    protected function logout()
    {
        $this->customerSession->setCustomerId(null);
        $this->customerSession->setCustomerGroupId(Group::NOT_LOGGED_IN_ID);

        $this->httpContext->unsValue(CustomerContext::CONTEXT_AUTH);
    }
}
