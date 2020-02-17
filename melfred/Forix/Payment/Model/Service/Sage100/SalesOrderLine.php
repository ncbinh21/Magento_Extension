<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 19/11/2018
 * Time: 16:39
 */

namespace Forix\Payment\Model\Service\Sage100;

use Forix\Payment\Model\Service\ConverterInterface;

class SalesOrderLine extends AbstractModel implements ConverterInterface
{
    public $APDivisionNo;
    public $AliasItemNo;
    public $BackorderKitCompLine;
    public $BillOption1;
    public $BillOption2;
    public $BillOption3;
    public $BillOption4;
    public $BillOption5;
    public $BillOption6;
    public $BillOption7;
    public $BillOption8;
    public $BillOption9;
    public $CommentText;
    public $Commissionable;
    public $CostCode;
    public $CostOfGoodsSoldAcctKey;
    public $CostOverridden;
    public $CostType;
    public $CustomerAction;
    public $Discount;
    public $DropShip;
    public $ExpirationDate;
    public $ExpirationOverridden;
    public $ExplodedKitItem;
    public $ExtDescriptionText;
    public $ExtendedDescriptionKey;
    public $ExtensionAmt;
    public $ItemAction;
    public $ItemCode;
    public $ItemCodeDesc;
    public $ItemType = 1;
    public $LineDiscountPercent;
    public $LineKey;
    public $LineSeqNo;
    public $LineWeight;
    public $LotSerialFullyDistributed;
    public $MasterOrderLineKey;
    public $MasterOriginalQty;
    public $MasterQtyBalance;
    public $MasterQtyOrderedToDate;
    public $PriceLevel;
    public $PriceOverridden;
    public $PrintDropShipment;
    public $PromiseDate;
    public $PurchaseOrderNo;
    public $PurchaseOrderRequiredDate;
    public $QuantityBackordered;
    public $QuantityOrdered;
    public $QuantityPerBill;
    public $QuantityShipped;
    public $RepeatingQtyShippedToDate;
    public $Revision;
    public $SOHistoryDetlSeqNo;
    public $SalesAcctKey;
    public $SalesKitLineKey;
    public $SalesOrderNo;
    public $SkipPrintCompLine = 'N';
    public $StandardKitBill;
    public $SubjectToExemption;
    public $TaxClass;
    public $UnitCost;
    public $UnitOfMeasure;
    public $UnitOfMeasureConvFactor;
    public $UnitPrice;
    public $Valuation;
    public $VendorNo;
    public $WarehouseCode;
    public $WarrantyCode;

    /**
     * @param \Magento\Framework\DataObject $destination
     * @return \Magento\Framework\DataObject
     */
    public function convertTo(\Magento\Framework\DataObject $destination)
    {
        /**
         * @var $destination \Magento\Sales\Model\Order\Item;
         */

    }

    /**
     * @param \Magento\Framework\DataObject $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source)
    {
        /**
         * @var $source \Magento\Sales\Model\Order\Item;
         */
        //Magento Fields ----------------------------
        $this->Discount = abs($source->getDiscountAmount()) > 0 ? 'Y': 'N'; // The Discount is greater than 1 character(s)

        //$this->AliasItemNo = $source->getSku(); //Where I Can find this options?
        $this->ItemCode = $source->getSku();

        //$this->ItemType = $source->getProductType(); //The ItemType is greater than 1 character(s)
        //$this->LineWeight = $source->getWeight(); //Line Weight is read only
        $this->QuantityBackordered = $source->getQtyBackordered()?:null;
        $this->QuantityOrdered = $source->getQtyOrdered()?:null;
        $this->QuantityShipped = $source->getQtyShipped()?:null;
        $order = $source->getOrder();
        if(!$order){
            $order = $source->getData('order');
        }
        $this->SalesOrderNo = $order->getSalesOrderNo()?:null;
        $this->TaxClass = 'NT';
        if($source->getProduct()) {
            $this->TaxClass = ($source->getProduct()->getData('tax_class_id') ? 'TX' : 'NT'); //Need confirm
        }
//        $this->PurchaseOrderNo = $order->getPoNumber()?:null; // Need  Confirmation form Mr.Binh
        $this->UnitPrice = $source->getBasePrice()?:null; //Need confirm
        $this->UnitCost = $source->getBaseCost()?:null;
        //Magento Fields ----------------------------

