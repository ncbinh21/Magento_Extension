/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global confirm:true*/
define([
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Ui/js/model/messageList'
    ],
    function ($, quote, checkoutData) {
        'use strict';
        var mixin = {
            initialize: function () {

                var shippingAddressData = checkoutData.getShippingAddressFromData();

                if (shippingAddressData) {
                    if (undefined == shippingAddressData.postcode) {
                        shippingAddressData.postcode = '';
                    }
                }
                checkoutData.setShippingAddressFromData(shippingAddressData);
                return this._super();
            }
        };
        return function (target) {
            return target.extend(mixin);
        };
    });
