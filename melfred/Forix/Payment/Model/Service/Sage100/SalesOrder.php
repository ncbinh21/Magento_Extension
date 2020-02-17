<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 15:35
 */

namespace Forix\Payment\Model\Service\Sage100;

use Forix\Payment\Model\Service\ConverterInterface;
use \Magento\Sales\Model\Order as MagentoOrder;
use Magento\Backend\Block\Widget\Grid\Column\Filter\Datetime;
use \Forix\Payment\Model\Service\Sage100\Shipping as Sage100Shipping;
use Magento\Framework\Exception\LocalizedException;


class SalesOrder extends AbstractModel implements ConverterInterface
{
    const SALE_ORDER_NEW = 'N';//N,O,C,H
    const SALE_ORDER_OPEN = 'O';//N,O,C,H
    const SALE_ORDER_CLOSE = 'C';//N,O,C,H
    const SALE_ORDER_HOLD = 'H';//N,O,C,H

    public $ARDivisionNo = null;
    public $BatchEmail = null;
    public $BatchFax = null;
    public $BillToAddress1 = null;
    public $BillToAddress2 = null;
    public $BillToAddress3 = null;
    public $BillToCity = null;
    public $BillToCountryCode = null;
    public $BillToCustomerNo = null;
    public $BillToDivisionNo = null;
    public $BillToName = null;
    public $BillToState = null;
    public $BillToZipCode = null;
    public $CRMCompanyID = null;
    public $CRMOpportunityID = null;
    public $CRMPersonID = null;
    public $CRMProspectID = null;
    public $CRMUserID = null;
    public $CancelReasonCode = null;
    public $CheckNoForDeposit = null;
    public $Comment = null;
    public $CommissionRate = 0;
    public $ConfirmTo = null;
    public $CurrentInvoiceNo = null;
    public $CustomerNo = null;
    public $CustomerPONo = null;
    public $CycleCode = null;
    public $DateTimeCreated = null;
    public $DateTimeUpdated = null;
    public $DepositAmt = null;
    public $DiscountAmt = null;
    public $DiscountRate = null;
    public $EBMSubmissionType = null;
    public $EBMUserIDSubmittingThisOrder = null;
    public $EBMUserType = null;
    public $EmailAddress = null;
    public $FOB = null;
    public $FaxNo = null;
    public $FreightAmt = null;
    public $FreightCalculationMethod = null;
    public $InvalidTaxCalc = null;
    public $JobNo = null;
    public $LastInvoiceOrderDate = null;
    public $LastInvoiceOrderNo = null;
    public $LastNoOfShippingLabels = null;
    /**
     * @var SalesOrderLine[]
     */
    public $Lines = [];
    public $LotSerialLinesExist = null;
    public $MasterRepeatingOrderNo = null;
    public $NonTaxableAmt = null;
    public $NonTaxableSubjectToDiscount = null;
    public $NumberOfShippingLabels = null;
    public $OrderDate = null;
    public $OrderStatus = null; //N,O,C,H
    public $OrderType = null;
    public $OtherPaymentTypeRefNo = null;
    public $PaymentType = null;
    public $PaymentTypeCategory = 'P';

