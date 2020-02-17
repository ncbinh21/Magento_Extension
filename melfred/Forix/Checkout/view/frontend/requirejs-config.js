var config = {
    map: {
        "*": {
            "Magento_Ui/js/form/element/post-code": 'Forix_Checkout/js/form/element/post-code',
            'Magento_Checkout/js/model/postcode-validator':'Forix_Checkout/js/model/postcode-validator'
        }
    },

    config: {
        mixins: {
            'Magento_Checkout/js/shopping-cart': {
                'Forix_Checkout/js/shopping-cart-mixin': true
            },
            'Magento_Ui/js/form/components/group': {
                'Forix_Checkout/js/address/street/moreline':true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Forix_Checkout/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/view/billing-address': {
                'Forix_Checkout/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Forix_Checkout/js/view/shipping-mixin': true
            }
        }
    }
};