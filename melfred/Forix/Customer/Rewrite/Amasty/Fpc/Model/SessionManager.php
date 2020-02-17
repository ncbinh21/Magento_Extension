<?php

namespace Forix\Customer\Rewrite\Amasty\Fpc\Model;

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

class SessionManager extends \Amasty\Fpc\Model\SessionManager
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var CustomerSession
     */
    protected $customerSession;
    /**
     * @var GroupRepository
     */
    protected $customerGroupRepository;
    /**
     * @var HttpContext
     */
    protected $httpContext;
    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;
    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        StoreManagerInterface $storeManager,
        CustomerSession $customerSession,
        GroupRepository $customerGroupRepository,
        HttpContext $httpContext,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerDataFactory = $customerDataFactory;
        $this->accountManagement = $accountManagement;
        parent::__construct($storeManager, $customerSession, $customerGroupRepository, $httpContext, $customerDataFactory, $accountManagement, $customerRepository);
    }

    /**
     * @param $customerGroup
     * @param $websiteId
     *
     * @return CustomerInterface
     */
    public function createUser($customerGroup, $websiteId)
    {
        /** @var CustomerInterface $customerData */
        $customerData = $this->customerDataFactory->create();

        $customerData
            ->setFirstname($this->getCustomerName($customerGroup, $websiteId))
            ->setLastname('Amasty')
            ->setEmail($this->getCustomerEmail($customerGroup, $websiteId))
            ->setWebsiteId($websiteId)
            ->setGroupId($customerGroup);

        $customerData->setCustomAttribute('skip_create_sage', 1);
        $customerData->setCustomAttribute('company_title', 'Amasty');

        $customerData = $this->accountManagement->createAccount($customerData);

        return $customerData;
    }
}
