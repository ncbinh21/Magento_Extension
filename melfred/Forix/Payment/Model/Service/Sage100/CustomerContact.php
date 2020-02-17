<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 16:11
 */

namespace Forix\Payment\Model\Service\Sage100;


use Forix\Payment\Model\Service\ConverterInterface;
class CustomerContact  extends AbstractModel implements ConverterInterface
{
    public $ARDivisionNo = '00';
    public $AddressLine1;
    public $AddressLine2;
    public $AddressLine3;
    public $City;
    public $ContactCode;
    public $ContactName;
    public $ContactNotes;
    public $ContactTitle;
    public $CountryCode;
    public $CustomerNo;
    public $DateTimeCreated;
    public $DateTimeUpdated;
    public $EBMUserID;
    public $EmailAddress;
    public $FaxNo;
    public $Salutation;
    public $State;
    public $TelephoneExt1;
    public $TelephoneExt2;
    public $TelephoneNo1;
    public $TelephoneNo2;
    public $UserCreatedKey;
    public $UserUpdatedKey;
    public $ZipCode;

    /**
     * @param \Magento\Framework\DataObject $destination
     * @return \Magento\Framework\DataObject
     */
    public function convertTo(\Magento\Framework\DataObject $destination)
    {
	    //Need Create new Attribute ----------------------
		$destination->setARDivisionNo($this->ARDivisionNo);
		$destination->setAddressLine1($this->AddressLine1);
		$destination->setAddressLine2($this->AddressLine2);
		$destination->setAddressLine3($this->AddressLine3);
		$destination->setCity($this->City);
		$destination->setContactCode($this->ContactCode);
		$destination->setContactName($this->ContactName);
		$destination->setContactNotes($this->ContactNotes);
		$destination->setContactTitle($this->ContactTitle);
		$destination->setCountryCode($this->CountryCode);
		$destination->setCustomerNo($this->CustomerNo);
		$destination->setDateTimeCreated($this->DateTimeCreated);
		$destination->setDateTimeUpdated($this->DateTimeUpdated);
		$destination->setEBMUserID($this->EBMUserID);
		$destination->setEmailAddress($this->EmailAddress);
		$destination->setFaxNo($this->FaxNo);
		$destination->setSalutation($this->Salutation);
		$destination->setState($this->State);
		$destination->setTelephoneExt1($this->TelephoneExt1);
		$destination->setTelephoneExt2($this->TelephoneExt2);
		$destination->setTelephoneNo1($this->TelephoneNo1);
		$destination->setTelephoneNo2($this->TelephoneNo2);
		$destination->setUserCreatedKey($this->UserCreatedKey);
		$destination->setUserUpdatedKey($this->UserUpdatedKey);
		$destination->setZipCode($this->ZipCode);
    }

    /**
     * @param \Magento\Framework\DataObject $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source)
    {
		$this->ContactNotes = $source->getContactNotes();
		$this->ContactTitle = $source->getContactTitle();
		$this->CountryCode = $source->getCountryCode();
		$this->DateTimeCreated = $source->getDateTimeCreated();
		$this->DateTimeUpdated = $source->getDateTimeUpdated();
		$this->EBMUserID = $source->getEBMUserID();
		$this->FaxNo = $source->getFaxNo();
		$this->Salutation = $source->getSalutation();
		$this->TelephoneExt1 = $source->getTelephoneExt1();
		$this->TelephoneExt2 = $source->getTelephoneExt2();
		$this->TelephoneNo1 = $source->getTelephoneNo1();
		$this->TelephoneNo2 = $source->getTelephoneNo2();
		$this->UserCreatedKey = $source->getUserCreatedKey();
		$this->UserUpdatedKey = $source->getUserUpdatedKey();
        $this->ContactName = $source->getFirstname() . ' ' .$source->getLastname();
        $this->EmailAddress = $source->getEmail();


		//information customer
//        $street = $source->getStreetLine(1);
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
//        $this->AddressLine1 = $temp1;
//        $this->AddressLine2 = $temp2;
//        $this->AddressLine3 = $temp3;
//        $this->ZipCode = $source->getZipCode();
//        $this->State = $source->getState();
//        $this->City = $source->getCity();

        $this->CustomerNo = $source->getCustomerNo();
        $this->ContactCode = $source->getContactCode();

        return $this;
    }
}