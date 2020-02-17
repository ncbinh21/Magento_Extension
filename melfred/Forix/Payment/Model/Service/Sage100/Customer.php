<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 15:47
 */

namespace Forix\Payment\Model\Service\Sage100;

use Forix\Payment\Model\Service\ConverterInterface;


class Customer extends AbstractModel implements ConverterInterface
{
    public $ARDivisionNo = '00';
    public $AddressLine1;
    public $AddressLine2;
    public $AddressLine3;
    public $AgingCategory1;
    public $AgingCategory2;
    public $AgingCategory3;
    public $AgingCategory4;
    public $AvgDaysOverDue;
    public $AvgDaysPaymentInvoice;
    public $BalanceForward;
    public $BatchFax;
    public $City;
    public $Comment;
    public $ContactCode;
    public $CountryCode;
    public $CreditCardGUID;
    public $CreditHold;
    public $CreditLimit;
    public $CurrentBalance;
    public $CustomerDiscountRate;
    public $CustomerName;
    public $CustomerNo;
    public $CustomerStatus;
    public $CustomerType;
    public $DateEstablished;
    public $DateLastActivity;
    public $DateLastAging;
    public $DateLastFinanceChrg;
    public $DateLastPayment;
    public $DateLastStatement;
    public $DateTimeCreated;
    public $DateTimeUpdated;
    public $DefaultCostCode;
    public $DefaultCostType;
    public $DefaultCreditCardPmtType;
    public $DefaultItemCode;
    public $DefaultPaymentType;
    public $EBMConsumerUserID;
    public $EBMEnabled;
    public $EmailAddress;
    public $EmailStatements;
    public $FaxNo;
    public $HighestStmntBalance;
    public $InactiveReasonCode;
    public $LastPaymentAmt;
    public $NumberOfInvToUseInCalc;
    public $OpenItemCustomer;
    public $OpenOrderAmt;
    public $PriceLevel;
    public $PrimaryShipToCode;
    public $PrintDunningMessage;
    public $ResidentialAddress;
    public $RetentionAging1;
    public $RetentionAging2;
    public $RetentionAging3;
    public $RetentionAging4;
    public $RetentionCurrent;
    public $SalesPersonDivisionNo2;
    public $SalesPersonDivisionNo3;
    public $SalesPersonDivisionNo4;
    public $SalesPersonDivisionNo5;
    public $SalesPersonNo2;
    public $SalesPersonNo3;
    public $SalesPersonNo4;
    public $SalesPersonNo5;
    public $SalespersonDivisionNo;
    public $SalespersonNo = "NADS";
    public $ServiceChargeRate;
    public $ShipMethod;
    public $SortField;
    public $SplitCommRate2;
    public $SplitCommRate3;
    public $SplitCommRate4;
    public $SplitCommRate5;
    public $State;
    public $StatementCycle;
    public $TaxExemptNo;
    public $TaxSchedule;
    public $TelephoneExt;
    public $TelephoneNo;
    public $TemporaryCustomer;
    public $TermsCode;
    public $URLAddress;
    public $UnpaidServiceChrg;
    public $UserCreatedKey;
    public $UserUpdatedKey;
    public $ZipCode;
    public $addressRepository;