    /**
     * @var SalesOrderPayment[]
     */
    public $Payments = [];
    public $PickingSheetPrinted = null;
    public $PrintPickingSheets = null;
    public $PrintSalesOrders = null;
    public $RMANo = null;
    public $ResidentialAddress = null;
    public $SalesOrderNo = null;
    public $SalesOrderPrinted = null;
    public $SalesTaxAmt = null;
    public $SalespersonDivisionNo = null;
    public $SalespersonDivisionNo2 = null;
    public $SalespersonDivisionNo3 = null;
    public $SalespersonDivisionNo4 = null;
    public $SalespersonDivisionNo5 = null;
    public $SalespersonNo = null;
    public $SalespersonNo2 = null;
    public $SalespersonNo3 = null;
    public $SalespersonNo4 = null;
    public $SalespersonNo5 = null;
    public $ShipExpireDate = null;
    public $ShipToAddress1 = null;
    public $ShipToAddress2 = null;
    public $ShipToAddress3 = null;
    public $ShipToCity = null;
    public $ShipToCode = null;
    public $ShipToCountryCode = null;
    public $ShipToName = null;
    public $ShipToState = null;
    public $ShipToZipCode = null;
    public $ShipVia = null;
    public $ShipWeight = null;
    public $ShipZone = null;
    public $ShipZoneActual = null;
    public $SplitCommRate2 = null;
    public $SplitCommRate3 = null;
    public $SplitCommRate4 = null;
    public $SplitCommRate5 = null;
    public $SplitCommissions = null;
    public $TaxExemptNo = null;
    public $TaxSchedule = 'DEFAULT';
    public $TaxSubjToDiscPrcntOfTotSubjTo = null;
    public $TaxableAmt = null;
    public $TaxableSubjectToDiscount = null;
    public $TermsCode = null;
    public $UserCreatedKey = null;
    public $UserUpdatedKey = null;
    public $WarehouseCode = '001';
    public $Weight = null;

    /**
     * @param \Magento\Sales\Model\Order $destination
     * @return \Magento\Framework\DataObject
     */
    public function convertTo(\Magento\Framework\DataObject $destination)
    {
        $status = $this->OrderStatus;
        $magentoStatus = null;
        switch (strtoupper($status)){
            case self::SALE_ORDER_OPEN:
                $magentoStatus = MagentoOrder::STATE_PROCESSING;
                break;
            case self::SALE_ORDER_CLOSE:
                $magentoStatus = MagentoOrder::STATE_CLOSED;
                break;
            case self::SALE_ORDER_HOLD:
                $magentoStatus = MagentoOrder::STATE_HOLDED;
                break;
            case self::SALE_ORDER_NEW:
                $magentoStatus = MagentoOrder::STATE_NEW;
                break;
        }
        $destination->setStatus($magentoStatus);
        return $destination;
    }

    /**
     * @param \Magento\Sales\Model\Order $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /**
         * @var $countryInformation \Magento\Directory\Api\CountryInformationAcquirerInterface
         */
        //$countryInformation = $objectManager->create(\Magento\Directory\Api\CountryInformationAcquirerInterface::class);
        /**
         * @var $source \Magento\Sales\Model\Order
         */
        //Convert Order -------------------------------
        $this->ARDivisionNo = $source->getArDivisionNo()?:null;
        $this->BatchEmail = $source->getBatchEmail()?:null;
        $this->BatchFax = $source->getBatchFax()?:null;

//        $this->DateTimeCreated = date_format(new \Datetime($source->getCreatedAt()), "Y-m-d\TH:m:s\.vP"); Creation Date is read only
//        $this->DateTimeUpdated = date_format(new \Datetime($source->getUpdatedAt()), "Y-m-d\TH:m:s\.vP"); Last Update Date is read only.
        $this->FaxNo = $source->getBillingAddress()->getFax()?:null;

        $this->SalesOrderNo = $source->getSalesOrderNo();
        $logger->info('Create order with Sales Order No: ' . $source->getSalesOrderNo());
        $this->Comment = substr(__("%1", $source->getOrderNote()), 0, 29);
//        $this->CurrentInvoiceNo = $source->getInvoiceCollection()->getFirstItem()->getData('increment_id');
        //$this->addOrderField('magento_order', $source->getIncrementId());
        //$this->addOrderField('base_weight', $source->getWeight());
        //$this->addOrderField('grand_total', $source->getGrandTotal());
        //$this->addOrderField('subtotal', $source->getSubtotal());

