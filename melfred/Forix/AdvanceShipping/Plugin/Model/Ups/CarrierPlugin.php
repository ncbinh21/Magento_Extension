<?php

namespace Forix\AdvanceShipping\Plugin\Model\Ups;

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
    public function aroundCollectRates(\Magento\Ups\Model\Carrier $carrier, callable $proceed, $request)
    {
        /**
         * @var \Magento\Quote\Model\Quote\Address\RateRequest $request
         */
        $heavyWeight = (double) $this->scopeConfig->getValue('forix_catalog/heavy/weight');
        $allItems = $request->getAllItems();
        if (count($allItems) > 0) {
            foreach ($allItems as $item) {
                if ($item->getWeight() >= $heavyWeight) {
                    return $carrier->getResult();

                }
            }
        }
        return $proceed($request);
    }
}