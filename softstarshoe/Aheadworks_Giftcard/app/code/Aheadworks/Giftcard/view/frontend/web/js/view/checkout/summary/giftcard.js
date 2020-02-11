/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/totals',
    'Aheadworks_Giftcard/js/action/remove-giftcard-code',
], function ($, Component, totals, removeAction) {
    'use strict';

    var giftcardRemoveUrl = window.checkoutConfig.awGiftcard.removeUrl;

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Giftcard/checkout/summary/giftcard'
        },
        isAjaxRemoveLink: true,
        code: 'aw_giftcard',
        totals: totals.totals(),

        /**
         * Is display Gift Card totals
         *
         * @return {boolean}
         */
        isDisplayed: function() {
            return this.isFullMode() && this.totals
                && totals.getSegment(this.code) && totals.getSegment(this.code).value != 0;
        },

        /**
         * Retrieve applied Gift Card codes
         *
         * @returns {Array}
         */
        getGiftcardCodes: function () {
            if (this.totals && totals.getSegment(this.code)) {
                var giftcardCodes = totals.getSegment(this.code).extension_attributes.aw_giftcard_codes;
                giftcardCodes.forEach(function(giftcardCode, index) {
                    if (typeof giftcardCode == 'string') {
                        giftcardCodes[index] = JSON.parse(giftcardCode);
                    }
                });
                return giftcardCodes;
            }
            return [];
        },

        /**
         * Retrieve formatted value
         *
         * @param {Number} value
         * @returns {String}
         */
        getValue: function (value) {
            return this.getFormattedPrice(value);
        },

        /**
         * Remove Gift Card by code
         *
         * @param {String} form
         */
        removeByCode: function (form) {
            if (this.isAjaxRemoveLink) {
                var giftcardCode = this._getGiftCardCodeFromForm(form);

                removeAction(giftcardCode)
            } else {
                $(form).attr('action', giftcardRemoveUrl);
                return true;
            }
        },

        /**
         * Retrieve Gift Card code from form
         *
         * @param {String} form
         * @return {String|null}
         */
        _getGiftCardCodeFromForm: function(form) {
            var formDataArray = $(form).serializeArray(),
                giftcardCode = null;

            formDataArray.forEach(function (entry) {
                if (entry.name === 'code') {
                    giftcardCode = entry.value;
                }
            });

            return giftcardCode;
        }
    });
});