        //Can map -----------------------------------
//        $this->CommentText =  $source->getName(); // __("This item placed from Magento: #%1", $order->getIncrementId());
        $this->WarehouseCode = $this->WarehouseCode == null ? $source->getWarehouseCode() : $this->WarehouseCode;

//        $this->APDivisionNo = $source->getProduct()->getAPDivisionNo();
        //Can map -----------------------------------

        //Unknown Fields ----------------------------
        $this->BackorderKitCompLine = $this->BackorderKitCompLine == null ? $source->getBackorderKitCompLine() : $this->BackorderKitCompLine;
        $this->BillOption1 = $this->BillOption1 == null ? $source->getBillOption1() : $this->BillOption1;
        $this->BillOption2 = $this->BillOption2 == null ? $source->getBillOption2() : $this->BillOption2;
        $this->BillOption3 = $this->BillOption3 == null ? $source->getBillOption3() : $this->BillOption3;
        $this->BillOption4 = $this->BillOption4 == null ? $source->getBillOption4() : $this->BillOption4;
        $this->BillOption5 = $this->BillOption5 == null ? $source->getBillOption5() : $this->BillOption5;
        $this->BillOption6 = $this->BillOption6 == null ? $source->getBillOption6() : $this->BillOption6;
        $this->BillOption7 = $this->BillOption7 == null ? $source->getBillOption7() : $this->BillOption7;
        $this->BillOption8 = $this->BillOption8 == null ? $source->getBillOption8() : $this->BillOption8;
        $this->BillOption9 = $this->BillOption9 == null ? $source->getBillOption9() : $this->BillOption9;
        $this->Commissionable = $this->Commissionable == null ? $source->getCommissionable() : $this->Commissionable;
        $this->CostCode = $this->CostCode == null ? $source->getCostCode() : $this->CostCode;
        $this->CostOfGoodsSoldAcctKey = $this->CostOfGoodsSoldAcctKey == null ? $source->getCostOfGoodsSoldAcctKey() : $this->CostOfGoodsSoldAcctKey;
        $this->CostOverridden = $this->CostOverridden == null ? $source->getCostOverridden() : $this->CostOverridden;
        $this->CostType = $this->CostType == null ? $source->getCostType() : $this->CostType;
        $this->CustomerAction = $this->CustomerAction == null ? $source->getCustomerAction() : $this->CustomerAction;
        $this->DropShip = $this->DropShip == null ? $source->getDropShip() : $this->DropShip;
        $this->ExpirationDate = $this->ExpirationDate == null ? $source->getExpirationDate() : $this->ExpirationDate;
        $this->ExpirationOverridden = $this->ExpirationOverridden == null ? $source->getExpirationOverridden() : $this->ExpirationOverridden;
        $this->ExplodedKitItem = $this->ExplodedKitItem == null ? $source->getExplodedKitItem() : $this->ExplodedKitItem;
        $this->ExtDescriptionText = $this->ExtDescriptionText == null ? $source->getExtDescriptionText() : $this->ExtDescriptionText;
        $this->ExtendedDescriptionKey = $this->ExtendedDescriptionKey == null ? $source->getExtendedDescriptionKey() : $this->ExtendedDescriptionKey;
        $this->ExtensionAmt = $this->ExtensionAmt == null ? $source->getExtensionAmt() : $this->ExtensionAmt;
        $this->ItemAction = $this->ItemAction == null ? $source->getItemAction() : $this->ItemAction;
        $this->LineDiscountPercent = $this->LineDiscountPercent == null ? $source->getLineDiscountPercent() : $this->LineDiscountPercent;
        $this->LineKey = $this->LineKey == null ? $source->getLineKey() : $this->LineKey;
        $this->LineSeqNo = $this->LineSeqNo == null ? $source->getLineSeqNo() : $this->LineSeqNo;
        $this->LotSerialFullyDistributed = $this->LotSerialFullyDistributed == null ? $source->getLotSerialFullyDistributed() : $this->LotSerialFullyDistributed;
        $this->MasterOrderLineKey = $this->MasterOrderLineKey == null ? $source->getMasterOrderLineKey() : $this->MasterOrderLineKey;
        $this->MasterOriginalQty = $this->MasterOriginalQty == null ? $source->getMasterOriginalQty() : $this->MasterOriginalQty;
        $this->MasterQtyBalance = $this->MasterQtyBalance == null ? $source->getMasterQtyBalance() : $this->MasterQtyBalance;
        $this->MasterQtyOrderedToDate = $this->MasterQtyOrderedToDate == null ? $source->getMasterQtyOrderedToDate() : $this->MasterQtyOrderedToDate;
        $this->PriceLevel = $this->PriceLevel == null ? $source->getPriceLevel() : $this->PriceLevel;
        $this->PriceOverridden = $this->PriceOverridden == null ? $source->getPriceOverridden() : $this->PriceOverridden;
        $this->PrintDropShipment = $this->PrintDropShipment == null ? $source->getPrintDropShipment() : $this->PrintDropShipment;
        $this->PromiseDate = $this->PromiseDate == null ? $source->getPromiseDate() : $this->PromiseDate;
        $this->PurchaseOrderRequiredDate = $this->PurchaseOrderRequiredDate == null ? $source->getPurchaseOrderRequiredDate() : $this->PurchaseOrderRequiredDate;
        $this->QuantityPerBill = $this->QuantityPerBill == null ? $source->getQuantityPerBill() : $this->QuantityPerBill;
        $this->RepeatingQtyShippedToDate = $this->RepeatingQtyShippedToDate == null ? $source->getRepeatingQtyShippedToDate() : $this->RepeatingQtyShippedToDate;
        $this->Revision = $this->Revision == null ? $source->getRevision() : $this->Revision;
        $this->SOHistoryDetlSeqNo = $this->SOHistoryDetlSeqNo == null ? $source->getSOHistoryDetlSeqNo() : $this->SOHistoryDetlSeqNo;
        $this->SalesAcctKey = $this->SalesAcctKey == null ? $source->getSalesAcctKey() : $this->SalesAcctKey;
        $this->SalesKitLineKey = $this->SalesKitLineKey == null ? $source->getSalesKitLineKey() : $this->SalesKitLineKey;
        $this->SkipPrintCompLine = $this->SkipPrintCompLine == null ? $source->getSkipPrintCompLine() : $this->SkipPrintCompLine; //The Skip Printing Of This Component failed validation on Y,N
        $this->StandardKitBill = $this->StandardKitBill == null ? $source->getStandardKitBill() : $this->StandardKitBill;
        $this->SubjectToExemption = $this->SubjectToExemption == null ? $source->getSubjectToExemption() : $this->SubjectToExemption;
        $this->UnitOfMeasure = $this->UnitOfMeasure == null ? $source->getUnitOfMeasure() : $this->UnitOfMeasure;
        $this->UnitOfMeasureConvFactor = $this->UnitOfMeasureConvFactor == null ? $source->getUnitOfMeasureConvFactor() : $this->UnitOfMeasureConvFactor;
        $this->Valuation = $this->Valuation == null ? $source->getValuation() : $this->Valuation;
        $this->VendorNo = $this->VendorNo == null ? $source->getVendorNo() : $this->VendorNo;
        $this->WarrantyCode = $this->WarrantyCode == null ? $source->getWarrantyCode() : $this->WarrantyCode;

        //custom
//        $this->ItemCodeDesc = $source->getName();
    }

}