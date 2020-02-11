define([
    'jquery',
    'underscore'
], function ($, _) {
    "use strict";

    $.widget('orange35.imageConstructorMini', {
        options: {
            paypal: ".paypal-review-items",
            productPaypal: "tbody.cart",
            restriction: ".product-restriction",
            productRestriction: ".product-item",
            cartSelector: "[data-block='minicart']",
            cartItemSelector: ".minicart-items .product-item",
            productImageWrapperSelector: ".product-image-container .product-image-wrapper",
            editActionSelector : ".action.edit"
        },
        pattern: /(?:\/id\/)(\d+)\//,
        chosenImages: {},
        ajaxObject: null,

        _create: function () {
            var _this = this;

            _this.applyLayersToItems();
            $(this.options.cartSelector).on('loadImageSuccess', function () {
                _this.loadAjax('mini_cart/').done(function (result) {
                    _this.options.items = result.items;
                    _this.applyLayersToItems();
                });
            });
        },

        applyLayersToItems: function () {
            var _this = this;
            if(this.options.items !== undefined){
                var itemHolders = $(this.options.cartSelector).find(this.options.cartItemSelector);
                if (!_.isUndefined(itemHolders)) {
                    itemHolders.each(function () {
                        _this.applyLayers(this, '');
                    })
                }
                var productRestriction = $(this.options.restriction).find(this.options.productRestriction);
                if (!_.isUndefined(productRestriction)) {
                    productRestriction.each(function () {
                        _this.applyLayers(this, 'restriction');
                    })
                }
                var productPaypal = $(this.options.paypal).find(this.options.productPaypal);
                if (!_.isUndefined(productPaypal)) {
                    productPaypal.each(function () {
                        _this.applyLayers(this, 'paypal');
                    })
                }
            }
        },

        applyLayers: function (itemHolder, type) {

            var itemImageHolder = $(itemHolder).find(this.options.productImageWrapperSelector);
            var imageHolderStyle = {
                position: "relative",
                top: 0,
                left: 0
            };
            itemImageHolder.css(imageHolderStyle);
            $(itemHolder).find(this.options.productImageWrapperSelector + ' img').slice(1).remove();
            var itemImageBlock = $(itemHolder).find(this.options.productImageWrapperSelector + ' img');
            var imageStyle = {
                position: "relative",
                top: 0,
                left: 0
            };
            $(itemImageBlock).css(imageStyle);
            var idProduct = this.getItemId(itemHolder);
            if(!idProduct) {
                idProduct = itemHolder.dataset.id;
            }
            var layers = this.getLayersById(idProduct);
            if(type == 'restriction') {
                layers = this.getLayersById(itemHolder.id);
            }

            _.each(layers, function (layer) {
                itemImageHolder.append(itemImageBlock.clone().attr('src', layer['url']).css("position", "absolute"));
            })

        },

        getLayersById: function (itemId) {
            if(itemId){
                if (!_.isUndefined(this.options.items[itemId])) {
                    return this.options.items[itemId].layers;
                } else {
                    return false;
                }
            }
            return false;
        },

        getItemId: function (itemHolder) {
            var itemEditUrl = $(itemHolder).find(this.options.editActionSelector).attr('href');
            var itemEditUrlCart = $(itemHolder).closest('.cart.item').find('.item-actions .action-edit').attr('href');
            if(this.pattern.exec(itemEditUrl)) {
                return this.pattern.exec(itemEditUrl)[1];
            }
            else if(this.pattern.exec(itemEditUrlCart)) {
                return this.pattern.exec(itemEditUrlCart)[1];
            }
            else {
                return false;
            }
        },

        loadAjax: function (group) {
            if(this.ajaxObject){
                this.ajaxObject.abort();
            }
            return this.ajaxObject = $.ajax({
                url: window.location.origin + '/catalog/loadimage/ajax',
                type: 'post',
                data: {
                    cardId: '1',
                    group: group
                },
                dataType: 'json',
            });
        },

    });

    return $.orange35.imageConstructorMini;
});