        $this->DiscountAmt = abs($source->getDiscountAmount())?:null;
        //Total Not Including Tax
//        $this->TaxableAmt = $source->getSubtotal(); //Taxable Amount is read only.
//        $this->TaxSchedule  = $this->TaxSchedule  == null ? $source->getTaxSchedule() : $this->TaxSchedule;  set default is AVATAX

//        $this->SalesTaxAmt = $source->getTaxAmount()?:null;
        $this->PaymentType =  \Forix\Payment\Model\Sage100Payment::PAYMENT_TYPE;
        // Billing Address ----------------------------
        /**
         * Only working with USA Address
         * @var $billingAddress \Magento\Sales\Model\Order\Address
         */
        $billingAddress = $source->getBillingAddress();

        //info customer
        if($customerNo = $this->getCustomerNoFromSage($source)) {
            $this->CustomerNo = $customerNo;
        } else {
            throw new LocalizedException(__('We cannot register Customer No, Please try to place the order again.'));
        }

        if($sageTaxSchedule = $this->getTaxScheduleFromSage($customerNo)) {
            $this->TaxSchedule = $sageTaxSchedule;
        }
        $this->CustomerPONo = $source->getPoNumber()?:null;
        $this->EmailAddress = $source->getCustomerEmail();

        // Billing Address ----------------------------
//        $street = $billingAddress->getStreetLine(1);
//        $temp1 = $street;
//        $temp2 = $temp3 = '';
//        if(strlen($street) > 30) {
//            $temp1 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//            $street = trim(substr($street, strlen($temp1)));
//            $temp2 = $street;
//            if(strlen($street) > 30) {
//                $temp2 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                $street = trim(substr($street, strlen($temp2)));
//                $temp3 = $street;
//                if(strlen($street) > 30) {
//                    $temp3 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                }
//            }
//        }
//        $this->BillToAddress1 = $temp1;
//        $this->BillToAddress2 = $temp2;
//        $this->BillToAddress3 = $temp3;
        $this->BillToAddress1 = $billingAddress->getStreetLine(1);
        if(strlen($billingAddress->getStreetLine(1)) > 30) {
            $this->BillToAddress1 = trim(substr($billingAddress->getStreetLine(1), 0, strrpos(substr($billingAddress->getStreetLine(1), 0, 30),' ')));
        }
        $this->BillToAddress2 = $billingAddress->getStreetLine(2);
        if(strlen($billingAddress->getStreetLine(2)) > 30) {
            $this->BillToAddress2 = trim(substr($billingAddress->getStreetLine(2), 0, strrpos(substr($billingAddress->getStreetLine(2), 0, 30),' ')));
        }


        $this->BillToCity = $billingAddress->getCity()?:null;
        $this->BillToCountryCode = $billingAddress->getCountryId()?:null;
            //$this->BillToCustomerNo = $source->getCustomer()->getCustomerNo(); //Need create attribute
            /**
             * Could not set SO_SalesOrder_Bus column BillToDivisionNo$:
             * This field cannot be set when the Accounts Receivable option Bill To Customer Reporting is No.
             */
            //$this->BillToDivisionNo = $source->getArDivisionNo(); //Need create attribute
            //This field cannot be set when the Accounts Receivable option Bill To Customer Reporting is No
        $this->BillToState = $billingAddress->getRegionCode()?:null;
        $this->BillToZipCode = $billingAddress->getPostcode()?:null;

