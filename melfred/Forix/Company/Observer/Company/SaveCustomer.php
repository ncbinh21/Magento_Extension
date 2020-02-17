<?php

namespace Forix\Company\Observer\Company;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Exception\LocalizedException;

class SaveCustomer implements ObserverInterface
{
    /**
     * @var \Magento\Company\Model\ResourceModel\Customer\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Company\Model\ResourceModel\Customer\Collection
     */
    protected $collectionCustomerCompany;

    /**
     * SaveCustomer constructor.
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Company\Model\ResourceModel\Customer\Collection $collectionCustomerCompany
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository,
        \Magento\Company\Model\ResourceModel\Customer\Collection $collectionCustomerCompany
    ) {
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->collectionCustomerCompany = $collectionCustomerCompany;
    }

    public function execute(Observer $observer)
    {
        $company = $observer->getDataObject();
        $customerCompanyCollection = $this->collectionCustomerCompany->addFieldToFilter('company_id', $company->getId());

        if ($customerCompanyCollection->getSize() > 0) {
            foreach ($customerCompanyCollection as $customerCompany) {
                try {
                    $customerRes = $this->customerRepository->getById($customerCompany->getId());
                    if ($company) {
                        $customer = $this->customerFactory->create()->load($customerCompany->getId());
                        $customer->setData('company_title', $company->getCompanyName());
                        $customer->getResource()->saveAttribute($customer, 'company_title');
                    }
                } catch (\Exception $exception) {
                    throw new LocalizedException(__('Can\'t save customer'));
                }
            }
        }

    }
}