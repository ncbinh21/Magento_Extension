<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 10:29
 */

namespace Forix\Payment\Model\Service\Sage100;
use Forix\Payment\Model\Service\ServiceInterface;
/**
 * Interface Sage100Interface
 * @package Forix\Payment\Model\Service\Sage100
 */
interface Sage100Interface extends ServiceInterface
{
    /**
     * Retrieve general information about the web services contracts.
     * @return ContractInformation
     */
    public function getContractInformation();

    /**
     * Retrieve statistical data that may aid in diagnosing performance issues, etc..
     *
     * @return DiagnosticInformation
     */
    public function getDiagnosticInformation();

    /**
     * Retrieve the requested sales order
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $salesOrderNo The number of the sales order to retrieve
     * @return SalesOrder
     */
    public function getSalesOrder(Logon $logon, $companyCode, $salesOrderNo);

    /**
     * Retrieve the next sales order number
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @return string
     */
    public function getNextSalesOrderNo(Logon $logon, $companyCode);

    /**
     * Retrieve an empty sales order with the default customer information set
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @return SalesOrder
     */
    public function getSalesOrderTemplate(Logon $logon, $companyCode, $arDivisionNo, $customerNo);

    /**
     * Retrieve a sales order that includes the default values and totals that would be committed if
     * the submitted sales order was passed to the CreateSalesOrder operation
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param SalesOrder $salesOrder
     * @return SalesOrder
     */
    public function previewSalesOrder(Logon $logon, $companyCode, SalesOrder $salesOrder);

    /**
     * Create a sales order in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param SalesOrder $salesOrder
     * @return void
     */
    public function createSalesOrder(Logon $logon, $companyCode, SalesOrder $salesOrder);

    /**
     * Update a sales order in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param SalesOrder $salesOrder
     * @return void
     */
    public function updateSalesOrder(Logon $logon, $companyCode, SalesOrder $salesOrder);

    /**
     * Delete the specified sales order from the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $salesOrderNo
     *
     * @return void
     */
    public function deleteSalesOrder(Logon $logon, $companyCode, $salesOrderNo);

    /**
     * Retrieve the requested customer
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @return Customer
     */
    public function getCustomer(Logon $logon, $companyCode, $arDivisionNo, $customerNo);

    /**
     * Retrieve the next customer number
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @return string
     */
    public function getNextCustomerNo(Logon $logon, $companyCode);

    /**
     * Create a customer in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param Customer $customer
     * @return void
     */
    public function createCustomer(Logon $logon, $companyCode, Customer $customer);

    /**
     * Update a customer in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param Customer $customer
     * @return void
     */
    public function updateCustomer(Logon $logon, $companyCode, Customer $customer);

    /**
     * Delete a customer in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @return void
     */
    public function deleteCustomer(Logon $logon, $companyCode, $arDivisionNo, $customerNo);

    /**
     * Retrieve the requested customer contact
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @param string $contactCode
     * @return CustomerContact
     */
    public function getCustomerContact(Logon $logon, $companyCode, $arDivisionNo, $customerNo, $contactCode);

    /**
     * Create a customer contact in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param CustomerContact $customerContact
     * @return void
     */
    public function createCustomerContact(Logon $logon, $companyCode, CustomerContact $customerContact);

    /**
     * Update a customer contact in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param CustomerContact $customerContact
     * @return void
     */
    public function updateCustomerContact(Logon $logon, $companyCode, CustomerContact $customerContact);

    /**
     * Delete a customer contact in the Sage 100 system
     *
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $arDivisionNo The division number of the customer
     * @param string $customerNo The customer number to use for the template
     * @param string $contactCode
     * @return void
     */
    public function deleteCustomerContact(Logon $logon, $companyCode, $arDivisionNo, $customerNo, $contactCode);

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
     * @return string //A GUID that can be used in future calls to PreAuthorizeCreditCard
     */
    public function addCreditCardToVault(Logon $logon, $companyCode, $paymentType, $creditCardData);

    /**
     * @param Logon $logon The company from which to obtain the sales order
     * @param string $companyCode The company from which to obtain the sales order
     * @param string $paymentType The payment type used for the credit card
     * @param PreAuthorizationData $preAuthorizationData A contract that contains the credit card information necessary to process a pre-authorization
     * @return PreAuthorizationResult
     */
    public function preAuthorizeCreditCard(Logon $logon, $companyCode, $paymentType, $preAuthorizationData);
}