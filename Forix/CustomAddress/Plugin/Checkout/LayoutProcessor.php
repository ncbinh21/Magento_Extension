<?php

namespace Forix\CustomAddress\Plugin\Checkout;

use \Magento\Checkout\Block\Checkout\LayoutProcessor as MageLayoutProcessor;

class LayoutProcessor
{
    protected $_customAttributeCode = 'license_number';

    public function afterProcess(MageLayoutProcessor $subject, $jsLayout)
    {
        if (isset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
            ['payment']['children']['payments-list']['children']))
        {
            foreach ($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'] as $key => $payment)
            {
                $paymentCode = 'billingAddress'.str_replace('-form','',$key);
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$key]['children']['form-fields']['children'][$this->_customAttributeCode] = $this->getLicenseNumberAttributeForAddress($paymentCode);
            }

        }

        if(isset($jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset'])
        ){
            $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']['shippingAddress']['children']['shipping-address-fieldset']['children'][$this->_customAttributeCode] = $this->getLicenseNumberAttributeForAddress('shippingAddress');
        }

        return $jsLayout;
    }

    public function getLicenseNumberAttributeForAddress($addressType)
    {
        return $customField = [
            'component' => 'Magento_Ui/js/form/element/abstract',
            'config' => [
                'customScope' => $addressType.'.custom_attributes',
                'customEntry' => null,
                'template' => 'ui/form/field',
                'elementTmpl' => 'ui/form/element/input'
            ],
            'dataScope' => $addressType.'.custom_attributes' . '.' . $this->_customAttributeCode,
            'label' => 'License Number',
            'provider' => 'checkoutProvider',
            'sortOrder' => 72,
            'validation' => [
                'required-entry' => false
            ],
            'options' => [],
            'filterBy' => null,
            'customEntry' => null,
            'visible' => true,
            'id' => $this->_customAttributeCode
        ];
    }
}