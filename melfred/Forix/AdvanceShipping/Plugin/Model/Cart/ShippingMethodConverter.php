<?php

/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 15:30
 */

namespace Forix\AdvanceShipping\Plugin\Model\Cart;

class ShippingMethodConverter
{
    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Converts a specified rate model to a shipping method data object.
     *
     * @param string $quoteCurrencyCode The quote currency code.
     * @param \Magento\Quote\Model\Quote\Address\Rate $rateModel The rate model.
     * @param \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface $result
     * @return \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface Shipping method data object.
     */
    public function afterModelToDataObject($subject, $result, $rateModel, $quoteCurrencyCode)
    {
        /**
         * @var $result \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface
         */
        $methodNotes = $this->_scopeConfig->getValue('shipping/advance_shipping/shipping_notes');
        if ($methodNotes) {
            $methodNotes = json_decode($methodNotes, true);
            if (is_array($methodNotes)) {
                foreach ($methodNotes as $methodNote) {
                    if ($methodNote['shipping_method'] === $rateModel->getCode()) {
                        $result->setMethodNote($methodNote['shipping_note']);
                        break;
                    }
                }
            }
        }
        return $result;
    }
}