        $billToName = $billingAddress->getCompany()?:null;
        if(strlen($billToName) > 30){
            $billToName = substr($billToName, 0, 30);
        }
        // $this->BillToName
        // MB-1269 Can't create order while billing to name > 30 Characters.
        // BillToName < 30 characters.
        $this->BillToName = $billToName;
        // Shipping Address ----------------------------
        $shippingAddress = $source->getShippingAddress();
        //$this->ShipExpireDate = $source->get;
        if($shippingAddress) {
//            $street = $shippingAddress->getStreetLine(1);
//            $temp1 = $street;
//            $temp2 = $temp3 = '';
//            if(strlen($street) > 30) {
//                $temp1 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                $street = trim(substr($street, strlen($temp1)));
//                $temp2 = $street;
//                if(strlen($street) > 30) {
//                    $temp2 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                    $street = trim(substr($street, strlen($temp2)));
//                    $temp3 = $street;
//                    if(strlen($street) > 30) {
//                        $temp3 = trim(substr($street, 0, strrpos(substr($street, 0, 30),' ')));
//                    }
//                }
//            }
//
//            $this->ShipToAddress1 = $temp1;
//            $this->ShipToAddress2 = $temp2;
//            $this->ShipToAddress3 = $temp3;
            $this->ShipToAddress1 = $shippingAddress->getStreetLine(1);
            if(strlen($shippingAddress->getStreetLine(1)) > 30) {
                $this->ShipToAddress1 = trim(substr($shippingAddress->getStreetLine(1), 0, strrpos(substr($shippingAddress->getStreetLine(1), 0, 30),' ')));
            }
            $this->ShipToAddress2 = $shippingAddress->getStreetLine(2);
            if(strlen($shippingAddress->getStreetLine(2)) > 30) {
                $this->ShipToAddress2 = trim(substr($shippingAddress->getStreetLine(2), 0, strrpos(substr($shippingAddress->getStreetLine(2), 0, 30),' ')));
            }
            $this->ShipToCity = $shippingAddress->getCity();
            $this->ShipToCountryCode = $shippingAddress->getCountryId();

            //get company name
            $shipToName = $shippingAddress->getCompany()?:null;
            // $this->ShipToName
            // MB-1269 Can't create order while billing to name > 30 Characters.
            // ShiptoName < 30 characters.
            if(strlen($shipToName) > 30){
                $shipToName = substr($shipToName, 0, 30);
            }
            $this->ShipToName = $shipToName;
            $this->ShipToState = $shippingAddress->getRegionCode();
            $this->ShipToZipCode = $shippingAddress->getPostcode();
        }
        /*if($companyName = $this->getCompanyNameFromCustomer($source)) {
            $this->ShipToName = $companyName;
            $this->BillToName = $companyName;
        }*/
        $this->ShipVia = Sage100Shipping::getShippingCode($source->getShippingMethod(true));
        $this->ShipWeight = intval(round($source->getWeight() + 0.499,0));
        $this->Weight = intval(round($source->getWeight() + 0.499,0));
        //$this->ShipZone = ;
        //$this->ShipZoneActual = $shippingAddress->;
        // Shipping Address ----------------------------

        $this->CRMCompanyID = $this->CRMCompanyID == null ? $source->getCRMCompanyID() : $this->CRMCompanyID;
        $this->CRMOpportunityID = $this->CRMOpportunityID == null ? $source->getCRMOpportunityID() : $this->CRMOpportunityID;
        $this->CRMPersonID = $this->CRMPersonID == null ? $source->getCRMPersonID() : $this->CRMPersonID;
        $this->CRMProspectID = $this->CRMProspectID == null ? $source->getCRMProspectID() : $this->CRMProspectID;
        $this->CRMUserID = $this->CRMUserID == null ? $source->getCRMUserID() : $this->CRMUserID;
        $this->CancelReasonCode = $this->CancelReasonCode == null ? $source->getCancelReasonCode() : $this->CancelReasonCode;
        $this->CheckNoForDeposit = $this->CheckNoForDeposit == null ? $source->getCheckNoForDeposit() : $this->CheckNoForDeposit;
        $this->CommissionRate = $this->CommissionRate == null ? $source->getCommissionRate() : $this->CommissionRate;
        $this->CycleCode = $this->CycleCode == null ? $source->getCycleCode() : $this->CycleCode;
        $this->DepositAmt = $this->DepositAmt == null ? $source->getDepositAmt() : $this->DepositAmt;
        $this->DiscountRate = $this->DiscountRate == null ? $source->getDiscountRate() : $this->DiscountRate;
        $this->EBMSubmissionType = $this->EBMSubmissionType == null ? $source->getEBMSubmissionType() : $this->EBMSubmissionType;
        $this->EBMUserIDSubmittingThisOrder = $this->EBMUserIDSubmittingThisOrder == null ? $source->getEBMUserIDSubmittingThisOrder() : $this->EBMUserIDSubmittingThisOrder;
        $this->EBMUserType = $this->EBMUserType == null ? $source->getEBMUserType() : $this->EBMUserType;

