var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/place-order': {
                'Forix_OrderComments/js/model/place-order-with-comments-mixin': true
            },
            'Magento_Paypal/js/view/payment/method-renderer/paypal-express-abstract': {
                'Forix_OrderComments/js/view/payment/method-renderer/paypal-express-abstract-mixin': true
            }
        }
    }
};