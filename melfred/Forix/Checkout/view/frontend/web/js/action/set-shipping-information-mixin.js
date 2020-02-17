/**
 * @author Johnny
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, checkoutData, quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction, messageContainer) {

            var shippingAddress = quote.shippingAddress();
            if (shippingAddress['customAttributes'] === undefined) {
                shippingAddress['customAttributes'] = {};
            }
            // you can extract value of extension attribute from any place (in this example I use customAttributes approach)
            if(shippingAddress.customAttributes['fullname'] !== undefined){
                shippingAddress['customAttributes']['fullname'] = shippingAddress.customAttributes['fullname'];
                var aFullName = shippingAddress.customAttributes['fullname'].trim().split(' ');
                if(aFullName[0] !== undefined)
                    shippingAddress['firstname'] = aFullName[0];
                if(aFullName[1] !== undefined)
                    shippingAddress['lastname'] = shippingAddress.customAttributes['fullname'].replace(aFullName[0],'').trim();
                var checkoutShippingData = checkoutData.getShippingAddressFromData();
                if(undefined !== checkoutShippingData.custom_attributes && undefined !== checkoutShippingData.custom_attributes.fullname){
                    checkoutShippingData.firstname = shippingAddress['firstname'];
                    checkoutShippingData.lastname = shippingAddress['lastname'];
                    // checkoutShippingData.custom_attributes.fullname = '';
                    checkoutData.setShippingAddressFromData(checkoutShippingData);
                }
                shippingAddress.customAttributes['fullname'] = '';
                quote.billingAddress(quote.shippingAddress());
            }

            // pass execution to original action ('Magento_Checkout/js/action/set-shipping-information')
            return originalAction(messageContainer);
        });
    };
});