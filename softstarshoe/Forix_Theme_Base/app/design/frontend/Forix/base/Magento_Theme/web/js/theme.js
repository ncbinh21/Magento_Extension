/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/smart-keyboard-handler',
    'mage/mage',
    'mage/ie-class-fixer',
    "matchMedia",
    "forix/libs",
    "forix/plugins/images/picturefill",
    "domReady!"
], function ($, keyboardHandler) {
    'use strict';

    if (typeof Forix == "undefined") {
        var Forix = {};
    }

    Forix.Scripts={
        init:function(){
            // Init Debug for Browser and Device
            var detachBrowser= $.initDebug(navigator.userAgent);
            $('html').addClass(detachBrowser);

            this.callAccordionFooter();
            // this.callApplyAccordionStyleguide();
            this.clickOutSite();
        },

        callAccordionFooter:function(){
            if($('.footer_links').length){
                mediaCheck({
                    media: '(max-width: 767px)',
                    entry: $.proxy(function() {
                        if($('.footer_links').length){
                            $('.footer_links .title').each(function(){
                                if(!$(this).find('.dot').length){
                                    $(this).append("<span class='dot'>dot</span>");
                                }
                            });
                            var event='click';
                            if($('html').hasClass('mobile')){
                                event='touchstart';
                            }
                            //console.log(event);
                            //$('.footer_links').find('.title').each(function(){
                            $(document).off(event, '.footer_links .dot');
                            $(document).on(event,'.footer_links .dot',function(){
                                var indexcheck = $(".footer_links li").index($(this).parents("li"));
                                //console.log("ffff"+indexcheck);
                                $('.footer_links li').each(function(index2){
                                    if(indexcheck!=index2){
                                        $(this).removeClass('active');
                                        $(this).find('.title').removeClass("active");
                                        $(this).find('.content').removeClass("active");
                                    }
                                });
                                if($(this).parents("li").hasClass('active')){
                                    $(this).parents("li").removeClass("active");
                                    $(this).parents("li").find('.title').removeClass("active");
                                    $(this).parents("li").find('.content').removeClass("active");
                                }else{
                                    $(this).parents("li").addClass("active");
                                    $(this).parents("li").find('.title').addClass("active");
                                    $(this).parents("li").find('.content').addClass("active");
                                }
                            });
                            //});
                        }
                    }, this),
                    exit: $.proxy(function() {
                        if($('.footer_links').length){
                            $('.footer_links').find('.dot').remove();
                            $('.footer_links').find('.dot').unbind('click,touchstart');
                            $('.footer_links').find('.title').parent().removeClass('active');
                        }
                    }, this)
                });
            }
        },

        callApplyAccordionStyleguide:function(){
            if($('.box-style-guide').length){
                $('body').removeClass("cms-page-view");
                $('.box-style-guide > section').hide();
                $('.box-style-guide > h3').each(function(){
                    $(this).bind('click',function(){
                        $('.box-style-guide > section').hide();
                        if($(this).hasClass('clicked')){
                            $(this).removeClass('clicked');
                            $(this).next().hide();
                        }else{
                            $(this).addClass('clicked');
                            $(this).next().show();
                        }
                    });
                });
            }
        },

        clickOutSite: function(){
            // Process when click Outside
            var $containerCart = $('.minicart-wrapper'),
                $toggleCart= $(".action.showcart");

            $(document).on('click', function (e) {
                // if element is opened and click target is outside it, hide it
                if (!$containerCart.is(e.target) && !$containerCart.has(e.target).length && !$toggleCart.is(e.target) && !$toggleCart.has(e.target).length) {
                    $containerCart.removeClass('active');
                }
            });
        }
    }

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    //custom streets form
    var customStreets = true;
    if(customStreets){
        $(document).on("click",".add-more-line",function () {
            $(this).parents(".street").toggleClass('active');
        });
    }
    //end custom streets form

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();

    Forix.Scripts.init();
});
