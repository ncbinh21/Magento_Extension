<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 15:21
 */

namespace Forix\Payment\Model\Service;

use Forix\Payment\Model\Service\Sage100\ContractInformation;
use Forix\Payment\Model\Service\Sage100\Customer;
use Forix\Payment\Model\Service\Sage100\CustomerContact;
use Forix\Payment\Model\Service\Sage100\DiagnosticInformation;
use Forix\Payment\Model\Service\Sage100\Logon;
use Forix\Payment\Model\Service\Sage100\PreAuthorizationData;
use Forix\Payment\Model\Service\Sage100\PreAuthorizationResult;
use Forix\Payment\Model\Service\Sage100\SalesOrder;
use Forix\Payment\Model\Service\Sage100\Sage100Interface;
use Forix\Payment\Model\ServiceAdapterInterface;
use Forix\Payment\Model\Context;

class Sage100PaymentService implements Sage100Interface
{
    /**
     * @var ServiceAdapterInterface
     */
    protected $_serviceAdapter;


    /**
     * @return ServiceAdapterInterface
     */
    public function getAdapter()
    {
        return $this->_serviceAdapter;
    }

    /**
     * @param ServiceAdapterInterface $adapter
     */
    public function setAdapter(ServiceAdapterInterface $adapter)
    {
        $this->_serviceAdapter = $adapter;
    }
    /**
     * Retrieve general information about the web services contracts.
     * @return ContractInformation | bool
     */
    public function getContractInformation()
    {
        return $this->getAdapter()->getContractInformation([]);
    }

    /**
     * Retrieve statistical data that may aid in diagnosing performance issues, etc..
     *
     * @return DiagnosticInformation | bool
     */
    public function getDiagnosticInformation()
    {
        return $this->getAdapter()->getDiagnosticInformation([]);
    }

    /**
     * Retrieve the requested sales order
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $salesOrderNo The number of the sales order to retrieve
     * @return SalesOrder | bool
     */
    public function getSalesOrder(Logon $logon, $companyCode, $salesOrderNo)
    {
        return $this->getAdapter()->getSalesOrder([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'salesOrderNo' => $salesOrderNo
        ]);
    }

    /**
     * Retrieve the next sales order number
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @return string | bool
     */
    public function getNextSalesOrderNo(Logon $logon, $companyCode)
    {
        return $this->getAdapter()->getNextSalesOrderNo([
            'logon' => $logon,
            'companyCode' => $companyCode
        ]);
    }

    /**
     * Retrieve an empty sales order with the default customer information set
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @return SalesOrder
     */
    public function getSalesOrderTemplate(Logon $logon, $companyCode, $arDivisionNo, $customerNo)
    {
        return $this->getAdapter()->getSalesOrderTemplate([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'arDivisionNo' => $arDivisionNo,
            'customerNo' => $customerNo
        ]);
    }

    /**
     * Retrieve a sales order that includes the default values and totals that would be committed if
     * the submitted sales order was passed to the CreateSalesOrder operation
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param SalesOrder $salesOrder
     * @return SalesOrder
     */
    public function previewSalesOrder(Logon $logon, $companyCode, SalesOrder $salesOrder)
    {
        return $this->getAdapter()->previewSalesOrder([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'salesOrder' => $salesOrder
        ]);
    }

    /**
     * Create a sales order in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param SalesOrder $salesOrder
     * @return mixed | bool
     */
    public function createSalesOrder(Logon $logon, $companyCode, SalesOrder $salesOrder)
    {
        return $this->getAdapter()->createSalesOrder([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'salesOrder' => $salesOrder
        ]);
    }

    /**
     * Update a sales order in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param SalesOrder $salesOrder
     * @return mixed | bool
     */
    public function updateSalesOrder(Logon $logon, $companyCode, SalesOrder $salesOrder)
    {
        return $this->getAdapter()->updateSalesOrder([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'salesOrder' => $salesOrder
        ]);
    }

    /**
     * Delete the specified sales order from the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $salesOrderNo
     *
     * @return mixed | bool
     */
    public function deleteSalesOrder(Logon $logon, $companyCode, $salesOrderNo)
    {
        return $this->getAdapter()->deleteSalesOrder([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'salesOrderNo' => $salesOrderNo
        ]);
    }

