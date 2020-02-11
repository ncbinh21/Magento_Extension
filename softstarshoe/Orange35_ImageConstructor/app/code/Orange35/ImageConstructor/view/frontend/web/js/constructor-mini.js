define([
    'jquery',
    'underscore',
], function ($, _) {
    "use strict";

    $.widget('orange35.imageConstructorMini', {
        options: {
            cartSelector: "[data-block='minicart']",
            cartItemSelector: ".minicart-items .product-item",
            productImageWrapperSelector: ".product-image-container .product-image-wrapper",
            editActionSelector : ".action.edit"
        },
        pattern: /(?:\/id\/)(\d+)\//,
        chosenImages: {},

        _create: function () {
            var _this = this;
            _this.applyLayersToItems();
            $(this.options.cartSelector).on('contentUpdated', function () {
                _this.applyLayersToItems();
            });
        },

        applyLayersToItems: function () {
            var _this = this;
            if(this.options.items !== undefined){
                var itemHolders = $(this.options.cartSelector).find(this.options.cartItemSelector);
                if (!_.isUndefined(itemHolders)) {
                    itemHolders.each(function () {
                        _this.applyLayers(this);
                    })
                }
            }
        },



        applyLayers: function (itemHolder) {

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

            var layers = this.getLayersById(this.getItemId(itemHolder));
            _.each(layers, function (layer) {
                itemImageHolder.append(itemImageBlock.clone().attr('src', layer['url']).css("position", "absolute"));
            })

        },

        getLayersById: function (itemId) {

            if (!_.isUndefined(this.options.items[itemId])) {
                return this.options.items[itemId].layers;
            } else {
                return false;
            }
        },

        getItemId: function (itemHolder) {
            var itemEditUrl = $(itemHolder).find(this.options.editActionSelector).attr('href');
            return this.pattern.exec(itemEditUrl)[1];
        }
    });

    return $.orange35.imageConstructorMini;
});
