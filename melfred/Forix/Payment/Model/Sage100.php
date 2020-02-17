<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 21/11/2018
 * Time: 14:17
 */

namespace Forix\Payment\Model;

use \Forix\Payment\Model\Service\Sage100\Logon;
use \Forix\Payment\Model\Service\ConverterAdapterInterface;
use \Magento\Framework\Exception\LocalizedException;

class Sage100
{

    protected $_sage100PaymentService;
    protected $_serviceAdapter;
    protected $_scopeConfig;
    protected $_logger;
    protected $_logon = null;
    protected $_companyCode = null;
    protected $_converterAdapter;
    protected $_orderFactory;
    protected $_customerFactory;
    protected $_storeId;
    protected $_encryptor;
    /**
     * @var string
     */
    protected $_code = \Forix\Payment\Model\Sage100Payment::CODE;

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param $storeId
     */
    public function setStore($storeId)
    {
        $this->_storeId = $storeId;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|\Magento\Store\Model\Store $storeId
     *
     * @return mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStoreId();
        }
        $path = 'payment/' . $this->getCode() . '/' . $field;
        return $this->_scopeConfig->getValue($path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function __construct(
        \Magento\Payment\Model\Method\Logger $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        ConverterAdapterInterface $converterAdapter,
        ServiceAdapterInterface $serviceAdapter,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        Service\Sage100PaymentService $sage100PaymentService,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
    )
    {
        $this->_sage100PaymentService = $sage100PaymentService;
        $this->_scopeConfig = $scopeConfig;
        //$this->_logger = $logger;

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/sage100-log.log');
        $this->_logger = new \Zend\Log\Logger();
        $this->_logger->addWriter($writer);

        $this->_orderFactory = $orderFactory;
        $this->_customerFactory = $customerFactory;
        $this->_converterAdapter = $converterAdapter;
        $this->_encryptor = $encryptor;
        $this->_sage100PaymentService->setAdapter($serviceAdapter);
    }

    public function getSalePersonNo()
    {
        return $this->getConfigData('salesperson_no');
    }

    public function getArDivisionNo()
    {
        return $this->getConfigData('division_no');
    }

    protected function getLogon()
    {

        if (null === $this->_logon) {
            $pass = $this->getConfigData('password');
            $this->_logon = new Logon($this->getConfigData('username'), $pass);
        }
        return $this->_logon;
    }

    protected function getCompanyCode()
    {
        if (null === $this->_companyCode) {
            $this->_companyCode = $this->getConfigData('company_code');
        }
        return $this->_companyCode;
    }

    /**
     * @return Service\Sage100\ContractInformation | bool
     */
    public function getContractInformation()
    {
        return $this->_sage100PaymentService->getContractInformation();
    }

    /**
     * // @throws LocalizedException
     * @return Service\Sage100\DiagnosticInformation | bool
     */
    public function getDiagnosticInformation()
    {
        /**
         * SoapFault: 'AllowRemoteDiagnostics' must be set to 'True' to access this method remotely.
         */
        $result = $this->_sage100PaymentService->getDiagnosticInformation();
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'getDiagnosticInformation'
            ]);
            //throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * @param Logon $logon
     * @param $companyCode
     * @param $salesOrderNo
     * // @throws LocalizedException
     * @return \Magento\Sales\Model\Order | bool
     */
    public function getSalesOrder($salesOrderNo)
    {
        if ($this->_companyCode) {
            $salesOrder = $this->_sage100PaymentService->getSalesOrder($this->getLogon(), $this->getCompanyCode(), $salesOrderNo);
            if ($this->_sage100PaymentService->getAdapter()->hasError()) {
                $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                    'method' => 'getSalesOrder',
                    'data' => $salesOrderNo
                ]);
                //throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
                return false;
            } else {
                $magentoOrder = $this->_orderFactory->create();
                $this->_converterAdapter->convert($salesOrder, $magentoOrder);
                return $magentoOrder;
            }
        }
        return false;
    }

    /**
     * Retrieve the next sales order number
     *
     * // @throws LocalizedException
     * @return string | bool
     */
    public function getNextSalesOrderNo()
    {
        /**
         * SoapFault: Logon failure: The user logon or password does not match or the user logon is not enabled for web services.
         */
        $nextOrderNo = $this->_sage100PaymentService->getNextSalesOrderNo($this->getLogon(), $this->getCompanyCode());
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'getNextSalesOrderNo'
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $nextOrderNo->GetNextSalesOrderNoResult;
    }

    /**
     * Retrieve an empty sales order with the default customer information set
     *
     * @param $customerNo
     * // @throws LocalizedException
     * @return Service\Sage100\SalesOrder | bool
     */
    public function getSalesOrderTemplate($customerNo)
    {
        $arDivisionNo = $this->getArDivisionNo();
        $salesOrderTemplate = $this->_sage100PaymentService->getSalesOrderTemplate($this->getLogon(), $this->getCompanyCode(), $arDivisionNo, $customerNo);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'getSalesOrderTemplate',
                'data' => [
                    'arDivisionNo' => $arDivisionNo,
                    'customerNo' => $customerNo
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $salesOrderTemplate;
    }

    /**
     * Retrieve a sales order that includes the default values and totals that would be committed if
     * the submitted sales order was passed to the CreateSalesOrder operation
     * @param $salesOrder
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function previewSalesOrder($salesOrder)
    {
        /**
         * @var $sage100Order Service\Sage100\SalesOrder
         */
        $sage100Order = $this->_converterAdapter->convert($salesOrder, new Service\Sage100\SalesOrder());

        $result = $this->_sage100PaymentService->previewSalesOrder($this->getLogon(), $this->getCompanyCode(), $sage100Order);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'previewSalesOrder',
                'data' => [
                    'saleOrder' => $salesOrder
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * Create a sales order in the Sage 100 system
     *
     * @param Service\Sage100\SalesOrder|\Magento\Sales\Model\Order $salesOrder
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function createSalesOrder($salesOrder)
    {
        if (!($salesOrder instanceof Service\Sage100\SalesOrder)) {
            $orderData = new Service\Sage100\SalesOrder();
            /*if($customer = $salesOrder->getCustomer())
            {
                $customerNo = $customer->getCustomerNo();
                $orderData = $this->getSalesOrderTemplate($customerNo);
            }*/
            $salesOrder->setArDivisionNo($this->getArDivisionNo());
            $salesOrder->setSalePersonNo($this->getSalePersonNo());
            $salesOrder = $this->_converterAdapter->convert($salesOrder, $orderData);
        }
        $result = $this->_sage100PaymentService->createSalesOrder($this->getLogon(), $this->getCompanyCode(), $salesOrder);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'createSalesOrder',
                'data' => [
                    'saleOrder' => $salesOrder
                ]
            ]);
            throw new LocalizedException(__('We can not create order due to: %1',implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * Update a sales order in the Sage 100 system
     * @param $salesOrder Service\Sage100\SalesOrder|\Magento\Sales\Model\Order
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function updateSalesOrder($salesOrder)
    {
        if (!($salesOrder instanceof Service\Sage100\SalesOrder)) {
            $salesOrder = $this->_converterAdapter->convert($salesOrder, new Service\Sage100\SalesOrder());
        }
        $result = $this->_sage100PaymentService->updateSalesOrder($this->getLogon(), $this->getCompanyCode(), $salesOrder);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'updateSalesOrder',
                'data' => [
                    'saleOrder' => $salesOrder
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * Delete the specified sales order from the Sage 100 system
     * @param $salesOrderNo
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function deleteSalesOrder($salesOrderNo)
    {
        $result = $this->_sage100PaymentService->updateSalesOrder($this->getLogon(), $this->getCompanyCode(), $salesOrderNo);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'updateSalesOrder',
                'data' => [
                    'salesOrderNo' => $salesOrderNo
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return true;
    }

    /**
     * Retrieve the requested customer
     *
     * @param string $customerNo The customer number to use for the template
     * // @throws LocalizedException
     * @return \Magento\Customer\Model\Customer | bool
     */
    public function getCustomer($customerNo)
    {
        $arDivisionNo = $this->getArDivisionNo();
        $sageCustomer = $this->_sage100PaymentService->getCustomer($this->getLogon(), $this->getCompanyCode(), $arDivisionNo, $customerNo);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'getCustomer',
                'data' => [
                    'customerNo' => $customerNo,
                    'arDivisionNo' => $arDivisionNo
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        $mageCustomer = $this->_customerFactory->create();
        $this->_converterAdapter->convert($sageCustomer->GetCustomerResult, $mageCustomer);
        $mageCustomer->setArDivisionNo($this->getArDivisionNo());
        $mageCustomer->setSalePersonNo($this->getSalePersonNo());
        return $mageCustomer;
    }

    /**
     * Retrieve the next customer number
     * @return string
     * // @throws LocalizedException
     */
    public function getNextCustomerNo()
    {
        $result = $this->_sage100PaymentService->getNextCustomerNo($this->getLogon(), $this->getCompanyCode());
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'getNextCustomerNo'
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result->GetNextCustomerNoResult;
    }

    /**
     * Create a customer in the Sage 100 system
     * @param $customer \Magento\Customer\Model\Customer|Service\Sage100\Customer
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function createCustomer($customer)
    {
        $temp = $customer;
        if (!($customer instanceof Service\Sage100\Customer)) {
            $customer->setArDivisionNo($this->getArDivisionNo());
            $customer->setSalePersonNo($this->getSalePersonNo());
            $customer = $this->_converterAdapter->convert($customer, new Service\Sage100\Customer());
        }
        $result = $this->_sage100PaymentService->createCustomer($this->getLogon(), $this->getCompanyCode(), $customer);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'createCustomer',
                'data' => [
                    'customer' => $customer
                ]
            ]);
            $temp->setNoAddress(true);
            if (!($temp instanceof Service\Sage100\Customer)) {
                $temp->setArDivisionNo($this->getArDivisionNo());
                $temp->setSalePersonNo($this->getSalePersonNo());
                $temp = $this->_converterAdapter->convert($temp, new Service\Sage100\Customer());
            }
            $result = $this->_sage100PaymentService->createCustomer($this->getLogon(), $this->getCompanyCode(), $temp);
            if ($this->_sage100PaymentService->getAdapter()->hasError()) {
                $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                    'method' => 'createCustomer',
                    'data' => [
                        'customer' => $temp
                    ]
                ]);
                return false;
            }
        }
        return $result;
    }

    /**
     * Update a customer in the Sage 100 system
     *
     * @param $customer \Magento\Customer\Model\Customer|Service\Sage100\Customer
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function updateCustomer($customer)
    {
        $temp = $customer;
        if (!($customer instanceof Service\Sage100\Customer)) {
            $customer->setArDivisionNo($this->getArDivisionNo());
//            $customer->setSalePersonNo($this->getSalePersonNo()); // MB-1334
            $customer = $this->_converterAdapter->convert($customer, new Service\Sage100\Customer());
        }
        $result = $this->_sage100PaymentService->updateCustomer($this->getLogon(), $this->getCompanyCode(), $customer);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'updateCustomer',
                'data' => [
                    'customer' => $customer
                ]
            ]);
            $temp->setNoAddress(true);
            if (!($temp instanceof Service\Sage100\Customer)) {
                $temp->setArDivisionNo($this->getArDivisionNo());
//                $temp->setSalePersonNo($this->getSalePersonNo()); // MB-1334
                $temp = $this->_converterAdapter->convert($temp, new Service\Sage100\Customer());
            }
            $result = $this->_sage100PaymentService->updateCustomer($this->getLogon(), $this->getCompanyCode(), $temp);
            if ($this->_sage100PaymentService->getAdapter()->hasError()) {
                $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                    'method' => 'updateCustomer',
                    'data' => [
                        'customer' => $temp
                    ]
                ]);
                return false;
            }
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
        }
        return $result;
    }

    /**
     * Delete a customer in the Sage 100 system
     *
     * @param string $customerNo The customer number to use for the template
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function deleteCustomer($customerNo)
    {
        $arDivisionNo = $this->getArDivisionNo();
        $result = $this->_sage100PaymentService->deleteCustomer($this->getLogon(), $this->getCompanyCode(), $arDivisionNo, $customerNo);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'deleteCustomer',
                'data' => [
                    'arDivisionNo' => $arDivisionNo,
                    'customer' => $customerNo
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return true;
    }

    /**
     * Retrieve the requested customer contact
     *
     * @param string $customerNo The customer number to use for the template
     * @param string $contactCode
     * // @throws LocalizedException
     * @return Service\Sage100\CustomerContact | bool
     */
    public function getCustomerContact($customerNo, $contactCode)
    {
        $arDivisionNo = $this->getArDivisionNo();
        $result = $this->_sage100PaymentService->getCustomerContact($this->getLogon(), $this->getCompanyCode(), $arDivisionNo, $customerNo, $contactCode);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'getCustomerContact',
                'data' => [
                    'arDivisionNo' => $arDivisionNo,
                    'customer' => $customerNo,
                    'contactCode' => $contactCode
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * Create a customer contact in the Sage 100 system
     *
     * @param Service\Sage100\CustomerContact $customerContact
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function createCustomerContact($customerContact)
    {
        if (!($customerContact instanceof Service\Sage100\CustomerContact)) {
            $customerContact->setArDivisionNo($this->getArDivisionNo());
            $customerContact->setSalePersonNo($this->getSalePersonNo());
            $customerContact = $this->_converterAdapter->convert($customerContact, new Service\Sage100\CustomerContact());
        }
        $result = $this->_sage100PaymentService->createCustomerContact($this->getLogon(), $this->getCompanyCode(), $customerContact);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'createCustomerContact',
                'data' => [
                    'customerContact' => $customerContact
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * Update a customer contact in the Sage 100 system
     *
     * @param Service\Sage100\CustomerContact $customerContact
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function updateCustomerContact($customerContact)
    {
        if (!($customerContact instanceof Service\Sage100\CustomerContact)) {
            $customerContact->setArDivisionNo($this->getArDivisionNo());
            $customerContact->setSalePersonNo($this->getSalePersonNo());
            $customerContact = $this->_converterAdapter->convert($customerContact, new Service\Sage100\CustomerContact());
        }
        $result = $this->_sage100PaymentService->updateCustomerContact($this->getLogon(), $this->getCompanyCode(), $customerContact);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'updateCustomerContact',
                'data' => [
                    'customerContact' => $customerContact
                ]
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result;
    }

    /**
     * Delete a customer contact in the Sage 100 system
     *
     * @param string $customerNo The customer number to use for the template
     * @param string $contactCode
     * // @throws LocalizedException
     * @return mixed | bool
     */
    public function deleteCustomerContact($customerNo, $contactCode)
    {

        $arDivisionNo = $this->getArDivisionNo();
        $result = $this->_sage100PaymentService->deleteCustomerContact($this->getLogon(), $this->getCompanyCode(), $arDivisionNo, $customerNo, $contactCode);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'deleteCustomerContact',
                'data' => [
                    'arDivisionNo' => $arDivisionNo,
                    'customer' => $customerNo,
                    'contactCode' => $contactCode
                ],
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return true;
    }

    /**
     * Add a credit card number and expiration date to the Sage Exchange
     * secure vault and returns a unique identifier that can be used in future calls to
     * PreAuthorizeCreditCard and is also placed in the SalesOrder contract for various
     * sales order operations
     *
     * @param string $paymentType The payment type used for the credit card
     * @param string $creditCardData The credit card number and expiration date formatted as follows: nnnnnnnnnnnnnnnn|mmyy
     * // @throws LocalizedException
     * @return string | bool //A GUID that can be used in future calls to PreAuthorizeCreditCard
     */
    public function addCreditCardToVault($paymentType, $creditCardData)
    {
        $result = $this->_sage100PaymentService->addCreditCardToVault($this->getLogon(), $this->getCompanyCode(), $paymentType, $creditCardData);
        // Don't save $creditCardData data to debug.
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'addCreditCardToVault',
                'data' => [
                    'paymentType' => $paymentType
                ],
            ]);
//            throw new LocalizedException(__(implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result->AddCreditCardToVaultResult;
    }

    /**
     * @see PaymentType
     * @param string $paymentType The payment type used for the credit card
     * @param Service\Sage100\PreAuthorizationData $preAuthorizationData A contract that contains the credit card
     *  information necessary to process a pre-authorization
     *
     * // @throws LocalizedException
     * @return Service\Sage100\PreAuthorizationResult | bool
     */
    public function preAuthorizeCreditCard($paymentType, $preAuthorizationData)
    {
        if (!($preAuthorizationData instanceof Service\Sage100\PreAuthorizationData)) {
            $preAuthorizationData = $this->_converterAdapter->convert($preAuthorizationData, new Service\Sage100\PreAuthorizationData());
        }
        $result = $this->_sage100PaymentService->preAuthorizeCreditCard($this->getLogon(), $this->getCompanyCode(), $paymentType, $preAuthorizationData);
        if ($this->_sage100PaymentService->getAdapter()->hasError()) {
            $this->_logger->debug($this->_sage100PaymentService->getAdapter()->getMessages(), [
                'method' => 'addCreditCardToVault',
                'data' => [
                    'paymentType' => $paymentType,
                    'preAuthorizationData' => $preAuthorizationData
                ],
            ]);
            throw new LocalizedException(__('We can not authorize your credit card due to: %1',implode(", ", $this->_sage100PaymentService->getAdapter()->getMessages())));
            return false;
        }
        return $result->PreAuthorizeCreditCardResult;
    }
}