<?php

namespace Forix\AdvanceShipping\Plugin\Model\Fedex;

class CarrierPlugin
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
    public function aroundCollectRates(\Magento\Fedex\Model\Carrier $carrier, callable $proceed, $request)
    {
        /**
         * @var \Magento\Quote\Model\Quote\Address\RateRequest $request
         */
        $heavyWeight = (double) $this->scopeConfig->getValue('forix_catalog/heavy/weight');
        if($heavyWeight && $request->getPackageWeight() >= $heavyWeight){
            return $proceed($request);
        }
        return $carrier->getResult();
    }
}