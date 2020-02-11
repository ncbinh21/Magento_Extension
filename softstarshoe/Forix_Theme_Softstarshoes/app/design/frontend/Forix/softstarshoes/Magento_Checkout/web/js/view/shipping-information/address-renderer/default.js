/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-information/address-renderer/default'
        },
        /**
         * Map Fullname payment ship to
         */
        // getFullName: function(name, result) {
        //     var customer = checkoutConfig.customerData.addresses;
        //     if(!name[result]) {
        //         if (customer) {
        //             for (var i = 0; i < customer.length; i++) {
        //                 if (customer[i].id == name.customerAddressId) {
        //                     return customer[i][result];
        //                 }
        //             }
        //         }
        //         if (name.customAttributes) {
        //             if (name.customAttributes.full_name) {
        //                 var fullName = name.customAttributes.full_name.trim().split(' ');
        //                 var firstname = fullName[0];
        //                 var lastname = name.customAttributes.full_name.replace(fullName[0], '').trim();
        //                 switch (result) {
        //                     case 'firstname':
        //                         name[result] = firstname;
        //                         break;
        //                     case 'lastname':
        //                         name[result] = lastname;
        //                         break;
        //                     default:
        //                         name[result] = '';
        //                 }
        //             }
        //         }
        //     }
        //     return name[result];
        // },
        /**
         * @param {*} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        }
    });
});
