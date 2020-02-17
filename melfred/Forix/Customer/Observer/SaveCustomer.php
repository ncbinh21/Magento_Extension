<?php

namespace Forix\Customer\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SaveCustomer implements ObserverInterface
{
    /**
     * @var \Magento\Company\Api\CompanyRepositoryInterface
     */
    protected $companyRepository;

    /**
     * SaveCustomer constructor.
     * @param \Magento\Company\Api\CompanyRepositoryInterface $companyRepository
     */
    public function __construct(
        \Magento\Company\Api\CompanyRepositoryInterface $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $customer = $observer->getCustomer();
        try{
            if ($customer->getExtensionAttributes() && $customer->getExtensionAttributes()->getCompanyAttributes() && $companyId = $customer->getExtensionAttributes()->getCompanyAttributes()->getCompanyId()) {
                $company = $this->companyRepository->get($companyId);
                $customer->setCustomAttribute('company_title', $company->getCompanyName());
            }
        } catch (\Exception $exception){
            throw new \Exception(__('Can\'t save customer'));
        }
    }
}