    /**
     * Retrieve the requested customer
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @return Customer | bool
     */
    public function getCustomer(Logon $logon, $companyCode, $arDivisionNo, $customerNo)
    {
        return $this->getAdapter()->getCustomer([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'arDivisionNo' => $arDivisionNo,
            'customerNo' => $customerNo
        ]);
    }

    /**
     * Retrieve the next customer number
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @return string | bool
     */
    public function getNextCustomerNo(Logon $logon, $companyCode)
    {
        return $this->getAdapter()->getNextCustomerNo([
            'logon' => $logon,
            'companyCode' => $companyCode
        ]);
    }

    /**
     * Create a customer in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param Customer $customer
     * @return mixed | bool
     */
    public function createCustomer(Logon $logon, $companyCode, Customer $customer)
    {
    	return $this->getAdapter()->createCustomer([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'customer' => $customer
        ]);
    }

    /**
     * Update a customer in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param Customer $customer
     * @return mixed | bool
     */
    public function updateCustomer(Logon $logon, $companyCode, Customer $customer)
    {
        return $this->getAdapter()->updateCustomer([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'customer' => $customer
        ]);
    }

    /**
     * Delete a customer in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @return mixed | bool
     */
    public function deleteCustomer(Logon $logon, $companyCode, $arDivisionNo, $customerNo)
    {
        return $this->getAdapter()->deleteCustomer([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'arDivisionNo' => $arDivisionNo,
            'customerNo' => $customerNo
        ]);
    }

    /**
     * Retrieve the requested customer contact
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @param string $contactCode
     * @return CustomerContact | bool
     */
    public function getCustomerContact(Logon $logon, $companyCode, $arDivisionNo, $customerNo, $contactCode)
    {
        return $this->getAdapter()->getCustomerContact([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'arDivisionNo' => $arDivisionNo,
            'customerNo' => $customerNo,
            'contactCode' => $contactCode
        ]);
    }

    /**
     * Create a customer contact in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param CustomerContact $customerContact
     * @return mixed | bool
     */
    public function createCustomerContact(Logon $logon, $companyCode, CustomerContact $customerContact)
    {
        return $this->getAdapter()->createCustomerContact([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'customerContact' => $customerContact
        ]);
    }

    /**
     * Update a customer contact in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param CustomerContact $customerContact
     * @return mixed | bool
     */
    public function updateCustomerContact(Logon $logon, $companyCode, CustomerContact $customerContact)
    {
        return $this->getAdapter()->updateCustomerContact([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'customerContact' => $customerContact
        ]);
    }

    /**
     * Delete a customer contact in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @param string $contactCode
     * @return mixed | bool
     */
    public function deleteCustomerContact(Logon $logon, $companyCode, $arDivisionNo, $customerNo, $contactCode)
    {
        return $this->getAdapter()->deleteCustomerContact([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'arDivisionNo' => $arDivisionNo,
            'customerNo' => $customerNo,
            'contactCode' => $contactCode
        ]);
    }

    /**
     * Add a credit card number and expiration date to the Sage Exchange
     * secure vault and returns a unique identifier that can be used in future calls to
     * PreAuthorizeCreditCard and is also placed in the SalesOrder contract for various
     * sales order operations
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $paymentType The payment type used for the credit card
     * @param string $creditCardData The credit card number and expiration date formatted as follows: nnnnnnnnnnnnnnnn|mmyy
     * @return string | bool //A GUID that can be used in future calls to PreAuthorizeCreditCard
     */
    public function addCreditCardToVault(Logon $logon, $companyCode, $paymentType, $creditCardData)
    {
        return $this->getAdapter()->addCreditCardToVault([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'paymentType' => $paymentType,
            'creditCardData' => $creditCardData
        ]);
    }

    /**
     * @see PaymentType
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $paymentType The payment type used for the credit card
     * @param PreAuthorizationData $preAuthorizationData A contract that contains the credit card information necessary to process a pre-authorization
     * @return PreAuthorizationResult | bool
     */
    public function preAuthorizeCreditCard(Logon $logon, $companyCode, $paymentType, $preAuthorizationData)
    {
        return $this->getAdapter()->preAuthorizeCreditCard([
            'logon' => $logon,
            'companyCode' => $companyCode,
            'paymentType' => $paymentType,
            'preAuthorizationData' => $preAuthorizationData
        ]);
    }
}