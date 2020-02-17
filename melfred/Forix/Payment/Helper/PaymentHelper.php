<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 08/11/2018
 * Time: 14:19
 */

namespace Forix\Payment\Helper;

use Magento\Customer\Model\AddressFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\DataObject;
use \Forix\Payment\Model\CustomerQueue;
use \Magento\Framework\Exception\LocalizedException;
class PaymentHelper extends AbstractHelper
{
    protected $_zipcodeRepository;
    protected $searchCriteriaInterfaceFactory;
    /**
     * @var \Forix\Payment\Model\Sage100Factory
     */
    protected $_sage100Factory;
    protected $_sage100;
    protected $_orderScheduleFactory;
    protected $_customerRegistry;
    protected $_customerFactory;
    protected $_customerScheduleFactory;
    protected $_customerQueueCollectionFactory;
    protected $_addressFactory;
    protected $companyRepository;
    protected $companyFactory;
    protected $resource;

    public function __construct(
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Forix\Payment\Model\CustomerQueueFactory $customerQueueFactory,
        \Forix\Payment\Model\ResourceModel\CustomerQueue\CollectionFactory $customerQueueCollectionFactory,
        \Forix\Payment\Model\Sage100Factory $sage100Factory,
        \Magento\Framework\Api\SearchCriteriaInterfaceFactory $searchCriteriaInterfaceFactory,
        \Forix\Distributor\Api\ZipcodeRepositoryInterface $zipcodeRepository,
        \Magento\Company\Api\CompanyManagementInterface $companyRepository,
        \Magento\Company\Model\CompanyFactory $companyFactory,
        \Magento\Framework\App\ResourceConnection $resource,
        Context $context)
    {
        $this->_sage100Factory = $sage100Factory;
        $this->_customerRegistry = $customerRegistry;
        $this->_customerScheduleFactory = $customerQueueFactory;
        $this->_customerQueueCollectionFactory = $customerQueueCollectionFactory;
        $this->_customerFactory = $customerFactory;
        $this->_zipcodeRepository = $zipcodeRepository;
        $this->searchCriteriaInterfaceFactory = $searchCriteriaInterfaceFactory;
        $this->_addressFactory = $addressFactory;
        $this->companyRepository = $companyRepository;
        $this->companyFactory = $companyFactory;
        $this->resource = $resource;
        parent::__construct($context);
    }


    protected function getSageInstance(){
        if(!$this->_sage100){
            $this->_sage100 = $this->_sage100Factory->create();
        }
        return $this->_sage100;
    }

    /**
     * @param $zipCode
     * @return bool
     */
    public function isMatchDistributor($zipCode)
    {
//        $searchCriteria = $this->searchCriteriaInterfaceFactory->create();
//        $searchCriteria->setFilterGroups([new DataObject([
//            'filters' => [
//                new DataObject([
//                    "field" => "zipcode",
//                    "value" => $zipCode,
//                    "condition_type" => "eq"
//                ])
//            ]
//        ])]);
//        $result = $this->_zipcodeRepository->getList($searchCriteria);
//        if ($result->getTotalCount()) {
//            return true;
//        }
        $connection = $this->resource->getConnection();
        $results = $connection->select()->from(['main' => $connection->getTableName('forix_distributor_zipcode')], [])
            ->joinInner(['am' => $connection->getTableName('amasty_amlocator_location')], 'main.distributor_id = am.id',[])
            ->joinInner(['com' => $connection->getTableName('company_distributors')], 'main.distributor_id = com.distributor_id',[])
            ->where('main.zipcode = ?', $zipCode)
            ->where('am.status = ?', 1);
            $results->reset('SELECT');
            $results->columns('COUNT(*)');

        $haveActiveDistributor = $this->resource->getConnection()->fetchOne($results);
        if($haveActiveDistributor > 0) {
            return true;
        }
        return false;
    }

    protected function getCustomerQueue($email){
        $customerQueue = $this->_customerQueueCollectionFactory->create()->getAddEmailToFilter($email)->getFirstItem();
        if($customerQueue->getId()){
            return $customerQueue;
        }
        return false;
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @return null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkCustomerNo($customer){
        if($companyRes = $this->companyRepository->getByCustomerId($customer->getId())) {
            $company = $this->companyFactory->create()->load($companyRes->getId());
            if($customerNo = $company->getCustomerNo()) {
                return $customerNo;
            }
            //must have customer no
            $company->setIsPushSage(true);
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info('save company if do not customer no');
            $company->save();
            $customerQueue = $this->_customerQueueCollectionFactory->create()->getAddEmailToFilter($companyRes->getCompanyEmail())->getFirstItem();
            if($customerQueue->getId()) {
                return $customerQueue->getCustomerNo();
            }
        }
        return null;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @return null|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Service_Exception
     */
    public function getCustomerNo($order)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $nextCustomerNo = null;
        if ($customerId = $order->getCustomerId()) {
            $logger->info('get customer no of customer');
            $customer = $this->_customerRegistry->retrieve($customerId);
            //create customer no in sage
            if($nextCustomerNo = $this->checkCustomerNo($customer)) {
                $order->setData('customer', $customer);
                $order->setData('customer_no', $nextCustomerNo);
            } else {
                return null;
            }
        } else {
            $logger->info('get customer no of guest');
            if ($nextCustomerNo = $this->getSageInstance()->getNextCustomerNo()) {
                $customer = $this->_customerFactory->create();
                $billingAddress = $order->getBillingAddress();
                $telephoneFormat = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", trim($billingAddress->getTelephone()));
                $address = $this->_addressFactory->create(
                    ['data' => [
                        'firstname' => trim($billingAddress->getFirstname()),
                        'lastname' => trim($billingAddress->getLastname()),
                        'street' => $billingAddress->getStreet(),
                        'city' => $billingAddress->getCity(),
                        'country_id' => $billingAddress->getCountryId(),
                        'region_id' => $billingAddress->getRegionId(),
                        'region' => $billingAddress->getRegion(),
                        'postcode' => $billingAddress->getPostcode(),
                        'telephone' => $telephoneFormat,
                    ]]
                );
                $customer->addAddress($address);
                $customer->setData('telephone', $telephoneFormat);
                $customer->setData('firstname', trim($billingAddress->getFirstname()));
                $customer->setData('lastname', trim($billingAddress->getLastname()));
                $customer->setData('email', trim($order->getCustomerEmail()));
                $customer->setData('customer_no', $nextCustomerNo);
                if($this->getSageInstance()->createCustomer($customer)) {
                    $order->setData('customer', $customer);
                    $order->setData('customer_no', $customer->getCustomerNo());
                } else {
                    return null;
                }
            }
        }
        $logger->info('Customer No: ' . $nextCustomerNo);
        return $nextCustomerNo;
    }

    public function getTaxScheduleCustomerFromSage($customerNo)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('get TaxSchedule from Customer No: ' . $customerNo);
        try {
            $customerSage = $this->getSageInstance()->getCustomer($customerNo);
            return $customerSage->getTaxSchedule();
        } catch (\Exception $exception) {
            $logger->info('Error when get TaxSchedule: ' . $exception->getMessage());
            return null;
        }
    }
}