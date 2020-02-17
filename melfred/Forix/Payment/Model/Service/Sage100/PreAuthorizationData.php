<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/11/2018
 * Time: 16:44
 */

namespace Forix\Payment\Model\Service\Sage100;


use Forix\Payment\Model\Service\ConverterInterface;

class PreAuthorizationData extends AbstractModel implements ConverterInterface
{
    public $Address;
    public $Amount;
    public $City;
    public $Country;
    public $CreditCardCVV;
    public $CreditCardGUID;
    public $CustomerNo;
    public $EmailAddress;
    public $FaxNo;
    public $Name;
    public $SalesOrderNo;
    public $ShippingAddress;
    public $ShippingAmount;
    public $ShippingCity;
    public $ShippingCountry;
    public $ShippingName;
    public $ShippingState;
    public $ShippingZipCode;
    public $State;
    public $TaxAmount;
    public $TelephoneNo;
    public $ZipCode;

    /**
     * @param \Magento\Framework\DataObject $destination
     * @return \Magento\Framework\DataObject
     */
    public function convertTo(\Magento\Framework\DataObject $destination)
    {
        // TODO: Implement convertTo() method.
        $data = get_object_vars($this);
        $destination->setData($data);
        return $destination;
    }

    /**
     * @param \Magento\Framework\DataObject $source
     * @return $this
     */
    public function convertFrom(\Magento\Framework\DataObject $source)
    {
        $data = $source->getData();
        foreach ($data as $var => $value) {
            $this->$var = $value;
        }
        return $this;
    }
}