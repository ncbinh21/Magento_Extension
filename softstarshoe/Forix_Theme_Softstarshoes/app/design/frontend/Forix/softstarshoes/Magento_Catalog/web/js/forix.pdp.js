define([
    'jquery',
    'matchMedia',
    "forix/plugins/tweenmax",
    "forix/plugins/stickykit",
    'domReady!'
], function($){
    "use strict";

    if (typeof Forix == "undefined") {
        var Forix = {};
    }

    Forix.PDP={
        init:function(){
            this.stickyProductMain();
            this.moveSizeChart();
        },


        stickyProductMain: function(){
            var isTouchDevice = typeof document.ontouchstart !== 'undefined';
            mediaCheck({
                media: '(max-width: 767px)',
                entry: $.proxy(function() {
                    $('.product.media').trigger("sticky_kit:detach");
                }, this),
                exit: $.proxy(function() {

                }, this)
            });
            mediaCheck({
                media: '(min-width: 768px)',
                entry: $.proxy(function() {
                    // if($('html').hasClass('mobile') || isTouchDevice){
                    //     $('.product.media').trigger("sticky_kit:detach");
                    // }else{
                        $('.product.media').stick_in_parent({
                            parent: '.product-container'
                        }).on("sticky_kit:stick", function() {
                            $('.product-container').addClass("product-sticky");
                        }).on("sticky_kit:unstick", function() {
                                $('.product-container').removeClass("product-sticky");
                        });
                    // }
                }, this),
                exit: $.proxy(function() {

                }, this)
            });
        },

        moveSizeChart: function(){
            if($('.page-product-configurable').length && $('.swatch-attribute.size').length && $('#sizechart').length){
                $('#sizechart').detach().appendTo('.swatch-opt').addClass("show");
            }
        }
    }

    return Forix.PDP.init();

});