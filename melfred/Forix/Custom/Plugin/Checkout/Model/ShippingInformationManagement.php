<?php

namespace Forix\Custom\Plugin\Checkout\Model;

class ShippingInformationManagement
{
    public function beforeSaveAddressInformation(\Magento\Checkout\Api\ShippingInformationManagementInterface $subject, $cartId, \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation)
    {
        $billingAddress = $addressInformation->getBillingAddress();
        $data = $billingAddress->getData();
        if(!isset($data['region_id'])){
            $billingAddress->setRegionId(null);
        }
        $shippingAddress = $addressInformation->getShippingAddress();
        $data = $shippingAddress->getData();
        if(!isset($data['region_id'])){
            $shippingAddress->setRegionId(null);
        }

        return [$cartId, $addressInformation];
    }
}