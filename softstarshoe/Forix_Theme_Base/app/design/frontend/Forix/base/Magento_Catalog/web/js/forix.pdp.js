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
            this.increaseQty();
            this.stickyProductMain();
            this.moveSizeChart();
        },

        increaseQty: function(){
            $('.box-tocart').find('.increase-down').on('click', $.proxy(function(e) {

                var valQty= parseInt($('.box-tocart').find('#qty').val());
                if(valQty>1)
                    valQty--;
                $('.box-tocart').find('#qty').val(valQty);
                $('.box-tocart').find('#qty').trigger('change');

            }, this));

            $('.box-tocart').find('.increase-up').on('click', $.proxy(function(e) {

                var valQty= parseInt($('.box-tocart').find('#qty').val());
                if(valQty<1)
                    valQty = 0;
                valQty++;

                $('.box-tocart').find('#qty').val(valQty);
                $('.box-tocart').find('#qty').trigger('change');
            }, this));
        },

        stickyProductMain: function(){
            var isTouchDevice = typeof document.ontouchstart !== 'undefined';
            mediaCheck({
                media: '(max-width: 1023px)',
                entry: $.proxy(function() {
                    $('.product-info-main').trigger("sticky_kit:detach");
                }, this),
                exit: $.proxy(function() {

                }, this)
            });
            mediaCheck({
                media: '(min-width: 1024px)',
                entry: $.proxy(function() {
                    if($('html').hasClass('mobile') || isTouchDevice){
                        $('.product-info-main').trigger("sticky_kit:detach");
                    }else{
                        $('.product-info-main').stick_in_parent({
                            parent: '.column.main',
                            offset_top: 0
                        });
                    }
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