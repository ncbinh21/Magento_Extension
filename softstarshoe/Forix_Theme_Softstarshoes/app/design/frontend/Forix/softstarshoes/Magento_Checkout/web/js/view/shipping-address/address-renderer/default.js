/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-address/form-popup-state',
    'Magento_Checkout/js/checkout-data',
    'Magento_Customer/js/customer-data'
], function ($, ko, Component, selectShippingAddressAction, quote, formPopUpState, checkoutData, customerData) {
    'use strict';

    var countryData = customerData.get('directory-data');

    return Component.extend({
        defaults: {
            template: 'Magento_Checkout/shipping-address/address-renderer/default'
        },

        /** @inheritdoc */
        initObservable: function () {
            this._super();
            this.isSelected = ko.computed(function () {
                var isSelected = false,
                    shippingAddress = quote.shippingAddress();

                if (shippingAddress) {
                    isSelected = shippingAddress.getKey() == this.address().getKey(); //eslint-disable-line eqeqeq
                }

                return isSelected;
            }, this);

            return this;
        },

        /**
         * @param {String} countryId
         * @return {String}
         */
        getCountryName: function (countryId) {
            return countryData()[countryId] != undefined ? countryData()[countryId].name : ''; //eslint-disable-line
        },

        /**
         * Map Fullname create new address (already address and create new)
         */
        // getFullName: function(name, result) {
        //     if(name.customAttributes && !name[result]) {
        //         if(name.customAttributes.full_name){
        //             var fullName = name.customAttributes.full_name.trim().split(' ');
        //             var firstname = fullName[0];
        //             var lastname = name.customAttributes.full_name.replace(fullName[0], '').trim();
        //             switch(result) {
        //                 case 'firstname':
        //                     name[result] = firstname;
        //                     break;
        //                 case 'lastname':
        //                     name[result] = lastname;
        //                     break;
        //                 default:
        //                     name[result] = '';
        //             }
        //         }
        //     }
        //     return name[result];
        // },

        /** Set selected customer shipping address  */
        selectAddress: function () {
            selectShippingAddressAction(this.address());
            var listItems = window.checkoutConfig.totalsData.items;
            $('.product-restriction').addClass("no-display");
            $('#opc-shipping_method').removeClass("no-display");
            if (this.address().countryId != 'US' && this.address().countryId != 'CA') {
                if (listItems && listItems.length > 0) {
                    for (var i = 0; i < listItems.length; i++) {
                        if (listItems[i].extension_attributes.product_restriction == '1') {
                            $('#opc-shipping_method').addClass("no-display");
                            $('.product-restriction').removeClass("no-display");
                            break;
                        }
                    }
                }
            }
            checkoutData.setSelectedShippingAddress(this.address().getKey());
        },

        /**
         * Edit address.
         */
        editAddress: function () {
            formPopUpState.isVisible(true);
            this.showPopup();

        },

        /**
         * Show popup.
         */
        showPopup: function () {
            $('[data-open-modal="opc-new-shipping-address"]').trigger('click');
        }
    });
});