    /**
     * @param \Magento\Framework\DataObject $destination
     * @return \Magento\Customer\Model\Customer
     */
    public function convertTo(\Magento\Framework\DataObject $destination)
    {

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var $destination \Magento\Customer\Model\Customer
         */
        //Seem magento field -----------------------------
        $destination->setEmail($this->EmailAddress);
        //Seem magento field -----------------------------

        //Need Create new Attribute ----------------------
        $destination->setARDivisionNo($this->ARDivisionNo);
        $destination->setCustomerNo($this->CustomerNo);
        $destination->setCustomerStatus($this->CustomerStatus); //Active|InActive|Temporary
        $destination->setCustomerType($this->CustomerType);
        //Need Create new Attribute ----------------------

        //$destination->setFaxNo($this->FaxNo);
        $destination->setAgingCategory1($this->AgingCategory1);
        $destination->setAgingCategory2($this->AgingCategory2);
        $destination->setAgingCategory3($this->AgingCategory3);
        $destination->setAgingCategory4($this->AgingCategory4);
        $destination->setAvgDaysOverDue($this->AvgDaysOverDue);
        $destination->setAvgDaysPaymentInvoice($this->AvgDaysPaymentInvoice);
        $destination->setBalanceForward($this->BalanceForward);
        $destination->setBatchFax($this->BatchFax);
        $destination->setComment($this->Comment);
        $destination->setContactCode($this->ContactCode);
        $destination->setCreditCardGUID($this->CreditCardGUID);
        $destination->setCreditHold($this->CreditHold);
        $destination->setCreditLimit($this->CreditLimit);
        $destination->setCurrentBalance($this->CurrentBalance);
        $destination->setCustomerDiscountRate($this->CustomerDiscountRate);
        $destination->setDateEstablished($this->DateEstablished);
        $destination->setDateLastActivity($this->DateLastActivity);
        $destination->setDateLastAging($this->DateLastAging);
        $destination->setDateLastFinanceChrg($this->DateLastFinanceChrg);
        $destination->setDateLastPayment($this->DateLastPayment);
        $destination->setDateLastStatement($this->DateLastStatement);
        $destination->setDateTimeCreated($this->DateTimeCreated);
        $destination->setDateTimeUpdated($this->DateTimeUpdated);
        $destination->setDefaultCostCode($this->DefaultCostCode);
        $destination->setDefaultCostType($this->DefaultCostType);
        $destination->setDefaultCreditCardPmtType($this->DefaultCreditCardPmtType);
        $destination->setDefaultItemCode($this->DefaultItemCode);
        $destination->setDefaultPaymentType($this->DefaultPaymentType);
        $destination->setEBMConsumerUserID($this->EBMConsumerUserID);
        $destination->setEBMEnabled($this->EBMEnabled);
        $destination->setEmailStatements($this->EmailStatements);
        $destination->setHighestStmntBalance($this->HighestStmntBalance);
        $destination->setInactiveReasonCode($this->InactiveReasonCode);
        $destination->setLastPaymentAmt($this->LastPaymentAmt);
        $destination->setNumberOfInvToUseInCalc($this->NumberOfInvToUseInCalc);
        $destination->setOpenItemCustomer($this->OpenItemCustomer);
        $destination->setOpenOrderAmt($this->OpenOrderAmt);
        $destination->setPriceLevel($this->PriceLevel);
        $destination->setPrimaryShipToCode($this->PrimaryShipToCode);
        $destination->setPrintDunningMessage($this->PrintDunningMessage);
        $destination->setResidentialAddress($this->ResidentialAddress);
        $destination->setRetentionAging1($this->RetentionAging1);
        $destination->setRetentionAging2($this->RetentionAging2);
        $destination->setRetentionAging3($this->RetentionAging3);
        $destination->setRetentionAging4($this->RetentionAging4);
        $destination->setRetentionCurrent($this->RetentionCurrent);
        $destination->setSalesPersonDivisionNo2($this->SalesPersonDivisionNo2);
        $destination->setSalesPersonDivisionNo3($this->SalesPersonDivisionNo3);
        $destination->setSalesPersonDivisionNo4($this->SalesPersonDivisionNo4);
        $destination->setSalesPersonDivisionNo5($this->SalesPersonDivisionNo5);
        $destination->setSalesPersonNo2($this->SalesPersonNo2);
        $destination->setSalesPersonNo3($this->SalesPersonNo3);
        $destination->setSalesPersonNo4($this->SalesPersonNo4);
        $destination->setSalesPersonNo5($this->SalesPersonNo5);
        $destination->setServiceChargeRate($this->ServiceChargeRate);
        $destination->setShipMethod($this->ShipMethod);
        $destination->setSortField($this->SortField);
        $destination->setSplitCommRate2($this->SplitCommRate2);
        $destination->setSplitCommRate3($this->SplitCommRate3);
        $destination->setSplitCommRate4($this->SplitCommRate4);
        $destination->setSplitCommRate5($this->SplitCommRate5);
        $destination->setStatementCycle($this->StatementCycle);
        $destination->setTaxExemptNo($this->TaxExemptNo);
        $destination->setTaxSchedule($this->TaxSchedule);
        $destination->setTelephoneExt($this->TelephoneExt);
        $destination->setTelephoneNo($this->TelephoneNo);
        $destination->setTemporaryCustomer($this->TemporaryCustomer);
        $destination->setTermsCode($this->TermsCode);
        $destination->setURLAddress($this->URLAddress);
        $destination->setUnpaidServiceChrg($this->UnpaidServiceChrg);
        $destination->setUserCreatedKey($this->UserCreatedKey);
        $destination->setUserUpdatedKey($this->UserUpdatedKey);

        // ----------------------- Address fields
        $address = $objectManager->create(\Magento\Customer\Model\Address::class);
        //$destination->setCustomerName($this->CustomerName); //Maybe missing last name or first name
        $name = explode(' ', $this->CustomerName);
        $address
            ->setFirstname($name[0])
            ->setLastname(isset($name[1]) ? $name[1] : "_")
            ->setCountryCode($this->CountryCode)// Need create object
            ->setPostcode($this->ZipCode)
            ->setCity($this->City)//Need verify data
            ->setTelephone($this->TelephoneNo)
            ->setRegion($this->State)// Need confirm
            ->setFax($this->FaxNo)
            ->setStreet([
                $this->AddressLine1,
                $this->AddressLine2,
                $this->AddressLine3
            ]);
        //->setCountryId($this->CountryCode) // Need create object
        $destination->addAddress($address);

        return $destination;
    }

