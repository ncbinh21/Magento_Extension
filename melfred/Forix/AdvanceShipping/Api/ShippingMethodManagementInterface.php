<?php
/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 06/07/2018
 * Time: 17:28
 */
namespace Forix\AdvanceShipping\Api;
use Magento\Quote\Api\Data\AddressExtensionInterface;

interface ShippingMethodManagementInterface extends \Temando\Shipping\Api\Quote\ShippingMethodManagementInterface
{
    /**
     * Estimate shipping
     *
     * @param int $cartId The shopping cart ID.
     * @param \Magento\Quote\Api\Data\EstimateAddressInterface $address The estimate address
     * @return \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     * @deprecated 100.0.7
     */
    public function estimateByAddress($cartId, \Magento\Quote\Api\Data\EstimateAddressInterface $address);

    /**
     * Estimate shipping
     *
     * @param int $cartId The shopping cart ID.
     * @param int $addressId The estimate address id
     * @param \Magento\Quote\Api\Data\AddressExtensionInterface|null $extensionAttributes
     * @return \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     */
    public function estimateByAddressId($cartId, $addressId, AddressExtensionInterface $extensionAttributes = null);

    /**
     * Lists applicable shipping methods for a specified quote.
     *
     * @param int $cartId The shopping cart ID.
     * @return \Forix\AdvanceShipping\Api\Data\ShippingMethodInterface[] An array of shipping methods.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified quote does not exist.
     * @throws \Magento\Framework\Exception\StateException The shipping address is not set.
     */
    public function getList($cartId);
}