<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.2-CE - Soft Star Shoes
 * Date: 05
 * Time: 15:30
 */
namespace Forix\InvoicePrint\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Data extends AbstractHelper
{
    const INVOICE_PDF_FOOTER_MESSAGE = 'invoice_print/settings/explanation_message';
    const INVOICE_PDF_SHIPPING_AGREEMENT = 'invoice_print/settings/shipping_agreement';
    const INVOICE_PDF_SHIPPING_AGREEMENT_COUNTRY = 'invoice_print/settings/shipping_agreement_country';

    protected $_ruleFactory;
    protected $_ruleCollection = null;
    protected $_countryFactory;
    public function __construct(
        Context $context,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        \Forix\InvoicePrint\Model\RuleFactory $ruleFactory
    )
    {
        parent::__construct($context);
        $this->_countryFactory = $countryFactory;
        $this->_ruleFactory = $ruleFactory;
    }


    public function getCountryName($countryCode){
        $country = $this->_countryFactory->create()->loadByCode($countryCode);
        return $country->getName();
    }

    /**
     * @return \Forix\InvoicePrint\Model\Rule[]
     */
    public function getRuleCollection(){
        if(null === $this->_ruleCollection){
            $collection = $this->_ruleFactory->create()->getCollection();
            $collection->getAvailableCollection();
            $this->_ruleCollection = $collection->getItems();
        }
        return $this->_ruleCollection;
    }

    /**
     * @return string
     */
    public function getInvoiceFooterMessage(){
        return $this->scopeConfig->getValue(self::INVOICE_PDF_FOOTER_MESSAGE);
    }

    /**
     * @return string
     */
    public function getInvoiceShippingAgreementMessage(){
        return $this->scopeConfig->getValue(self::INVOICE_PDF_SHIPPING_AGREEMENT);
    }

    /**
     * @return array
     */
    public function getInvoiceShippingAgreementCountry(){
        return explode(',', strtolower((string)$this->scopeConfig->getValue(self::INVOICE_PDF_SHIPPING_AGREEMENT_COUNTRY)));
    }
}