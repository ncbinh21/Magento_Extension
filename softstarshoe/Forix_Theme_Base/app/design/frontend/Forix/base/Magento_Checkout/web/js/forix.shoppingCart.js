/**
 * Created by Eric on 3/2/2016.
 */
define([
    "jquery",
    "matchMedia",
    "forix/plugins/tweenmax",
    "forix/plugins/stickykit",
    "Magento_Ui/js/modal/modal",
    "domReady!"
], function($) {
    "use strict";

    if (typeof Forix == "undefined") {
        var Forix = {};
    }

    Forix.ShoppingCart={
        init : function(){
            this.stickyCartSummary();
            if($('#alert_delete').length){
                this.deleteConfirm();
            }else{
                this.applyRemoveandEditLink();
            }
            this.updateSticky();
        },

        stickyCartSummary: function(){
            var isTouchDevice = typeof document.ontouchstart !== 'undefined';
            mediaCheck({
                media: '(max-width: 1023px)',
                entry: $.proxy(function() {
                    $('.cart-summary').trigger("sticky_kit:detach");
                }, this),
                exit: $.proxy(function() {

                }, this)
            });
            mediaCheck({
                media: '(min-width: 1024px)',
                entry: $.proxy(function() {
                    if($('html').hasClass('mobile') || isTouchDevice){
                        $('.cart-summary').trigger("sticky_kit:detach");
                    }else{
                        $('.cart-summary').stick_in_parent({
                            parent: '.cart-container',
                            offset_top: 0
                        });
                    }
                }, this),
                exit: $.proxy(function() {

                }, this)
            });

        },

        applyRemoveandEditLink: function(){
            $('.form-cart .cart.item').each(function(){
                $(this).find('.item-info .action-edit').on('click', $.proxy(function(e) {
                    e.preventDefault();
                    var urlEDit=$(this).closest('.cart.item').find('.item-actions .action-edit').attr('href');
                    $(this).closest('.cart.item').find('.item-actions .action-edit').trigger( "click" );
                    if(urlEDit)
                    location.href = urlEDit;
                }, this));
                $(this).find('.item-info .action-delete').on('click', $.proxy(function(e) {
                    e.preventDefault();
                    $(this).closest('.cart.item').find('.item-actions .action-delete').trigger( "click" );
                }, this));
            });
        },

        updateSticky: function(){
            var stickyDiscount = $("#block-discount").find('.title'),
                stickyGiftCard = $("#block-giftcard").find('.title'),
                stickyEstimate = $("#block-shipping").find('.title');
            if(stickyDiscount.data('forixToggleAdvanced')){
                stickyDiscount.on('toggleadvancedaftertoggle',function(){
                    $('.cart-summary').trigger("sticky_kit:recalc");
                });
            }
            if(stickyGiftCard.data('forixToggleAdvanced')){
                stickyGiftCard.on('toggleadvancedaftertoggle',function(){
                    $('.cart-summary').trigger("sticky_kit:recalc");
                });
            }
            if(stickyEstimate.data('forixToggleAdvanced')){
                stickyEstimate.on('toggleadvancedaftertoggle',function(){
                    $('.cart-summary').trigger("sticky_kit:recalc");
                });
            }
        },

        deleteConfirm: function(){
            var options = {
                type: 'popup',
                modalClass: 'confirm',
                responsive: true,
                innerScroll: true,
                buttons: [
                    {
                        text: $.mage.__('Cancel'),
                        class: 'action-secondary action-dismiss',
                        click: function () {
                            $('#alert_delete').modal('closeModal');
                        }
                    },
                    {
                        text: $.mage.__('OK'),
                        class: 'action-primary action-accept',
                        click: function () {
                            $('#alert_delete').modal('closeModal');
                            $('.form-cart .cart.item').find('.item-actions .action-delete.deleted').trigger( "click" );
                        }
                    }
                ]
            }, popup=$('#alert_delete').modal(options);

            $('.form-cart .cart.item').each(function(){
                $(this).find('.item-info .action-edit').on('click', $.proxy(function(e) {
                    e.preventDefault();
                    var urlEDit=$(this).closest('.cart.item').find('.item-actions .action-edit').attr('href');
                    $(this).closest('.cart.item').find('.item-actions .action-edit').trigger( "click" );
                    if(urlEDit)
                        location.href = urlEDit;
                }, this));
                $(this).find('.item-info .action-delete').on('click', $.proxy(function(e) {
                    e.preventDefault();
                    $('.form-cart .cart.item').find('.item-actions .action-delete').removeClass("deleted");
                    $(this).closest('.cart.item').find('.item-actions .action-delete').addClass("deleted");
                    popup.modal('openModal');
                }, this));
            });
        }
    }

    return Forix.ShoppingCart.init();

});