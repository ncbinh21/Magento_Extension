/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'Magento_Checkout/js/model/totals',
        'uiComponent',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote',
        'jquery'
    ],
    function (ko, totals, Component, stepNavigator, quote, $) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/cart-items'
            },
            totals: totals.totals(),
            items: ko.observable([]),
            maxCartItemsToDisplay: window.checkoutConfig.maxCartItemsToDisplay,
            cartUrl: window.checkoutConfig.cartUrl,
            
            /**
             * @deprecated Please use observable property (this.items())
             */             
            getItems: totals.getItems(),
            
            /**
             * Returns cart items qty.
             *
             * @returns {Number}
             */             
            getItemsQty: function() {
                return parseFloat(this.totals['items_qty']);
            },            
            
            /**
             * Returns count of cart line items.
             *
             * @returns {Number}
             */
            getCartLineItemsCount: function () {
                return parseInt(totals.getItems()().length, 10);
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();
                // Set initial items to observable field
                this.setItems(totals.getItems()());
                // Subscribe for items data changes and refresh items in view
                totals.getItems().subscribe(function (items) {
                    this.setItems(items);
                }.bind(this));
            },

            /**
             * Set items to observable field.
             *
             * @param {Object} items
             */
            setItems: function (items) {
                if (items && items.length > 0) {
                    items = items.slice(parseInt(-this.maxCartItemsToDisplay, 10));
                }
                this.items(items);
            },

            /**
             * Returns bool value for items block state (expanded or not).
             *
             * @returns {*|Boolean}
             */
            isItemsBlockExpanded: function () {
                return quote.isVirtual() || stepNavigator.isProcessed('shipping');
            },

            /**
             * Adding by Forix
             *
             * Update scroll when apply jscrollpane for minicart dropdown
             */
            showScroll: function(){
                var miniCart = $('[data-block=\'summary-minicart\']');
                if (miniCart.data('forixScrollData')) {
                    miniCart.scrollData('updateScroll');
                }
            }
        });
    }
);