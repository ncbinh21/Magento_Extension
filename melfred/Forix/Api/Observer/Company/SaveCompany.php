<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */

namespace Forix\Api\Observer\Company;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Forix\Payment\Model\CustomerQueue;

class SaveCompany implements ObserverInterface
{
    protected $customerRepository;
    protected $sage100Factory;
    protected $sageDataHelper;
    protected $sage100;
    protected $paymentHelper;
    protected $customerQueueFactory;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Forix\Payment\Model\Sage100Factory $sage100Factory,
        \Forix\Payment\Helper\PaymentHelper $paymentHelper,
        \Forix\Payment\Model\CustomerQueueFactory $customerQueueFactory,
        \Forix\Api\Helper\SageDataHelper $sageDataHelper
    )
    {
        $this->customerRepository = $customerRepository;
        $this->sage100Factory = $sage100Factory;
        $this->sageDataHelper = $sageDataHelper;
        $this->paymentHelper = $paymentHelper;
        $this->customerQueueFactory = $customerQueueFactory;
    }


    protected function deleteCustomerQueue($customerNo)
    {
        $customerQueue = $this->customerQueueFactory->create();
        $customerQueue->getResource()->getConnection()->delete($customerQueue->getResource()->getMainTable(), ['customer_no = ?' => $customerNo]);
    }

    protected function saveCustomerQueue($customerQueue, $email, $customerNo, $contactCode)
    {
        if (!$customerQueue) {
            $customerQueue = $this->customerQueueFactory->create();
        }
        $customerQueue->setData('customer_email', $email);
        $customerQueue->setData('customer_no', $customerNo);
        $customerQueue->setData('contact_code', $contactCode);
        $customerQueue->save();
        return $customerQueue;
    }

    public function createCustomerNo($company)
    {
        $customerNo = $this->sageDataHelper->getCustomerNoSage($company);
        $this->sageDataHelper->saveInforCompany($customerNo, $company);
    }

    public function execute(Observer $observer)
    {
        try{
        $company = $observer->getDataObject();
        if ($company->getId()) {
            if ($customerNo = $company->getCustomerNo()) {
                $this->sageDataHelper->saveInforCompany($customerNo, $company);
                // Insert 1 record vào customer queue
                $customerQueue = $this->customerQueueFactory->create();
                $this->deleteCustomerQueue($customerNo);
                $customerQueue->setStatus(1);
                $this->saveCustomerQueue($customerQueue, $company->getCompanyEmail(), $customerNo, $company->getContactCode());
                return true;
            }
            $customerQueue = $this->sageDataHelper->getCustomerQueue($company->getCompanyEmail());
            if (!$customerQueue->getId()) {
                if ($this->sageDataHelper->checkIsDistributor($company)) {
                    $this->createCustomerNo($company);//--
                }else{
                    if (!$this->paymentHelper->isMatchDistributor($company->getPostcode())) {
                        $this->createCustomerNo($company); //--
                    }else{
                        if($company->getIsPushSage()){
                            $this->createCustomerNo($company); //--
                        }
                    }
                }
            } else {
                if($company->getCustomerNo()) {
                    if ($customerQueue->getCustomerNo() != $company->getCustomerNo()) {
                        $this->deleteCustomerQueue($company->getCustomerNo());
                        $this->saveCustomerQueue($customerQueue, $company->getCompanyEmail(), $company->getCustomerNo(), $company->getContactCode());
                        $this->sageDataHelper->saveInforCompany($company->getCustomerNo(), $company);
                    }
                }else{
                    $this->sageDataHelper->saveInforCompany($customerQueue->getCustomerNo(), $company);
                }
            }
        } else {
            $this->createCustomerNo($company);
        }
        }catch (\Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/hidro_debug.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('.modman/Forix_Api/app/code/Forix/Api/Observer/Company/SaveCompany.php:98');
            $logger->info($e->getMessage());
            throw $e;
        }
        return false;
    }
}