define([
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Mirasvit_Credit/js/action/apply-credit'
], function (ko, Component, quote, priceUtils, applyCreditAction) {
    'use strict';

    var amountUsed = ko.observable(window.checkoutConfig.creditConfig.amountUsed);
    var isApplied = ko.observable(amountUsed() > 0);
    var isLoading = ko.observable(false);
    var isAllowed = window.checkoutConfig.creditConfig.isAllowed;

    return Component.extend({
        defaults: {
            template: 'Mirasvit_Credit/payment/credit'
        },
        isLoading: isLoading,
        isApplied: isApplied,
        isAllowed: isAllowed,

        amount: window.checkoutConfig.creditConfig.amount,

        initObservable: function () {
            console.info(window.checkoutConfig.creditConfig);

            this._super();

            return this;
        },

        formatBalanceAmount: function () {
            return priceUtils.formatPrice(this.amount, quote.getPriceFormat());
        },

        applyCredit: function () {
            applyCreditAction(true, isLoading);

            this.isApplied(true);
        },

        cancelCredit: function () {
            applyCreditAction(false, isLoading);

            this.isApplied(false);
        }
    });
});