    /**
     * @param \Magento\Framework\DataObject $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source)
    {
    	/**
         * @var $source \Magento\Customer\Model\Customer
         */
        $this->AgingCategory1 = $source->getAgingCategory1();
        $this->AgingCategory2 = $source->getAgingCategory2();
        $this->AgingCategory3 = $source->getAgingCategory3();
        $this->AgingCategory4 = $source->getAgingCategory4();
        $this->AvgDaysOverDue = $source->getAvgDaysOverDue();
        $this->AvgDaysPaymentInvoice = $source->getAvgDaysPaymentInvoice();
        $this->BalanceForward = $source->getBalanceForward();
        $this->BatchFax = $source->getBatchFax();
        $this->Comment = $source->getComment();
        $this->CreditCardGUID = $source->getCreditCardGUID();
        $this->CreditHold = $source->getCreditHold();
        $this->CreditLimit = $source->getCreditLimit();
        $this->CurrentBalance = $source->getCurrentBalance();
        $this->CustomerDiscountRate = $source->getCustomerDiscountRate();
        $this->DateEstablished = $source->getDateEstablished();
        $this->DateLastActivity = $source->getDateLastActivity();
        $this->DateLastAging = $source->getDateLastAging();
        $this->DateLastFinanceChrg = $source->getDateLastFinanceChrg();
        $this->DateLastPayment = $source->getDateLastPayment();
        $this->DateLastStatement = $source->getDateLastStatement();
        $this->DateTimeCreated = $source->getDateTimeCreated();
        $this->DateTimeUpdated = $source->getDateTimeUpdated();
        $this->DefaultCostCode = $source->getDefaultCostCode();
        $this->DefaultCostType = $source->getDefaultCostType();
        $this->DefaultCreditCardPmtType = $source->getDefaultCreditCardPmtType();
        $this->DefaultItemCode = $source->getDefaultItemCode();
        $this->DefaultPaymentType = $source->getDefaultPaymentType();
        $this->EBMConsumerUserID = $source->getEBMConsumerUserID();
        $this->EBMEnabled = $source->getEBMEnabled();
        $this->EmailStatements = $source->getEmailStatements();
        $this->HighestStmntBalance = $source->getHighestStmntBalance();
        $this->InactiveReasonCode = $source->getInactiveReasonCode();
        $this->LastPaymentAmt = $source->getLastPaymentAmt();
        $this->NumberOfInvToUseInCalc = $source->getNumberOfInvToUseInCalc();
        $this->OpenItemCustomer = $source->getOpenItemCustomer();
        $this->OpenOrderAmt = $source->getOpenOrderAmt();
        $this->PriceLevel = $source->getPriceLevel();
        $this->PrimaryShipToCode = $source->getPrimaryShipToCode();
        $this->PrintDunningMessage = $source->getPrintDunningMessage();
        $this->ResidentialAddress = $source->getResidentialAddress();
        $this->RetentionAging1 = $source->getRetentionAging1();
        $this->RetentionAging2 = $source->getRetentionAging2();
        $this->RetentionAging3 = $source->getRetentionAging3();
        $this->RetentionAging4 = $source->getRetentionAging4();
        $this->RetentionCurrent = $source->getRetentionCurrent();
        $this->SalesPersonDivisionNo2 = $source->getSalesPersonDivisionNo2();
        $this->SalesPersonDivisionNo3 = $source->getSalesPersonDivisionNo3();
        $this->SalesPersonDivisionNo4 = $source->getSalesPersonDivisionNo4();
        $this->SalesPersonDivisionNo5 = $source->getSalesPersonDivisionNo5();
        $this->SalesPersonNo2 = $source->getSalesPersonNo2();
        $this->SalesPersonNo3 = $source->getSalesPersonNo3();
        $this->SalesPersonNo4 = $source->getSalesPersonNo4();
        $this->SalesPersonNo5 = $source->getSalesPersonNo5();
        $this->SalespersonDivisionNo = $source->getSalespersonDivisionNo();
        $this->SalespersonNo = $source->getSalespersonNo();
        $this->ServiceChargeRate = $source->getServiceChargeRate();
        $this->ShipMethod = $source->getShipMethod();
        $this->SortField = $source->getSortField();
        $this->SplitCommRate2 = $source->getSplitCommRate2();
        $this->SplitCommRate3 = $source->getSplitCommRate3();
        $this->SplitCommRate4 = $source->getSplitCommRate4();
        $this->SplitCommRate5 = $source->getSplitCommRate5();
        $this->StatementCycle = $source->getStatementCycle();
        $this->TaxExemptNo = $source->getTaxExemptNo();
//        $this->TaxSchedule = $source->getTaxSchedule();
        $this->TelephoneExt = $source->getTelephoneExt();
        $this->TelephoneNo = $source->getTelephoneNo();
        $this->TemporaryCustomer = $source->getTemporaryCustomer();
        $this->URLAddress = $source->getURLAddress();
        $this->UnpaidServiceChrg = $source->getUnpaidServiceChrg();
        $this->UserCreatedKey = $source->getUserCreatedKey();
        $this->UserUpdatedKey = $source->getUserUpdatedKey();
        $this->SalespersonDivisionNo = $source->getArDivisionNo();
	    $this->SalespersonNo =  $source->getSalePersonNo();
        $this->FaxNo = $source->getFax();

