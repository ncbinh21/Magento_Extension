<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2 - EE - Melfredborzall
 * Date: 24/04/2019
 * Time: 13:37
 */

namespace Forix\AdvanceShipping\Rewrite\Magento\Shipping\Model;

class Shipping extends \Magento\Shipping\Model\Shipping
{
    /**
     * Fix bug FREE method didn't appear
     * Fix Free Shipping Method By Cart Price Rules
     * Add a rate to the result
     *
     * @param \Magento\Shipping\Model\Carrier\AbstractCarrier $carrier
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @param \Magento\Quote\Model\Quote\Address\RateResult\AbstractResult|\Magento\Shipping\Model\Rate\Result $result
     * @return $this
     */
    protected function appendResults($carrier, $request, $result)
    {
        if ($result instanceof \Magento\Shipping\Model\Rate\Result) {
            $rates = $result->getAllRates();
            foreach ($rates as $rate) {
                $this->appendResults($carrier, $request, $rate);
            }
        } else {
            $carrierEnableFree = $carrier->getConfigData('free_shipping_enable');
            if ($carrierEnableFree) {
                $carrierFreeCode = $carrier->getConfigData('free_method');
                $methodName = $result->getMethod();
                if ($carrierFreeCode == $methodName) {
                    if (!($request->getFreeShipping())) {
                        return $this;
                    }
                }
            }
            $this->getResult()->append($result);
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function collectCarrierRates($carrierCode, $request)
    {
        /* @var $carrier \Magento\Shipping\Model\Carrier\AbstractCarrier */
        $carrier = $this->_carrierFactory->createIfActive($carrierCode, $request->getStoreId());
        if (!$carrier) {
            return $this;
        }
        $carrier->setActiveFlag($this->_availabilityConfigField);
        $result = $carrier->checkAvailableShipCountries($request);
        if (false !== $result && !$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Error) {
            $result = $carrier->proccessAdditionalValidation($request);
        }
        /*
         * Result will be false if the admin set not to show the shipping module
         * if the delivery country is not within specific countries
         */
        if (false !== $result) {
            if (!$result instanceof \Magento\Quote\Model\Quote\Address\RateResult\Error) {
                if ($carrier->getConfigData('shipment_requesttype')) {
                    $packages = $this->composePackagesForCarrier($carrier, $request);
                    if (!empty($packages)) {
                        $sumResults = [];
                        foreach ($packages as $weight => $packageCount) {
                            $request->setPackageWeight($weight);
                            $result = $carrier->collectRates($request);
                            if (!$result) {
                                return $this;
                            } else {
                                $result->updateRatePrice($packageCount);
                            }
                            $sumResults[] = $result;
                        }
                        if (!empty($sumResults) && count($sumResults) > 1) {
                            $result = [];
                            foreach ($sumResults as $res) {
                                if (empty($result)) {
                                    $result = $res;
                                    continue;
                                }
                                foreach ($res->getAllRates() as $method) {
                                    foreach ($result->getAllRates() as $resultMethod) {
                                        if ($method->getMethod() == $resultMethod->getMethod()) {
                                            $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                            continue;
                                        }
                                    }
                                }
                            }
                        }
                    } else {
                        $result = $carrier->collectRates($request);
                    }
                } else {
                    $result = $carrier->collectRates($request);
                }
                if (!$result) {
                    return $this;
                }
            }
            if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                return $this;
            }
            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice') && is_callable([$result, 'sortRatesByPrice'])) {
                $result->sortRatesByPrice();
            }
            $this->appendResults($carrier, $request, $result);
        }
        return $this;
    }
}