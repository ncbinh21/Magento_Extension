<?php

namespace Forix\Checkout\Plugin\Checkout;

class LayoutProcessor
{
    protected $_session;

    public function __construct(
    	\Magento\Customer\Model\Session $session,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_session = $session;
	    $this->scopeConfig = $scopeConfig;
    }


    public function beforeProcess(\Magento\Checkout\Block\Checkout\LayoutProcessor $layoutProcessor, $jsLayout)
    {
	    if ($this->scopeConfig->getValue('payment/free/active') == 0)
		    unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['renders']['children']['free-payments']);
	    if ($this->scopeConfig->getValue('payment/banktransfer/active') == 0)
		    unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['renders']['children']['offline-payments']['methods']['banktransfer']);
	    if ($this->scopeConfig->getValue('payment/cashondelivery/active') == 0)
		    unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['renders']['children']['offline-payments']['methods']['cashondelivery']);
	    if ($this->scopeConfig->getValue('payment/purchaseorder/active') == 0)
		    unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['renders']['children']['offline-payments']['methods']['purchaseorder']);
	    if ($this->scopeConfig->getValue('payment/checkmo/active') == 0)
		    unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['renders']['children']['offline-payments']['methods']['checkmo']);

	    return [$jsLayout];
    }

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    )
    {
        $customAttributeCode = 'fullname';
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['config']['template'] = 'Forix_Checkout/address/street/moreline';
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['street']['children'][1]['visible'] = 0;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['telephone']['sortOrder'] = 4;

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['country_id']['sortOrder'] = 90;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['postcode']['sortOrder'] = 95;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['city']['sortOrder'] = 100;
        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']['region_id']['sortOrder'] = 105;

        $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'];

        $jsLayout['components']['checkout']['children']['steps']['children']['shipping-step']['children']
        ['shippingAddress']['children']['shipping-address-fieldset']['children']['fullname'] =
            [
                'component' => 'Forix_Checkout/js/form/element/fullname',
                'config' => [
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'options' => [],
                    'id' => 'full_name',
                    'customScope' => 'shippingAddress',
                ],
                'dataScope' => 'shippingAddress.custom_attributes.fullname',
                'dataScopePrefix' => 'shippingAddress.fullname',
            'label' => "Full name",
            'provider' => 'checkoutProvider',
            'visible' => true,
            'validation' => ['required-entry' => true],
            'sortOrder' => 3,
            'value' => $this->_getCustomizeShippingAddressLoggedinCustomerFullname(),
            'id' => $customAttributeCode
        ];

        foreach ($paymentForms as $paymentGroup => $groupConfig) {
            if (isset($groupConfig['component']) AND $groupConfig['component'] === 'Magento_Checkout/js/view/billing-address') {
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children'][$customAttributeCode] = [
                    'component' => 'Forix_Checkout/js/form/element/fullname',
                    'config' => [
                        'template' => 'ui/form/field',
                        'elementTmpl' => 'ui/form/element/input',
                        'options' => [],
                        'id' => 'full_name',
                        'customScope' => 'billingAddress',
                    ],
                    'dataScope' => $groupConfig['dataScopePrefix'] . '.custom_attributes.' . $customAttributeCode,
                    'dataScopePrefix' => 'billingAddress' . $customAttributeCode,
                    'label' => "Full name",
                    'provider' => 'checkoutProvider',
                    'visible' => true,
                    'validation' => ['required-entry' => true],
                    'sortOrder' => 3,
                    'value' => $this->_getCustomizeShippingAddressLoggedinCustomerFullname(),
                    'id' => $customAttributeCode
                ];

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['postcode']['config']['elementTmpl'] = 'Forix_Checkout/address/autocomplete/postcode';



                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']
                ['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']
                ['form-fields']['children']['postcode']['label'] = "Zip/Postal Code";

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['country_id']['sortOrder'] = 99;
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['postcode']['sortOrder'] = 100;
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['city']['sortOrder'] = 101;
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['region_id']['sortOrder'] = 102;

                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['street']['config']['template'] = 'Forix_Checkout/address/street/moreline';
                $jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']
                ['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['street']['children'][1]['visible'] = 0;
            }

            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['firstname']);
            unset($jsLayout['components']['checkout']['children']['steps']['children']['billing-step']['children']['payment']['children']['payments-list']['children'][$paymentGroup]['children']['form-fields']['children']['lastname']);
        }

        return $jsLayout;
    }

    /**
     * get logged in customer fullname
     * @return null|string
     */
    private function _getCustomizeShippingAddressLoggedinCustomerFullname()
    {
        $customerSession = $this->_session->getCustomer();
        $isLogged = $this->_session->isLoggedIn();
        $cuFirstName = trim($customerSession->getData('firstname'));
        $cuMidName = trim($customerSession->getData('middlename'));
        $cuLastName = trim($customerSession->getData('lastname'));
        $fullName = join(' ', array_filter([$cuFirstName, $cuMidName, $cuLastName]));
        return $isLogged ? $fullName : '';
    }
}