<?php
/**
 * Created by Forix.
 * User: Johnny
 * Date: 8/17/2017
 * Time: 3:53 PM
 */
namespace Forix\CustomShipping\Plugin\Magento\Checkout;
class LayoutProcessor {
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array  $jsLayout
    ) {
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['firstname']['sortOrder']=16;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['lastname']['sortOrder']=17;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['company']['sortOrder']=18;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['sortOrder']=19;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['sortOrder']=20;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['sortOrder']=21;

        $postcodeField = [
            'component' => 'Forix_CustomShipping/js/autocomplete',
            'config' => [
                // customScope is used to group elements within a single form (e.g. they can be validated separately)
                'customScope' => 'shippingAddress',
                'template' => 'ui/form/field',
                'elementTmpl' => 'Forix_CustomShipping/autocomplete/postcodeInput',
                'id' => 'postcode',
            ],
            'dataScope' => 'shippingAddress.postcode',
            'label' => 'Zip Code',
            'provider' => 'checkoutProvider',
            'sortOrder' => 20,
            'validation' => [
                'required-entry' => true
            ],
            'options' => [],
            'visible' => true,
            'id' => 'postcode'
        ];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode'] = $postcodeField;

        $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];

        foreach ($paymentForms as $paymentGroup => $groupConfig) {
            if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {

                //Zip code
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['postcode']['component'] = 'Forix_CustomShipping/js/autocomplete_billing';

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['postcode']['config']['elementTmpl'] = 'Forix_CustomShipping/autocomplete/postcodeInputBilling';

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['postcode']['sortOrder'] = 100;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['postcode']['label'] = "Zip Code";

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city']['sortOrder'] = 101;
            }
        }

        return $jsLayout;
    }
}