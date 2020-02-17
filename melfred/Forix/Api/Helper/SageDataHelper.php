<?php
/**
 * Created by Mercurial
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 */
namespace Forix\Api\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Forix\Payment\Model\CustomerQueue;

class SageDataHelper extends AbstractHelper
{
    protected $customerQueueCollectionFactory;
    protected $customerQueueFactory;
    protected $customerContactQueueCollectionFactory;
    protected $customerContactQueueFactory;
    protected $sage100Factory;
    protected $sage100;
    protected $customerFactory;
    protected $zipCodeFactory;
	protected $paymentHelper;

    public function __construct(
        Context $context,
        \Forix\Payment\Model\ResourceModel\CustomerQueue\CollectionFactory $customerQueueCollectionFactory,
        \Forix\Payment\Model\CustomerQueueFactory $customerQueueFactory,
        \Forix\Payment\Model\ResourceModel\CustomerContactQueue\CollectionFactory $customerContactQueueCollectionFactory,
        \Forix\Payment\Model\CustomerContactQueueFactory $customerContactQueueFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory,
		\Forix\Payment\Helper\PaymentHelper $paymentHelper,
        \Forix\Payment\Model\Sage100Factory $sage100Factory
    ) {
        $this->customerFactory = $customerFactory;
        $this->sage100Factory = $sage100Factory;
        $this->customerQueueFactory = $customerQueueFactory;
        $this->customerQueueCollectionFactory = $customerQueueCollectionFactory;
        $this->customerContactQueueFactory = $customerContactQueueFactory;
        $this->zipCodeFactory = $zipCodeFactory;
        $this->customerContactQueueCollectionFactory = $customerContactQueueCollectionFactory;
		$this->paymentHelper = $paymentHelper;
        parent::__construct($context);
    }

    public function getCustomerQueue($email){
        $customerQueue = $this->customerQueueCollectionFactory->create()->getAddEmailToFilter($email)->getFirstItem();
        return $customerQueue;
    }

    public function getCustomerNoSage($company)
    {
        try{
            $matchDistributor = $this->paymentHelper->isMatchDistributor($company->getPostcode());
            if(!$this->checkIsDistributor($company) && $matchDistributor) {
                if(!$company->getIsPushSage() && $matchDistributor) {
                    return false;
                }
            }

            $customerQueue = $this->getCustomerQueue($company->getCompanyEmail());
            if(!$customerQueue->getId()) {
                if($customerNo = $company->getCustomerNo()){
                    return $customerNo;
                }
                if($sageService = $this->getSageInstance()) {
                    if($nextCustomerNo = $sageService->getNextCustomerNo()) {
                        return $nextCustomerNo;
                    }
                }
            } else {
                return $customerQueue->getCustomerNo();
            }
        } catch (\Exception $exception) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('.modman/Forix_Api/app/code/Forix/Api/Helper/SageDataHelper.php:80');
            $logger->info($exception->getMessage());
            return false;
        }
        return false;
    }

    public function saveInforCompany($customerNo, $company)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('start save company');
        try{
            if($customerNo) {
                $customerQueue = $this->customerQueueCollectionFactory->create()->getAddEmailToFilter($company->getCompanyEmail())->getFirstItem();
                $customer = $this->customerFactory->create()->load($company->getSuperUserId());
                if(strpos(trim($company->getCompanyName()), ' ')){
                    $contactCode = trim(substr($company->getCompanyName(), 0, strpos($company->getCompanyName(), ' ')));
                } else {
                    $contactCode = trim($company->getCompanyName());
                }
                $customer->setCustomerNo($customerNo);
                $company->setCustomerNo($customerNo);
                if(!$customerQueue->getId()) {
                    $customerQueueCustomerNo = $this->customerQueueCollectionFactory->create()->getCustomerNoFilter($customerNo)->getFirstItem();
                    if(!$customerQueueCustomerNo->getId()) {
                        $customer->setContactCode($contactCode);
                        $logger->info('create customer (company) in sage');
                        if($this->getSageInstance()->createCustomer($company)) {
                            if(!$this->getSageInstance()->createCustomerContact($customer)) {
                                $contactCode = null;
                            } else {
                                $company->setContactCode($contactCode);
                                $this->getSageInstance()->updateCustomer($company);
                            }
                            $company->setContactCode($contactCode);
                            $customerQueue = $this->customerQueueFactory->create();
                            $this->saveCustomerQueue($customerQueue, $company->getCompanyEmail(), $customerNo, $contactCode, 1);
                        }  else {
                            $company->setCustomerNo(null);
                            $company->setContactCode(null);
                        }
                    } else {
                        $logger->info('update customer (company) in sage case 1');
                        if($this->getSageInstance()->updateCustomer($company)) {
                            $customer->setContactCode($contactCode);
                            if($contactCode = $customerQueueCustomerNo->getContactCode()) {
                                $customer->setContactCode($contactCode);
                                $this->getSageInstance()->updateCustomerContact($customer);
                            } else {
                                if(!$this->getSageInstance()->createCustomerContact($customer)) {
                                    $contactCode = null;
                                } else {
                                    $company->setContactCode($contactCode);
                                    $this->getSageInstance()->updateCustomer($company);
                                }
                            }
                            $company->setContactCode($contactCode);
                            $this->saveCustomerQueue($customerQueueCustomerNo, $company->getCompanyEmail(), $customerNo, $contactCode, 1);
                        }
                    }
                } else {
                    $logger->info('update customer (company) in sage case 2');
                    if($this->getSageInstance()->updateCustomer($company)) {
                        $customer->setContactCode($contactCode);
                        if($contactCode = $customerQueue->getContactCode()) {
                            $customer->setContactCode($contactCode);
                            $this->getSageInstance()->updateCustomerContact($customer);
                        } else {
                            if(!$this->getSageInstance()->createCustomerContact($customer)) {
                                $contactCode = null;
                            } else {
                                $company->setContactCode($contactCode);
                                $this->getSageInstance()->updateCustomer($company);
                            }
                        }
                        $company->setContactCode($contactCode);
                        $this->saveCustomerQueue($customerQueue, $company->getCompanyEmail(), $customerNo, $contactCode, 1);
                    }
                }
            }
        } catch (\Exception $exception) {
            $logger->info('error when save information company');
            return $company;
        }
        return $company;
    }

    protected function saveCustomerQueue($customerQueue, $email, $customerNo, $contactCode, $status)
    {
        $customerQueue->setData('customer_email', $email);
        $customerQueue->setData('customer_no', $customerNo);
        $customerQueue->setData('contact_code', $contactCode);
        $customerQueue->setData('status', $status);
        $customerQueue->save();
        return true;
    }

    public function checkIsDistributor($company) {
		if($company->getCustomerGroupId() && $groupsetting = $this->scopeConfig->getValue('forix_customer/customergroup/distributor_group')){
			$arrGroup = explode(',', $groupsetting);
            $companyGroup  = $company->getCustomerGroupId();
            if (in_array($companyGroup, $arrGroup)) {
                return true;
            }	
		}
		return false;
	}

    protected function getSageInstance(){
        if(!$this->sage100){
            $this->sage100 = $this->sage100Factory->create();
        }
        return $this->sage100;
    }
}