/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'uiComponent',
        'jquery'
    ],
    function (Component, $) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/item/details'
            },

            /**
             * @param {Object} quoteItem
             * @return {String}
             */
            getValue: function(quoteItem) {
                return quoteItem.name;
            },

            updateScroll: function(){
                var miniCart = $('[data-block=\'summary-minicart\']');
                if (miniCart.data('forixScrollData')) {
                    miniCart.scrollData('updateScroll');
                }
            }
        });
    }
);
