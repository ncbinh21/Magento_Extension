/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'ko',
    'uiComponent',
    'Aheadworks_Giftcard/js/model/customer/giftcard',
    'Aheadworks_Giftcard/js/action/apply-giftcard-code',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Customer/js/model/customer',
    'Aheadworks_Giftcard/js/action/get-customer-giftcards'
], function ($, ko, Component, customerGiftcard, applyAction, quote, priceUtils, customer, getCustomerGiftcardsAction) {
    'use strict';

    var giftcardCode = ko.observable(null);

    /**
     * Add Gift Card code to quote
     *
     * @param {String} giftcardCode
     */
    function addGiftcardCode(giftcardCode) {
        applyAction(giftcardCode);
    }

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Giftcard/payment/giftcard'
        },

        /**
         * Gift Card code
         */
        giftcardCode: giftcardCode,

        /**
         * {@inheritdoc}
         */
        initialize: function () {
            this._super();
            getCustomerGiftcardsAction();
        },

        /**
         * Retrieve customer Gift Card codes
         *
         * @return {boolean}
         */
        getCustomerGiftcardCodes: function() {
            return customerGiftcard.giftcardCodes();
        },

        /**
         * Is customer Gift Card codes displayed
         *
         * @return {boolean}
         */
        isCustomerGiftcardCodesDisplayed: function() {
            return customer.isLoggedIn() && this.getCustomerGiftcardCodes().length > 0;
        },

        /**
         * Is loading customer gift card codes block
         *
         * @return {boolean}
         */
        isLoadingCustomerGiftcardsBlock: function() {
            return customerGiftcard.isLoading();
        },

        /**
         * Format Gift Card price
         *
         * @returns {String}
         */
        formatPrice: function(amount) {
            return priceUtils.formatPrice(amount, quote.getPriceFormat());
        },

        /**
         * Apply Gift Card
         */
        apply: function() {
            if (this.validate()) {
                addGiftcardCode(giftcardCode());
            }
        },

        /**
         * Apply Gift Card by code
         *
         * @param {String} giftcardCode
         */
        applyByCode: function(giftcardCode) {
            addGiftcardCode(giftcardCode);
        },

        /**
         * Gift Card form validation
         *
         * @returns {Boolean}
         */
        validate: function() {
            var form = '#aw-giftcard-form';

            return $(form).validation() && $(form).validation('isValid');
        }
    });
});
