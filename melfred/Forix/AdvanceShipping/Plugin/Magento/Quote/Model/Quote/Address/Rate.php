<?php
/**
 * Created by Nick Howard.
 * Job Title: Magento Developer
 * Project: melfredborzall.local
 */

namespace Forix\AdvanceShipping\Plugin\Magento\Quote\Model\Quote\Address;


class Rate
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    protected $scopeConfig;

    protected $shippingSurcharges;
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }
    public function getShippingSurcharges(){
        if(is_null($this->shippingSurcharges)){
            $this->shippingSurcharges = $this->scopeConfig->getValue('shipping/additional_fee/shipping_surcharges');
            $this->shippingSurcharges = json_decode($this->shippingSurcharges);
        }
        return $this->shippingSurcharges;
    }
    public function afterImportShippingRate(\Magento\Quote\Model\Quote\Address\Rate $subject, $result){
        $shippingSurcharges = $this->getShippingSurcharges();
        foreach($shippingSurcharges as $_surcharge){
            if($subject->getCode() == $_surcharge->shipping_method){
                $_surcharge = (double) $_surcharge->shipping_surcharge;
                $newPrice = $subject->getPrice() + $subject->getPrice() * $_surcharge/100;
                $subject->setPrice($newPrice);
                break;
            }
        }
        
        return $result;
    }
}