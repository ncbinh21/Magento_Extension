define([
    'jquery',
    'underscore',
    'jquery/jquery.hashchange',
    'imageConstructorMini'
], function ($, _) {
    "use strict";

    $.widget('orange35.imageConstructorCheckout', $.orange35.imageConstructorMini, {
        _create: function () {
            var _this = this;
            var waiting = setInterval(function(){
                if($(_this.options.cartSelector+' '+_this.options.cartItemSelector+' '+_this.options.productImageWrapperSelector).length > 0){
                    clearInterval(waiting);
                    _this.applyLayersToItems();
                };
            }, 2000);
            $(window).hashchange(_.bind(_this.applyLayersToItems, _this));
        },

        getItemId: function (itemHolder) {
            var _this = this;
            return $(itemHolder).find(_this.options.productImageWrapperSelector).attr('item-id');
        }
    });

    return $.orange35.imageConstructorCheckout;
});
