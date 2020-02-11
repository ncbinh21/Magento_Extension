/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/url-builder',
    'mageUtils'
], function(customer, urlBuilder, utils) {
    "use strict";
    return {
        /**
         * Retrieve url for apply Gift Card code
         *
         * @param {String} giftcardCode
         * @param {Number} quoteId
         * @returns {String}
         */
        getApplyGiftcardUrl: function(giftcardCode, quoteId) {
            var params = (this.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {},
                urls = {
                    'guest': '/guest-carts/' + quoteId + '/aw-giftcard/' + giftcardCode,
                    'customer': '/carts/mine/aw-giftcard/' + giftcardCode
                };

            return this.getUrl(urls, params);
        },

        /**
         * Retrieve url for remove Gift Card code
         *
         * @param {String} giftcardCode
         * @param {Number} quoteId
         * @returns {String}
         */
        getRemoveGiftcardUrl: function(giftcardCode, quoteId) {
            var params = (this.getCheckoutMethod() == 'guest') ? {quoteId: quoteId} : {},
                urls = {
                    'guest': '/guest-carts/' + quoteId + '/aw-giftcard/' + giftcardCode,
                    'customer': '/carts/mine/aw-giftcard/' + giftcardCode
                };

            return this.getUrl(urls, params);
        },

        /**
         * Retrieve customer Gift Card codes
         *
         * @returns {String}
         */
        getCustomerGiftcardsUrl: function() {
            var params = {},
                urls = {
                    'customer': '/carts/mine/aw-giftcard-get-customer-codes/'
                };

            return this.getUrl(urls, params);
        },

        /**
         * Get url for service
         *
         * @returns {String}
         */
        getUrl: function(urls, urlParams) {
            var url;

            if (utils.isEmpty(urls)) {
                return 'Provided service call does not exist.';
            }

            if (!utils.isEmpty(urls['default'])) {
                url = urls['default'];
            } else {
                url = urls[this.getCheckoutMethod()];
            }
            return urlBuilder.createUrl(url, urlParams);
        },

        /**
         * Retrieve checkout method
         *
         * @returns {String}
         */
        getCheckoutMethod: function() {
            return customer.isLoggedIn() ? 'customer' : 'guest';
        }
    };
});
