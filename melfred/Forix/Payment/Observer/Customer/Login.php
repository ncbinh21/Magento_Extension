<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\Payment\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
/**
 * Persistent Session Observer
 */
class Login implements ObserverInterface
{
    protected $customerFactory;
    protected $scopeConfig;
    protected $sage100Factory;
    protected $companyFactory;
    protected $customerQueueCollectionFactory;
    protected $resource;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Forix\Payment\Model\Sage100Factory $sage100Factory,
        \Magento\Company\Api\CompanyManagementInterface $companyRepository,
        \Magento\Company\Model\CompanyFactory $companyFactory,
        \Forix\Payment\Model\ResourceModel\CustomerQueue\CollectionFactory $customerQueueCollectionFactory,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->customerFactory = $customerFactory;
        $this->sage100Factory = $sage100Factory;
        $this->companyRepository = $companyRepository;
        $this->companyFactory = $companyFactory;
        $this->customerQueueCollectionFactory = $customerQueueCollectionFactory;
        $this->resource = $resource;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        if ($this->syncStatus()) {
            try {
                $customer = $observer->getEvent()->getCustomer();
                $companyRes = $this->companyRepository->getByCustomerId($customer->getId());
                if ($companyRes) {
                    if ($customerNo = $companyRes->getCustomerNo()) {
                        $this->saveTermsCodeCompany($companyRes->getId(), $customerNo);
                    } else {
                        $company = $this->companyFactory->create()->load($companyRes->getId());
                        $company->save();
                        $customerQueue = $this->customerQueueCollectionFactory->create()->getAddEmailToFilter($companyRes->getCompanyEmail())->getFirstItem();
                        if ($customerQueue->getId()) {
                            $this->saveTermsCodeCompany($companyRes->getId(), $customerQueue->getCustomerNo());
                        }
                    }
                }
            }catch (\Exception $e){
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/hidro_debug.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('.modman/Forix_Payment/app/code/Forix/Payment/Observer/Customer/Login.php:43');
                $logger->info($e->getMessage());
            }
        }
    }

    protected function saveTermsCodeCompany($id, $customerNo)
    {
        $sageFactory = $this->sage100Factory->create();
        if ($data = $sageFactory->getCustomer($customerNo)) {
            $netterm = $data->getTermsCode();
            $query = $this->resource->getConnection();
            $data = ['terms_code' => $netterm];
            $query->update('company', $data, 'entity_id = '.$id.'');
        }
    }
    /**
     *
     */
    protected function syncStatus()
    {
        return $this->scopeConfig->getValue("payment/sage100_service/sage100_payment_config/sync_customer");
    }
}
