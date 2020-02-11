/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'underscore',
    'forix/sidebarExt',
    'forix/scroll',
    'mage/translate'
], function (Component, customerData, $, ko, _) {
    'use strict';

    var sidebarInitialized = false,
        addToCartCalls = 0,
        miniCart,
        miniOptions;

    miniCart = $('[data-block=\'minicart\']');

    // initSidebar();

    /**
     * @return {Boolean}
     */
    function initSidebar() {
        if (miniCart.data('forixSidebarExt')) {
            miniCart.sidebarExt('update');
        }

        if (!$('[data-role=product-item]').length) {
            return false;
        }

        miniCart.trigger('contentUpdated');

        if (sidebarInitialized) {
            // counter Number on mini cart
            var countNumber = parseInt(miniCart.attr('data-counter'));
            if(!miniCart.attr('data-loader')){
                countNumber++;
            }else{
                miniCart.removeAttr('data-loader');
                if(countNumber == 0)
                    countNumber = 1 ;
                countNumber++;
            }

            miniCart.attr("data-counter",countNumber);
            initScroll(countNumber);
            return false;
        }
        sidebarInitialized = true;
        miniCart.sidebarExt({
            'targetElement': 'div.block.block-minicart',
            'url': {
                'checkout': window.checkout.checkoutUrl,
                'update': window.checkout.updateItemQtyUrl,
                'remove': window.checkout.removeItemUrl,
                'loginUrl': window.checkout.customerLoginUrl,
                'isRedirectRequired': window.checkout.isRedirectRequired
            },
            'button': {
                'checkout': '#top-cart-btn-checkout',
                'remove': '#mini-cart a.action.delete',
                'close': '#btn-minicart-close'
            },
            'showcart': {
                'parent': 'span.counter',
                'qty': 'span.counter-number',
                'label': 'span.counter-label'
            },
            'minicart': {
                'list': '#mini-cart',
                'content': '#minicart-content-wrapper',
                'qty': 'div.items-total',
                'subtotal': 'div.subtotal span.price',
                'maxItemsVisible': window.checkout.minicartMaxItemsVisible
            },
            'item': {
                'qty': ':input.cart-item-qty',
                'button': ':button.update-cart-item'
            },
            'confirmMessage': $.mage.__('Are you sure you would like to remove this item from the shopping cart?'),
            'afterInit': function(){
                miniCart.attr("data-loader",true);
            }
        });
        // counter Number on mini cart
        var lengthA=miniCart.find(".product-item").length;
        miniCart.attr("data-counter",0);
        initScroll(lengthA);
        var checkExist = setInterval(function() {
            if ($('.block-minicart .product-image-container .product-image-wrapper').length) {
                miniCart.trigger('loadImageSuccess');
                clearInterval(checkExist);
            }
        }, 100);
    }

    function initScroll(index){
        var countEle = miniCart.attr("data-counter");
        var setNumber = window.checkout.minicartMaxItemsVisible;

        if(countEle > setNumber){
            var countCheck = parseInt(countEle);
            var setNuberEle = miniCart.find('.minicart-items-wrapper').attr('data-set');

            if(setNuberEle){
                setNumber = parseInt(setNuberEle);
            }

            if( countCheck == index && index > setNumber){
                miniCart.find('.minicart-items-wrapper').addClass("have-scroll");
                miniCart.scrollData({
                    'eleScroll': "[data-action=scroll]",
                    'item': "[data-role=product-item]",
                    'numberScroll': setNumber,
                    deplay: 400
                });
            }

            // Fix some case scroll don't cal Height correct
            setTimeout(function () {
                if (miniCart.data('forixScrollData')) {
                    miniCart.scrollData('updateScroll');
                }
            },400);
        }else{
            if (miniCart.data('forixScrollData')) {
                miniCart.find('.minicart-items-wrapper').removeClass("have-scroll");
                miniCart.scrollData('destroy');
            }
        }
    }

    initSidebar();

    return Component.extend({
        shoppingCartUrl: window.checkout.shoppingCartUrl,
        cart: {},
        maxItemsToDisplay: window.checkout.maxItemsToDisplay,
        /**
         * @override
         */
        initialize: function () {
            var self = this,
                cartData = customerData.get('cart');

            this.update(cartData());

            cartData.subscribe(function (updatedCart) {
                addToCartCalls--;
                this.isLoading(addToCartCalls > 0);
                sidebarInitialized = false;
                this.update(updatedCart);
                initSidebar();

                var lengthA=miniCart.find(".product-item").length;
                initScroll(lengthA);

            }, this);

            $('[data-block="minicart"]').on('contentLoading', function () {
                addToCartCalls++;
                self.isLoading(true);
            });

            if (cartData()['website_id'] !== window.checkout.websiteId) {
                customerData.reload(['cart'], false);
            }
            return this._super();
        },
        isLoading: ko.observable(false),
        initSidebar: initSidebar,

        /**
         * Close mini shopping cart.
         */
        closeMinicart: function () {
            // $('[data-block="minicart"]').find('[data-role="dropdownDialog"]').dropdownDialog('close');
        },

        /**
         * @return {Boolean}
         */
        closeSidebar: function () {
            var minicart = $('[data-block="minicart"]');

            minicart.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                // minicart.find('[data-role="dropdownDialog"]').dropdownDialog('close');
            });

            return true;
        },

        /**
         * @param {String} productType
         * @return {*|String}
         */
        getItemRenderer: function (productType) {
            return this.itemRenderer[productType] || 'defaultRenderer';
        },

        /**
         * Update mini shopping cart content.
         *
         * @param {Object} updatedCart
         * @returns void
         */
        update: function (updatedCart) {
            _.each(updatedCart, function (value, key) {
                if (!this.cart.hasOwnProperty(key)) {
                    this.cart[key] = ko.observable();
                }
                this.cart[key](value);
            }, this);
        },

        /**
         * Get cart param by name.
         * @param {String} name
         * @returns {*}
         */
        getCartParam: function (name) {
            if (!_.isUndefined(name)) {
                if (!this.cart.hasOwnProperty(name)) {
                    this.cart[name] = ko.observable();
                }
            }

            return this.cart[name]();
        },

        /**
         * Get length for items
         * @param {String} name
         * @returns int
         */
        getCartParamLength: function(name){
            if (!_.isUndefined(name)) {
                if (!this.cart.hasOwnProperty(name)) {
                    this.cart[name] = ko.observable();
                }
            }
            if(this.cart[name]())
                return this.cart[name]().length;
        },
        /**
         * Returns array of cart items, limited by 'maxItemsToDisplay' setting
         * @returns []
         */
        getCartItems: function () {
            var items = this.getCartParam('items') || [];
            items = items.slice(parseInt(-this.maxItemsToDisplay, 10));

            return items;
        },

        /**
         * Returns count of cart line items
         * @returns {Number}
         */
        getCartLineItemsCount: function () {
            var items = this.getCartParam('items') || [];

            return parseInt(items.length, 10);
        }
    });
});