        $this->FOB = $this->FOB == null ? $source->getFOB() : $this->FOB;
        $this->FreightCalculationMethod = $this->FreightCalculationMethod == null ? $source->getFreightCalculationMethod() : $this->FreightCalculationMethod;
        $this->InvalidTaxCalc = $this->InvalidTaxCalc == null ? $source->getInvalidTaxCalc() : $this->InvalidTaxCalc;
        $this->JobNo = $this->JobNo == null ? $source->getJobNo() : $this->JobNo;
        $this->LastInvoiceOrderDate = $this->LastInvoiceOrderDate == null ? $source->getLastInvoiceOrderDate() : $this->LastInvoiceOrderDate;
        $this->LastInvoiceOrderNo = $this->LastInvoiceOrderNo == null ? $source->getLastInvoiceOrderNo() : $this->LastInvoiceOrderNo;
        $this->LastNoOfShippingLabels = $this->LastNoOfShippingLabels == null ? $source->getLastNoOfShippingLabels() : $this->LastNoOfShippingLabels;

        //fix
        $confirmToName = 'Attn ' . $shippingAddress->getFirstname() . ' ' . $shippingAddress->getLastname();
        $this->ConfirmTo = $confirmToName;
        if(strlen($confirmToName) > 30) {
            $this->ConfirmTo = trim(substr($confirmToName, 0, strrpos(substr($confirmToName, 0, 30),' ')));
        }
//        $this->ShipToCode = 1;
//        if($shipCode = $this->getShipToCodeFromSystem($source)) {
//            $this->ShipToCode = $shipCode;
//        }

        $this->FreightAmt = $source->getShippingAmount();
        $this->TermsCode = $source->getTermsCode();
        //Convert Order -----------------------------------------------
        // Convert sales order items ------------------------------------
        /**
         * @var $source \Magento\Sales\Model\Order;
         */
        $items = $source->getItems();
        $orderLine = [];
        switch ($source->getStatus()){
            case MagentoOrder::STATE_NEW:
                $this->OrderStatus = self::SALE_ORDER_NEW;
                break;
            case MagentoOrder::STATE_PROCESSING:
                $this->OrderStatus = self::SALE_ORDER_OPEN;
                break;
            case MagentoOrder::STATE_PENDING_PAYMENT:
                $this->OrderStatus = self::SALE_ORDER_OPEN;
                break;
            case MagentoOrder::STATE_PAYMENT_REVIEW:
                $this->OrderStatus = self::SALE_ORDER_OPEN;
                break;
            case MagentoOrder::STATE_HOLDED:
                $this->OrderStatus = self::SALE_ORDER_HOLD;
                break;
            case MagentoOrder::STATE_COMPLETE:
                $this->OrderStatus = self::SALE_ORDER_OPEN;
                break;
            case MagentoOrder::STATE_CANCELED:
                $this->OrderStatus = self::SALE_ORDER_CLOSE;
                break;
            case MagentoOrder::STATE_CLOSED:
                $this->OrderStatus = self::SALE_ORDER_CLOSE;
                break;
            default:
                $this->OrderStatus = self::SALE_ORDER_NEW;
                break;
        }
        foreach ($items as $item) {
            if ($item->getProduct() && ($item->getProduct()->isVirtual() || $item->getParentItem())) {
                continue;
            }
            $line = new SalesOrderLine();
            if($source->getCustomer()){
                $item->setData('warehouse_code', $this->WarehouseCode);
            }
            $item->setRepeatingQtyShippedToDate(0);
            $item->setSkipPrintCompLine(1);
            $item->setData('order',$source);
            $line->convertFrom($item);
            array_push($orderLine, $line);
        }


        $this->Lines = $orderLine;
        // Convert sales order items ------------------------------------
        // Convert Sales Payments.
        $payment = $source->getPayment();
        if($payment->getMethod() == 'magenest_sagepayus') {
            $this->OtherPaymentTypeRefNo = $source->getIncrementId();
            $this->PaymentType = 'WEB';
            $this->DepositAmt = $source->getGrandTotal();
        }

