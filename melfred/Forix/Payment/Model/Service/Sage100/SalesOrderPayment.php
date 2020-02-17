<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 19/11/2018
 * Time: 16:42
 */

namespace Forix\Payment\Model\Service\Sage100;


use Forix\Payment\Model\Service\ConverterInterface;

class SalesOrderPayment extends AbstractModel implements ConverterInterface
{
    public $AVSAddressLine1;
    public $AVSAddressLine2;
    public $AVSCity;
    public $AVSCountryCode;
    public $AVSState;
    public $AVSZipCode;
    public $AuthorizationDateTime;
    public $BankAccountType;
    public $BankName;
    public $CardType;
    public $CardholderName;
    public $CorporateCustIDPurchOrder;
    public $CorporateSalesTax;
    public $CorporateTaxOverrd;
    public $CreditCardAuthorizationNo;
    public $CreditCardComment;
    public $CreditCardEmailAddress;
    public $CreditCardGUID;
    public $CreditCardID;
    public $CreditCardTrackingID;
    public $CreditCardTransactionID;
    public $DateTimeCreated;
    public $DateTimeUpdated;
    public $ExpirationDateMonth;
    public $ExpirationDateYear;
    public $Last4BankAccountNos;
    public $Last4BankRoutingNos;
    public $Last4UnencryptedCreditCardNos;
    public $PaymentSeqNo;
    public $PaymentType;
    public $PaymentTypeCategory = 'P';
    public $SalesOrderNo;
    public $SaveCreditCard;
    public $TransactionAmt;
    public $UserCreatedKey;
    public $UserUpdatedKey;

    /**
     * @param \Magento\Framework\DataObject $destination
     * @return \Magento\Framework\DataObject
     */
    public function convertTo(\Magento\Framework\DataObject $destination)
    {
    	//Need Create new Attribute ----------------------
		$destination->setAVSAddressLine1($this->AVSAddressLine1);
		$destination->setAVSAddressLine2($this->AVSAddressLine2);
		$destination->setAVSCity($this->AVSCity);
		$destination->setAVSCountryCode($this->AVSCountryCode);
		$destination->setAVSState($this->AVSState);
		$destination->setAVSZipCode($this->AVSZipCode);
		$destination->setAuthorizationDateTime($this->AuthorizationDateTime);
		$destination->setBankAccountType($this->BankAccountType);
		$destination->setBankName($this->BankName);
		$destination->setCardType($this->CardType);
		$destination->setCardholderName($this->CardholderName);
		$destination->setCorporateCustIDPurchOrder($this->CorporateCustIDPurchOrder);
		$destination->setCorporateSalesTax($this->CorporateSalesTax);
		$destination->setCorporateTaxOverrd($this->CorporateTaxOverrd);
		$destination->setCreditCardAuthorizationNo($this->CreditCardAuthorizationNo);
		$destination->setCreditCardComment($this->CreditCardComment);
		$destination->setCreditCardEmailAddress($this->CreditCardEmailAddress);
		$destination->setCreditCardGUID($this->CreditCardGUID);
		$destination->setCreditCardID($this->CreditCardID);
		$destination->setCreditCardTrackingID($this->CreditCardTrackingID);
		$destination->setCreditCardTransactionID($this->CreditCardTransactionID);
		$destination->setDateTimeCreated($this->DateTimeCreated);
		$destination->setDateTimeUpdated($this->DateTimeUpdated);
		$destination->setExpirationDateMonth($this->ExpirationDateMonth);
		$destination->setExpirationDateYear($this->ExpirationDateYear);
		$destination->setLast4BankAccountNos($this->Last4BankAccountNos);
		$destination->setLast4BankRoutingNos($this->Last4BankRoutingNos);
		$destination->setLast4UnencryptedCreditCardNos($this->Last4UnencryptedCreditCardNos);
		$destination->setPaymentSeqNo($this->PaymentSeqNo);
		$destination->setPaymentType($this->PaymentType);
		$destination->setPaymentTypeCategory($this->PaymentTypeCategory);
		$destination->setSalesOrderNo($this->SalesOrderNo);
		$destination->setSaveCreditCard($this->SaveCreditCard);
		$destination->setTransactionAmt($this->TransactionAmt);
		$destination->setUserCreatedKey($this->UserCreatedKey);
		$destination->setUserUpdatedKey($this->UserUpdatedKey);

    }

