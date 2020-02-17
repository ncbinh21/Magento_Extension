define(
    [
        'Magento_Checkout/js/view/payment/default'
    ],
    function (Component) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Forix_NetTermsPayment/payment/netterms'
            },

            initialize: function () {
                this._super();
                jQuery.cookie('redirect_checkout', 1);
                // jQuery.cookie("redirect_checkout", null, { path: '/' });
            },

            urlCustomerLogin: function () {
                return window.location.origin + '/customer/account/login';
            },

            urlRegisterNetterm: function () {
                return window.location.origin + '/netterm';
            },

            isNetterms: function () {
                var netterms = window.checkoutConfig.is_netterms;
                jQuery('.button-apply.login-netterms').click(function () {
                    jQuery.cookie('redirect_checkout', 1);
                })
                return netterms;
            },

            /** Returns send check to info */
            getMailingAddress: function() {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
        });
    }
);