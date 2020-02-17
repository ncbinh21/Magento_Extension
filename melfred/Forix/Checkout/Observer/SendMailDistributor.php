<?php

namespace Forix\Checkout\Observer;

use Forix\Distributor\Model\ResourceModel\CompanyDistributor\CollectionFactory;
use Magento\Framework\Event\ObserverInterface;

class SendMailDistributor implements ObserverInterface
{
    /**
     * Recipient email config path
     */
    const XML_PATH_EMAIL_RECIPIENT = 'forix_custom_checkout/distributor/email';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Forix\Distributor\Model\ZipcodeFactory
     */
    protected $zipCodeFactory;

    /**
     * @var \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory
     */
    protected $collectionLocationFactory;

    /**
     * @var \Magento\Company\Model\ResourceModel\Company\CollectionFactory
     */
    protected $collectionCompanyFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CollectionFactory
     */
    protected $collectionCompanyDistriFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Company\Model\CompanyManagement
     */
    protected $companyManagement;

    /**
     * @var \Forix\Checkout\Helper\Data
     */
    protected $helperData;

    /**
     * SendMailDistributor constructor.
     * @param \Forix\Checkout\Helper\Data $helperData
     * @param \Psr\Log\LoggerInterface $logger
     * @param CollectionFactory $collectionCompanyDistriFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Company\Model\ResourceModel\Company\CollectionFactory $collectionCompanyFactory
     * @param \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory
     * @param \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Directory\Model\CountryFactory $countryFactory
     * @param \Magento\Company\Model\CompanyManagement $companyManagement
     */
    public function __construct(
        \Forix\Checkout\Helper\Data $helperData,
        \Psr\Log\LoggerInterface $logger,
        \Forix\Distributor\Model\ResourceModel\CompanyDistributor\CollectionFactory $collectionCompanyDistriFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Company\Model\ResourceModel\Company\CollectionFactory $collectionCompanyFactory,
        \Amasty\Storelocator\Model\ResourceModel\Location\CollectionFactory $collectionLocationFactory,
        \Forix\Distributor\Model\ZipcodeFactory $zipCodeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Magento\Company\Model\CompanyManagement $companyManagement
    ) {
        $this->helperData = $helperData;
        $this->logger = $logger;
        $this->collectionCompanyDistriFactory = $collectionCompanyDistriFactory;
        $this->customerRepository = $customerRepository;
        $this->collectionCompanyFactory = $collectionCompanyFactory;
        $this->collectionLocationFactory = $collectionLocationFactory;
        $this->zipCodeFactory = $zipCodeFactory;
        $this->storeManager = $storeManager;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->countryFactory = $countryFactory;
        $this->companyManagement = $companyManagement;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if($order = $observer->getEvent()->getOrder()) {
                if(!$this->helperData->checkDistributorOrNotZone($order)) {
                    $this->getDistributorWithZipcode($order);
                }
            } elseif($orders = $observer->getEvent()->getOrders()) {
                if(count($orders) > 0) {
                    foreach ($orders as $order) {
                        if(!$this->helperData->checkDistributorOrNotZone($order)) {
                            $this->getDistributorWithZipcode($order);
                        }
                    }
                }
            } else {
                //
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
    }

    public function getDistributorWithZipcode($order)
    {
        try {
            $zipCode = $order->getBillingAddress()->getPostcode();
            $distributorZipCode = $this->zipCodeFactory->create()->getCollection()->addFieldToFilter('zipcode', $zipCode)->getFirstItem();
            if ($distributorId = $distributorZipCode->getDistributorId()) {
                $location = $this->collectionLocationFactory->create()->addFieldToFilter('id', $distributorId)->getFirstItem();
                $companyDistributors = $this->collectionCompanyDistriFactory->create()->addFieldToFilter('distributor_id', $distributorId);
                foreach ($companyDistributors as $companyDistributor) {
                    if ($companyId = $companyDistributor->getCompanyId()) {
                        $company = $this->collectionCompanyFactory->create()->addFieldToFilter('entity_id', $companyId)->addFieldToFilter('status', 1)->getFirstItem();
                        if ($companyAdminId = $company->getSuperUserId()) {
                            $customer = $this->customerRepository->getById($companyAdminId);
                            $this->sendMailDistributor($order, $customer->getEmail(), $location->getName());
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\LocalizedException($exception->getMessage());
        }
    }

    public function sendMailDistributor($order, $email, $distributorName = '')
    {

        try {
            $sender = [
                'name' => 'Melfred Borzall Store',
                'email' => 'sales@melfredborzall.com',
            ];

            $nameMethod = '';
            if($order->getPayment() && isset($order->getPayment()->getAdditionalInformation()['method_title']) && $order->getPayment()->getAdditionalInformation()['method_title']){
                $nameMethod = $order->getPayment()->getAdditionalInformation()['method_title'];
            }

            $pdt_date = date('F d, Y', strtotime($order->getCreatedAt())) . " at ".date('h:i:s A', strtotime($order->getCreatedAt()))." PDT";

//            $nameOfCustomer = $order->getCustomerName();
//            if($order->getCustomerId() && $companyName = $this->getCompanyNameFromCustomer($order->getCustomerId())) {
//                $nameOfCustomer = $companyName;
//            }
            $nameOfCustomer = $order->getBillingAddress()->getCompany();
	        $templateVars = [
                'order' => $order,
		        'pdt_date'=>$pdt_date,
                'distributor_name'  => $distributorName,
                'customer_name'  => $nameOfCustomer,
                'shipping_street'  => $order->getShippingAddress()->getStreet()[0],
                'billing_street'  => $order->getBillingAddress()->getStreet()[0],
                'shipping_country' => $this->getCountryname($order->getShippingAddress()->getCountryId()),
                'billing_country' => $this->getCountryname($order->getBillingAddress()->getCountryId()),
                'shipping_customer_name'  => $order->getShippingAddress()->getFirstName() . ' ' . $order->getShippingAddress()->getLastName(),
                'billing_customer_name'  => $order->getBillingAddress()->getFirstName() . ' ' . $order->getBillingAddress()->getLastName(),
                'order_increment_id'  => $order->getIncrementId(),
                'payment_method_title'  =>  $nameMethod,
                'shipping_method_title'  => $order->getShippingDescription()
            ];
            $scopeStore = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $storeId = $this->storeManager->getStore()->getStoreId();
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_RECIPIENT, $scopeStore , $storeId)) // this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars($templateVars)
                ->setFrom($sender)
                ->addTo($email)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            return;
        } catch (\Exception $e) {
            $this->inlineTranslation->resume();
            return;
        }
    }
    
    public function getCountryname($countryCode){
        $country = $this->countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    public function getCompanyNameFromCustomer($customerId) {
        if($customerId) {
            try {
                $company = $this->companyManagement->getByCustomerId($customerId);
                return $company->getCompanyName();
            } catch (\Exception $exception) {
                return null;
            }
        }
        return null;
    }
}