        $_payment = new SalesOrderPayment();
        $payment->setData('order', $source);
        $_payment->convertFrom($payment);
//        array_push($this->Payments, $_payment);
        if($_payment->CreditCardID && $_payment->CreditCardGUID && $_payment->ExpirationDateMonth && $_payment->ExpirationDateYear) {
            $this->Payments['SalesOrderPayment'] = $_payment;
        }
        return $this;
    }

    protected function getShipToCodeFromSystem($order) {
        if($customerId = $order->getCustomerId()) {
            try {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $companyManagement = $objectManager->create(\Magento\Company\Model\CompanyManagement::class);
                $company = $companyManagement->getByCustomerId($customerId);
                $companyId = $company->getId();
                $addressId = $order->getShippingAddress()->getCustomerAddressId();
                return $this->checkShipToCode($companyId, $addressId);
            } catch (\Exception $exception) {
                $logger->info('-----Error getShipToCode-------');
                $logger->info($exception->getMessage());
                return null;
            }
        }
        return null;
    }

    protected function checkShipToCode($companyId, $addressId = null) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->create(\Magento\Framework\App\ResourceConnection::class);
        if($addressId) {
            $checkExist = 'select * from forix_payment_shiptocode where company_id = "' . $companyId . '" and customer_address_id = "' . $addressId . '" ORDER BY shiptocode_id DESC LIMIT 1';
            $checkExistShipToCode = $resource->getConnection()->fetchRow($checkExist);
            if($checkExistShipToCode && isset($checkExistShipToCode['ship_to_code'])) {
                return $checkExistShipToCode['ship_to_code'];
            } else {
                return $this->saveShipToCode($resource, $companyId, $addressId);
            }
        } else {
            return $this->saveShipToCode($resource, $companyId, $addressId);
        }
    }

    protected function saveShipToCode($resource, $companyId, $addressId = null) {
        $checkNew = 'select * from forix_payment_shiptocode where company_id = "' . $companyId . '" ORDER BY shiptocode_id DESC LIMIT 1';
        $checkNewShipToCode = $resource->getConnection()->fetchRow($checkNew);
        $createNew = 'insert into forix_payment_shiptocode (company_id, customer_address_id, ship_to_code) values ("' . $companyId .'","' . $addressId . '",1)';
        $addShipToCode = 1;
        if($checkNewShipToCode && isset($checkNewShipToCode['ship_to_code'])) {
            $addShipToCode = $checkNewShipToCode['ship_to_code'] + 1; //+1 ship_to_code
            $createNew = 'insert into forix_payment_shiptocode (company_id, customer_address_id, ship_to_code) values ("' . $companyId .'","' . $addressId . '",' . $addShipToCode . ')';
        }
        $resource->getConnection()->query($createNew);
        return $addShipToCode;
    }

    protected function getCompanyNameFromCustomer($order) {
        if($customerId = $order->getCustomerId()) {
            try {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/binh_developer.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $companyManagement = $objectManager->create(\Magento\Company\Model\CompanyManagement::class);
                $company = $companyManagement->getByCustomerId($customerId);
                return $company->getCompanyName();
            } catch (\Exception $exception) {
                $logger->info('-----Error getCompanyName-------');
                $logger->info($exception->getMessage());
                return null;
            }
        }
        return null;
    }

    protected function getConfirmTo($order) {
        if($customerId = $order->getCustomerId()) {
        }
        return null;
    }

    protected function getCustomerNoFromSage($order) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create(\Forix\Payment\Helper\PaymentHelper::class);
        return $helper->getCustomerNo($order);
    }

    protected function getTaxScheduleFromSage($customerNo)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper = $objectManager->create(\Forix\Payment\Helper\PaymentHelper::class);
        return $helper->getTaxScheduleCustomerFromSage($customerNo);
    }
}