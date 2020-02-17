<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Forix\Custom\Rewrite\Model\Plugin\Quote\Model\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\Framework\App\State;

/**
 * Plugin for class Address to apply negotiations shipping price and shipping method on shipping information.
 */
class AddressPlugin
{
    /**
     * @var \Magento\Framework\App\State
     */
    private $appState;

    /**
     * @param State $appState
     */
    public function __construct(State $appState)
    {
        $this->appState = $appState;
    }

    /**
     * Set shipping price and shipping method after address request shipping rates.
     *
     * @param Address $address
     * @param bool $result
     * @return bool
     */
    public function afterRequestShippingRates(Address $address, $result)
    {
        if (!$result) {
            return $result;
        }
        $quote = $address->getQuote();
        if (!($quote->getExtensionAttributes() && $quote->getExtensionAttributes()->getNegotiableQuote() && $quote->getExtensionAttributes()->getNegotiableQuote()->getQuoteId())) {
            return $result;
        }
        $originalPrice = $quote->getExtensionAttributes()->getNegotiableQuote()->getShippingPrice();
        foreach ($address->getAllShippingRates() as $rate) {
            $rate->setData('original_price', $rate->getPrice());
            /** @var $rate \Magento\Quote\Model\Quote\Address\Rate */
            if ($rate->getCode() == $address->getShippingMethod()) {
                if (!isset($originalPrice)) {
                    $rate->setPrice($rate->getData('original_price'));
                } else {
                    $rate->setPrice($originalPrice);
                }
            } else {
                if (\Magento\Framework\App\Area::AREA_ADMINHTML != $this->appState->getAreaCode()) {
                    $rate->isDeleted(true);
                }
            }
        }
        $address->setShippingAmount($originalPrice)->setBaseShippingAmount($originalPrice);

        return $result;
    }
}
