<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Forix\AdvancedPaypal\Rewrite\Magento\Paypal\Model\Express;

use Magento\Paypal\Model\Config as PaypalConfig;
use Magento\Quote\Model\Quote\Address;
use Magento\Framework\DataObject;

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Checkout extends \Magento\Paypal\Model\Express\Checkout
{

    /**
     * Sets address data from exported address
     *
     * @param Address $address
     * @param array $exportedAddress
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _setExportedAddressData($address, $exportedAddress)
    {
        // Exported data is more priority if we came from Express Checkout button
        $isButton  = (bool)$this->_quote->getPayment()->getAdditionalInformation(self::PAYMENT_INFO_BUTTON);
        if (!$isButton) {
            foreach ($exportedAddress->getExportedKeys() as $key) {
                $oldData = $address->getDataUsingMethod($key);
                $isEmpty = null;
                if (is_array($oldData)) {
                    foreach ($oldData as $val) {
                        if (!empty($val)) {
                            $isEmpty = false;
                            break;
                        }
                        $isEmpty = true;
                    }
                }
                if (empty($oldData) || $isEmpty === true) {
                    if($key == 'street'){
                        $street = $exportedAddress->getData('street');
                        if($exportedAddress->getData('street2')) {
                            $street = $exportedAddress->getData('street') . PHP_EOL  . $exportedAddress->getData('street2');
                        }
                        $address->setDataUsingMethod('street', $street);
                    } else {
                        $address->setDataUsingMethod($key, $exportedAddress->getData($key));
                    }
                }
            }
        } else {
            foreach ($exportedAddress->getExportedKeys() as $key) {
                if($key == 'street'){
                    $data = $exportedAddress->getData('street');
                    if($exportedAddress->getData('street2')) {
                        $data = $exportedAddress->getData('street') . PHP_EOL . $exportedAddress->getData('street2');
                    }
                } else {
                    $data = $exportedAddress->getData($key);
                }
                if (!empty($data)) {
                    $address->setDataUsingMethod($key, $data);
                }
            }
        }
    }
}
