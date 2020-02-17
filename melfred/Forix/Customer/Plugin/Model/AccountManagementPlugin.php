<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\Customer\Plugin\Model;
use \Magento\Framework\Exception\NoSuchEntityException;

class AccountManagementPlugin
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Company\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Company\Model\StructureFactory
     */
    protected $structureFactory;

    /**
     * @var \Magento\Company\Model\UserRoleFactory
     */
    protected $userRoleFactory;

    /**
     * @var \Magento\Company\Model\ResourceModel\Role\CollectionFactory
     */
    protected $roleCollection;

    /**
     * @var CollectionFactory
     */
    protected $structureCollection;

    /**
     * @var \Magento\Company\Api\CompanyRepositoryInterface
     */
    protected $companyRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * AccountManagementPlugin constructor.
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Company\Model\ResourceModel\Structure\CollectionFactory $structureCollection
     * @param \Magento\Company\Api\CompanyRepositoryInterface $companyRepository
     * @param \Magento\Company\Model\CustomerFactory $customerFactory
     * @param \Magento\Company\Model\StructureFactory $structureFactory
     * @param \Magento\Company\Model\UserRoleFactory $userRoleFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Company\Model\ResourceModel\Role\CollectionFactory $roleCollection
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Company\Model\ResourceModel\Structure\CollectionFactory $structureCollection,
        \Magento\Company\Api\CompanyRepositoryInterface $companyRepository,
        \Magento\Company\Model\CustomerFactory $customerFactory,
        \Magento\Company\Model\StructureFactory $structureFactory,
        \Magento\Company\Model\UserRoleFactory $userRoleFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Company\Model\ResourceModel\Role\CollectionFactory $roleCollection
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->scopeConfig = $scopeConfig;
        $this->structureCollection = $structureCollection;
        $this->companyRepository = $companyRepository;
        $this->customerFactory = $customerFactory;
        $this->structureFactory = $structureFactory;
        $this->userRoleFactory = $userRoleFactory;
        $this->roleCollection = $roleCollection;
        $this->_logger = $logger;
    }

    /**
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param callable $proceed
     * @param $customerEmail
     * @param null $websiteId
     * @return mixed
     */
    public function aroundIsEmailAvailable(\Magento\Customer\Model\AccountManagement $accountManagement, callable $proceed, $customerEmail, $websiteId = null)
    {
        $result = $proceed($customerEmail, $websiteId);
        setcookie('name_customer', '', time() + (86400 * 30), "/");
        if(!$result) {
            try {
                if ($websiteId === null) {
                    $websiteId = $this->storeManager->getStore()->getWebsiteId();
                }
                $customer = $this->customerRepository->get($customerEmail, $websiteId);
                setcookie('name_customer', $customer->getFirstname(), time() + (86400 * 30), "/"); // 86400 = 1 day
            } catch (NoSuchEntityException $e) {
                setcookie('name_customer', '', time() + (86400 * 30), "/");
            }
        }
        return $result;
    }

    /**
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param null $password
     * @param string $redirectUrl
     * @return array
     */
    public function beforeCreateAccount(\Magento\Customer\Model\AccountManagement $accountManagement, \Magento\Customer\Api\Data\CustomerInterface $customer, $password = null, $redirectUrl = '')
    {
        if(!$customer->getCustomAttribute('country_name')) {
            $customer->setCustomAttribute('country_name', 'US');
        }

        if(!$customer->getCustomAttribute('company_title')) {
            if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getCompanyAttributes() && $companyId = $customer->getExtensionAttributes()->getCompanyAttributes()->getCompanyId()) {
                $company = $this->companyRepository->get($companyId);
                $customer->setCustomAttribute('company_title', $company->getCompanyName());
            }
            if($customer->getAddresses() && count($customer->getAddresses()) > 0) {
                foreach ($customer->getAddresses() as $address) {
                    if($address->isDefaultBilling() && $address->getCompany()) {
                        $customer->setCustomAttribute('company_title', $address->getCompany());
                    }
                }
            }
        }
        return [$customer, $password, $redirectUrl];
    }

    public function beforeCreateAccountWithPasswordHash(\Magento\Customer\Model\AccountManagement $accountManagement, \Magento\Customer\Api\Data\CustomerInterface $customer, $hash, $redirectUrl = '')
    {
        $companyId = $this->scopeConfig->getValue('company/general/mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        try {
            $company = $this->companyRepository->get($companyId);
            if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getCompanyAttributes() && $customer->getExtensionAttributes()->getCompanyAttributes()->getCompanyId()) {
                return [$customer, $hash, $redirectUrl];
            }
            if($customer->getGroupId() != $company->getCustomerGroupId()) {
                $customer->setGroupId($company->getCustomerGroupId());
            }
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Can\'t find company default. Please try again later.'));
        }
        return [$customer, $hash, $redirectUrl];
    }

    public function afterCreateAccountWithPasswordHash(\Magento\Customer\Model\AccountManagement $accountManagement, $customer)
    {
        if ($customer->getId()) {
            if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getCompanyAttributes() && $customer->getExtensionAttributes()->getCompanyAttributes()->getCompanyId()) {
                return $customer;
            }
            $companyId = $this->scopeConfig->getValue('company/general/mode', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $telephone = null; // create by form frontend
            $roleCollect = $this->roleCollection->create();
            $roleFirst = $roleCollect->addFieldToFilter('company_id', $companyId)->getFirstItem();
            if ($roleFirst) {
                try {
                    $company = $this->companyRepository->get($companyId);
                    $strucCollection = $this->structureCollection->create();
                    $strucCom = $strucCollection->addFieldToFilter('entity_id', $company->getSuperUserId())->getFirstItem();
                    if ($strucCom->getId()) {
                        if($this->registry->registry('forix_company_admin') == 1) {
                            return $customer;
                        }
                        $customerCompany = $this->customerFactory->create();
                        $customerCompany->setCustomerId($customer->getId());
                        $customerCompany->setCompanyId($companyId);
                        $customerCompany->setStatus(1);
                        $customerCompany->setTelephone($telephone);
                        $customerCompany->save();

                        $roleCompany = $this->userRoleFactory->create();
                        $roleCompany->setRoleId($roleFirst->getId());
                        $roleCompany->setUserId($customer->getId());
                        $roleCompany->save();

                        $strucCompany = $this->structureFactory->create();
                        $strucCompany->setParentId($strucCom->getId());
                        $strucCompany->setEntityId($customer->getId());
                        $strucCompany->setEntityType(0);
                        $strucCompany->setPath($strucCom->getId() . '/' . $customer->getId());
                        $strucCompany->setPosition(0);
                        $strucCompany->setLevel(1);
                        $strucCompany->save();
                        return $customer;
                    } else {
                        $this->_logger->error(__("Can't find any structure with entity_id %1", $company->getSuperUserId()));
                    }
                } catch (\Exception $e) {
                    $this->_logger->error($e);
                }
            } else {
                $this->_logger->error(__("Can't find any role for the company. Company Id: %1", $companyId));
            }
            throw new \Magento\Framework\Exception\LocalizedException(__('Can\'t create customer. Please try again later.'));
        }
    }
}