    /**
     * @param \Magento\Framework\DataObject $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/mylogfile.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        /**
         * @var $source \Magento\Sales\Model\Order\Payment
         */
        $instaninfomation = $source->getAdditionalInformation();
        $billingAddress = $source->getOrder()->getBillingAddress();

        /**
         *  'approval_indicator' , $result->ApprovalIndicator;
         *  'authorization_date_time' , $result->AuthorizationDateTime;
         *  'credit_card_authorization_no' , $result->CreditCardAuthorizationNo;
         *  'credit_card_transaction_id' , $result->CreditCardTransactionID;
         *  'credit_card_message' , $result->Message;
         */
        // TODO: Implement convertFrom() method.
	    $this->AVSAddressLine1 = $billingAddress->getStreetLine(1);
		$this->AVSAddressLine2 = $billingAddress->getStreetLine(2);
		$this->AVSCity = $billingAddress->getCity();
		$this->AVSCountryCode =  $billingAddress->getCountryId();
		$this->AVSState =   $billingAddress->getRegionCode(); // AVSState$: The AVS State is greater than 2 character(s).
        $this->AVSZipCode = $billingAddress->getPostcode();
        $this->SalesOrderNo = $source->getOrder()->getSalesOrderNo();

        $this->CreditCardGUID = $source->getAdditionalInformation('CreditCardGUID');
        // CreditCardGUID$: The Accounts Receivable division number and customer number is required. in
        $this->AuthorizationDateTime =  $source->getAdditionalInformation('authorization_date_time');
        $this->CreditCardTransactionID = $source->getAdditionalInformation('credit_card_transaction_id');
        $this->CreditCardComment =  $source->getAdditionalInformation('credit_card_message');
        $this->CreditCardAuthorizationNo = $source->getAdditionalInformation('credit_card_authorization_no');
        $this->Last4UnencryptedCreditCardNos = $source->getCcLast4(); // This field can only be set when entering a credit card transaction.


        $this->ExpirationDateMonth =  $source->getCcExpMonth();
        $this->ExpirationDateYear =  $source->getCcExpYear(); // ExpirationDateYear$: This field can only be set when entering a credit card transaction.
        //$this->CardType = $source->getCcType(); // The Credit Card Type is greater than 1 character(s).
        $this->PaymentType = \Forix\Payment\Model\Sage100Payment::PAYMENT_TYPE;

        $this->CardholderName = trim($billingAddress->getFirstname())    . " " . trim($billingAddress->getLastname());
        $this->BankName = $source->getBankName();
        $this->BankAccountType = $source->getBankAccountType();

        //$this->CorporateCustIDPurchOrder = $source->getCorporateCustIDPurchOrder();
        //$this->CorporateSalesTax = $source->getCorporateSalesTax();
        //$this->CorporateTaxOverrd = $source->getCorporateTaxOverrd();


        $this->CreditCardEmailAddress = $source->getCreditCardEmailAddress();


        $this->CreditCardID = $source->getCreditCardID();
        $this->CreditCardTrackingID = $source->getCreditCardTrackingID();

        //$this->DateTimeCreated = $source->getDateTimeCreated();
        //$this->DateTimeUpdated = $source->getDateTimeUpdated();


        $this->Last4BankAccountNos = $source->getLast4BankAccountNos();
        $this->Last4BankRoutingNos = $source->getLast4BankRoutingNos();


        //$this->PaymentSeqNo = $source->getPaymentSeqNo();
//        $this->PaymentTypeCategory = 'P';
		$this->SaveCreditCard = 'N';
		$this->TransactionAmt = $source->getAmountOrdered();
		//$this->UserCreatedKey = $source->getUserCreatedKey();
		//$this->UserUpdatedKey = $source->getUserUpdatedKey();
		return $this;
    }

}
