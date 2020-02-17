<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 17:37
 */

namespace Forix\AdvanceShipping\Api;


interface ShipmentEstimationInterface
{
    /**
     * Estimate shipping by address and return list of available shipping methods
     * @param mixed $cartId
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @return \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface[] An array of shipping methods
     * @since 100.0.7
     */
    public function estimateByExtendedAddress($cartId, \Magento\Quote\Api\Data\AddressInterface $address);
}