        //tax
//        $this->TaxSchedule = 'AVATAX';

	    //Not confirm
        $this->CustomerType  = $source->getCustomerType();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
//	    //Information of company
        if($source->getSuperUserId()) {
//            $this->ContactCode = $this->getContactCode($source->getSuperUserId());
            $this->ContactCode = $source->getContactCode();


            $logger->info('Contact code: ' . $this->ContactCode);
            $logger->info('create customer as company with Customer No: ' . $source->getCustomerNo());
            $this->EmailAddress  = $source->getCompanyEmail();
            $this->CustomerName  = trim(substr($source->getCompanyName(), 0, 30));
            $this->CustomerNo    = $source->getCustomerNo();
            $this->TermsCode = $source->getTermsCode();
            $telephoneFormat = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", trim($source->getTelephone()));
            $this->TelephoneNo = $telephoneFormat;

            if(!$source->getNoAddress()) {
//                $street = $source->getStreetLine(1);
//                $temp1 = $street;
//                $temp2 = $temp3 = '';
//                if(strlen($street) > 30) {
//                    $temp1 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                    $street = trim(substr($street, strlen($temp1)));
//                    $temp2 = $street;
//                    if(strlen($street) > 30) {
//                        $temp2 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                        $street = trim(substr($street, strlen($temp2)));
//                        $temp3 = $street;
//                        if(strlen($street) > 30) {
//                            $temp3 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                        }
//                    }
//                }
//                $this->AddressLine1 = $temp1;
//                $this->AddressLine2 = $temp2;
//                $this->AddressLine3 = $temp3;
                $this->AddressLine1 = $source->getStreetLine(1);
                if(strlen($source->getStreetLine(1)) > 30) {
                    $this->AddressLine1 = trim(substr($source->getStreetLine(1), 0, strrpos(substr($source->getStreetLine(1), 0, 30),' ')));
                }
                $this->AddressLine2 = $source->getStreetLine(2);
                if(strlen($source->getStreetLine(2)) > 30) {
                    $this->AddressLine2 = trim(substr($source->getStreetLine(2), 0, strrpos(substr($source->getStreetLine(2), 0, 30),' ')));
                }
                $this->CountryCode = $source->getCountryId();
                $this->ZipCode = $source->getPostcode();
                $this->City = $source->getCity();
                $this->State = $this->getRegionCodeById($source->getRegionId());
            }
        } else {
            $logger->info('create customer as customer with Customer No: ' . $source->getCustomerNo());
            $this->EmailAddress  = $source->getEmail();
            $this->CustomerName  = trim(substr($source->getName(), 0, 30));
            $this->CustomerNo    = $source->getCustomerNo();
            $this->TermsCode = $source->getTermsCode();
            $telephoneFormat = preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", trim($source->getTelephone()));
            $this->TelephoneNo = $telephoneFormat;
            if(count($source->getAddresses()) > 0 && isset($source->getAddresses()[0]) && !$source->getNoAddress()) {
                $addressSave = $source->getAddresses()[0];
//                $street = $addressSave->getStreetLine(1);
//                $temp1 = $street;
//                $temp2 = $temp3 = '';
//                if(strlen($street) > 30) {
//                    $temp1 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                    $street = trim(substr($street, strlen($temp1)));
//                    $temp2 = $street;
//                    if(strlen($street) > 30) {
//                        $temp2 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                        $street = trim(substr($street, strlen($temp2)));
//                        $temp3 = $street;
//                        if(strlen($street) > 30) {
//                            $temp3 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                        }
//                    }
//                }
//                $this->AddressLine1 = $temp1;
//                $this->AddressLine2 = $temp2;
//                $this->AddressLine3 = $temp3;
                $this->AddressLine1 = $addressSave->getStreetLine(1);
                if(strlen($addressSave->getStreetLine(1)) > 30) {
                    $this->AddressLine1 = trim(substr($addressSave->getStreetLine(1), 0, strrpos(substr($addressSave->getStreetLine(1), 0, 30),' ')));
                }
                $this->AddressLine2 = $addressSave->getStreetLine(2);
                if(strlen($addressSave->getStreetLine(2)) > 30) {
                    $this->AddressLine2 = trim(substr($addressSave->getStreetLine(2), 0, strrpos(substr($addressSave->getStreetLine(2), 0, 30),' ')));
                }
                $this->CountryCode = $addressSave->getCountryId();
                $this->ZipCode = $addressSave->getPostcode();
                $this->City = $addressSave->getCity();
                $this->State = $addressSave->getRegion();
                if($region = $this->getRegionCodeById($addressSave->getRegionId())) {
                    $this->State = $region;
                }
            }
        }
        return $this;
    }

    protected function getRegionCodeById($regionId)
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $region = $objectManager->create('Magento\Directory\Model\Region')->load($regionId);
//            $postCodeCollection =  \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Directory\Model\ResourceModel\Region\CollectionFactory::class)->create();
            if($region->getCode()){
                return $region->getCode();
            }
            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }

    protected function getContactCode($customerId)
    {
        try {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customer = $objectManager->create('Magento\Customer\Model\ResourceModel\CustomerRepository')->getById($customerId);
//            $postCodeCollection =  \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Directory\Model\ResourceModel\Region\CollectionFactory::class)->create();

            $customerContactQueue = $objectManager->create('Forix\Payment\Model\ResourceModel\CustomerContactQueue\Collection')->getAddEmailToFilter($customer->getEmail())->setOrder('customercontactqueue_id', 'DESC')->getFirstItem();
            if($customerContactQueue->getId()) {
                return $customerContactQueue->getContactCode();
            }
            return null;
        } catch (\Exception $exception) {
            return null;
        }
    }

}