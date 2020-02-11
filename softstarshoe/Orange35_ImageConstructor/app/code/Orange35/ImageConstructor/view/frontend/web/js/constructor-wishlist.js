define([
    'jquery',
    'imageConstructorMini'
], function ($) {
    "use strict";

    $.widget('orange35.imageConstructorWishlist', $.orange35.imageConstructorMini, {
        pattern: /\d+/,
        _create: function () {
            var _this = this;
            _this.applyLayersToItems();
        },

        getItemId: function (itemHolder) {
            var itemId = $(itemHolder).attr('id');
            return this.pattern.exec(itemId)[0];
        }

    });

    return $.orange35.imageConstructorWishlist;
});
