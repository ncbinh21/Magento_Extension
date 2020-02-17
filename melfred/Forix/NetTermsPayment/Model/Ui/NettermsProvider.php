<?php

namespace Forix\NetTermsPayment\Model\Ui;

use Magento\Checkout\Model\ConfigProviderInterface;

class NettermsProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var Company
     */
    protected $company;

    /**
     * @var \Magento\Company\Api\CompanyManagementInterface
     */
    protected $companyManagement;

    /**
     * CustomerInfo constructor.
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     */
    public function __construct(
        \Magento\Company\Api\CompanyManagementInterface $companyManagement,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
    ) {
        $this->companyManagement = $companyManagement;
        $this->customerRepository = $customerRepository;
        $this->currentCustomer = $currentCustomer;
    }

    public function getConfig()
    {
        $isNetterms = 2;
        if ($customerId = $this->currentCustomer->getCustomerId()) {
            $customer = $this->customerRepository->getById($customerId);
            $company = $this->getCustomerCompany($customerId);
            if ($company && (($company->getCustomerNo() && ($company->getTermsCode() == 30 || $company->getTermsCode() == 45)) ||  $company->getIsNettermActiveCompany())) {
                $isNetterms = 1;
            } else {
                $isNetterms = 0;
            }
        }
        $additionalVariables['is_netterms'] = $isNetterms;
        return $additionalVariables;
    }

    /**
     * @return \Magento\Company\Api\Data\CompanyInterface
     */
    public function getCustomerCompany($customerId)
    {
        if ($this->company !== null) {
            return $this->company;
        }

        if ($customerId) {
            $this->company = $this->companyManagement->getByCustomerId($customerId);
        }

        return $this->company;
    }
}
