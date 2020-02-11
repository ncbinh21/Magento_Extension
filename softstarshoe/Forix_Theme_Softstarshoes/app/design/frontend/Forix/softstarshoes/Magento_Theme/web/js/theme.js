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
    'forix/plugins/fancybox',
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
            this.callApplyAccordionStyleguide();
            this.clickOutSite();
            this.fixedHeader();
            this.cmsLeftMenu();
            this.callAccordionFaq();
            this.callAccordionCusomProduct();
            this.galleryVideoFancybox();
            this.telephone();
            this.imageResponsize();
            //this.hiddenMessageMinicart();
            var seft = this;
            $(window).resize(function() {
                seft.fixedHeader();
            });
        },
        telephone:function(){
            var detectIsMobile = window.mobileAndTabletcheck = function() {
                var check = false;
                (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
                return check;
            };
            if (!detectIsMobile()) {
                $("p a.tel").attr("href","javascript:void(0);");
            }
        },
        hiddenMessageMinicart: function(){
            setTimeout(function() {
                $('[class="page messages"]').hide('slow');
            }, 5000);
        },
        imageResponsize: function() {
            if ($("img.image-responsive").length) {
                $("img.image-responsive").each(function() {
                    var srcDesktop = $(this).data("image-desktop");
                    var srcTablet = $(this).data("image-tablet");
                    var srcMobile = $(this).data("image-mobile");
                    var iWidth = $(window).width();
                    if (iWidth < 768) {
                        if (srcMobile) {
                            $(this).attr("src", srcMobile);
                        } else if (srcTablet) {
                            $(this).attr("src", srcTablet);
                        } else if (srcDesktop) {
                            $(this).attr("src", srcDesktop);
                        }
                    } else if (iWidth > 1023) {
                        if (srcDesktop) {
                            $(this).attr("src", srcDesktop);
                        } else if (srcTablet) {
                            $(this).attr("src", srcTablet);
                        } else if (srcMobile) {
                            $(this).attr("src", srcMobile);
                        }
                    } else {
                        if (srcTablet) {
                            $(this).attr("src", srcTablet);
                        } else if (srcDesktop) {
                            $(this).attr("src", srcDesktop);
                        } else if (srcMobile) {
                            $(this).attr("src", srcMobile);
                        }
                    }
                });
            }
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
                            //$('.footer_links').find('.title').each(function(){
                            $(document).off(event, '.footer_links .dot');
                            $(document).on(event,'.footer_links .dot',function(){
                                var indexcheck = $(".footer_links li").index($(this).parents("li"));
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

        callAccordionCusomProduct:function(){
            if($('.o35-swatch-opt').length){
                mediaCheck({
                    media: '(min-width: 0)',
                    entry: $.proxy(function() {
                        if($('.o35-swatch-opt').length){
                            $('.o35-swatch-opt .o35-swatch-option-label').each(function(){
                                if(!$(this).find('.dot').length){
                                    $(this).append("<span class='dot'>select</span>");
                                }
                            });
                            var event='click';
                            if($('html').hasClass('mobile')){
                                event='touchstart';
                            }
                            $(document).off(event, '.o35-swatch-opt .dot');
                            $(document).on(event,'.o35-swatch-opt .dot',function(){
                                // var indexcheck = $(".o35-swatch-opt .o35-swatch-opt-item").index($(this).parents(".o35-swatch-opt-item"));
                                //var currentOffset = $(this).offset().top;
                                var indexcheck = $(this).parents(".o35-swatch-opt-item");
                                $('.o35-swatch-opt .o35-swatch-opt-item').each(function(index2){
                                    if(indexcheck!=index2){
                                        $(this).removeClass('active');
                                        $(this).find('.o35-swatch-option-label').removeClass("active");
                                        $(this).find('.o35-swatch-option-values').removeClass("active");
                                    }
                                });
                                if($(this).parents(".o35-swatch-opt-item").hasClass('active')){
                                    $(this).parents(".o35-swatch-opt-item").removeClass("active");
                                    $(this).parents(".o35-swatch-opt-item").find('.o35-swatch-option-label').removeClass("active");
                                    $(this).parents(".o35-swatch-opt-item").find('.o35-swatch-option-values').removeClass("active");
                                }else{
                                    $(this).parents(".o35-swatch-opt-item").addClass("active");
                                    $(this).parents(".o35-swatch-opt-item").find('.o35-swatch-option-label').addClass("active");
                                    $(this).parents(".o35-swatch-opt-item").find('.o35-swatch-option-values').addClass("active");
                                }
                                if ($(window).width() >= 1024) {
                                    $("body, html").animate({
                                        scrollTop: $(this).offset().top - 80
                                    }, 0);
                                }
                                else{
                                    $("body, html").animate({
                                        scrollTop: $(this).offset().top - 10
                                    }, 0);
                                }
                            });
                        }
                    }, this),
                    exit: $.proxy(function() {
                        if($('.o35-swatch-opt').length){
                            $('.o35-swatch-opt').find('.dot').remove();
                            $('.o35-swatch-opt').find('.dot').unbind('click,touchstart');
                            $('.o35-swatch-opt').find('.o35-swatch-option-label').parent().removeClass('active');
                        }
                    }, this)
                });
            }
        },

        callApplyAccordionStyleguide:function(){
            if($('.box-style-guide').length){
                $('body').removeClass("cms-page-view");
                //$('.box-style-guide > section').hide();
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
        },

        fixedHeader:function(){
            var $header = $(".page-header");

            var $height1 = 0;
            var $height2 = 0;
            var $height3 = 0;

            if($(".page-promotions").length > 0){
                $height1= $(".page-promotions").innerHeight();
            }
            if($(".panel.wrapper").length > 0){
                $height2=  $(".panel.wrapper").innerHeight();
            }
            if($(".page-header .header.content").length > 0){
                $height3=  $(".page-header .header.content").innerHeight();
            }

            $(".page-wrapper").css({'padding-top': $height1 + $height2 + $height3});

            $(window).scroll(function() {
                if (window.pageYOffset >= 50) {
                    if(!$header.hasClass("sticky")){
                        $("body").addClass("has-sticky");
                    }
                    $header.addClass("sticky");
                    $(".page-wrapper .page-header.sticky").css({ 'top': -($height1 + $height2)});
                    var $heightSticky = $(".page-header.sticky .header.content").innerHeight();
                    $(".page-wrapper").css({'padding-top': $heightSticky});
                }
                else {
                    if($header.hasClass("sticky")){
                        $("body").removeClass("has-sticky");
                    }
                    $header.removeClass("sticky");
                    $(".page-wrapper .page-header").css({ 'top': 0});
                    var $heightNoneSticky = $(".page-header .header.content").innerHeight();
                    $(".page-wrapper").css({'padding-top': $height1 + $height2 + $heightNoneSticky});
                }
            });
        },

        callAccordionFaq:function(){
            if($('.block-faqs').length){
                mediaCheck({
                    media: '(min-width: 0)',
                    entry: $.proxy(function() {
                        if($('.block-faqs').length){
                            $('.block-faqs .title').each(function(){
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
                            $(document).off(event, '.block-faqs .title');
                            $(document).on(event,'.block-faqs .title',function(){
                                var indexcheck = $(".block-faqs li").index($(this).parents("li"));
                                //console.log("ffff"+indexcheck);
                                $('.block-faqs li').each(function(index2){
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
                        if($('.block-faqs').length){
                            $('.block-faqs').find('.dot').remove();
                            $('.block-faqs').find('.dot').unbind('click,touchstart');
                            $('.block-faqs').find('.title').parent().removeClass('active');
                        }
                    }, this)
                });
            }
        },
        cmsLeftMenu:function(){
            if($("#block-cms-toggle").length){
                var arrPath = location.pathname.split("/");
                if(arrPath.length){
                    var pageName = arrPath[arrPath.length - 1] != "" ? arrPath[arrPath.length - 1] : arrPath[arrPath.length - 2];
                    $("#block-cms-toggle li a").each(function(){
                        if($(this).attr("href") && $(this).attr("href").indexOf(pageName) > 0){
                            $(this).parent().addClass("active current");
                            $(this).attr("href","javascript:void(0)");
                        }
                    });
                }
            }

            $(".account .account-nav-content").each(function(){
                var navActive = $(this).find(".nav.item.current");
                var title = "<strong>Account Dashboard</strong>";
                if(navActive.length > 0){
                    title = navActive.html();
                }

                $(".account-nav-title").html(title);
            });

            mediaCheck({
                media: '(max-width: 767px)',
                entry: $.proxy(function() {
                    try{
                        if($("#block-cms-toggle li.active").length){
                            var menuText = $("#block-cms-toggle li.active").text();
                            $(".sidebar-cms .block-title > strong").html(menuText);
                            $("#block-cms-toggle li.active").addClass("hide");
                        }
                    }
                    catch(err){
                    }
                }, this),
                exit: $.proxy(function() {
                    $(".sidebar-cms .block-title > strong").html("Help");
                    $("#block-cms-toggle li.active").removeClass("hide");
                }, this)
            });
        },
        galleryVideoFancybox:function(){
            $(".fancybox-video").each(function(){
                var url = $(this).attr("href").replace("/watch?v=","/embed/");
                $(this).attr("href", url);
            });

            $(".fancybox-video").fancybox({
                //margin      : [40, 10, 10, 10],
                padding     : 0,
                type        : 'iframe',
                autoSize    : false,
                fitToView   : true,
                width       : 1024,
                height      : 576,
                aspectRatio : true,
                scrolling   : 'no',
                maxWidth    : '94%',
                closeClick  : true,
                helpers: {
                    overlay: {
                        locked: false
                    }
                },
                afterShow: function(){
                    $(".fancybox-skin").addClass("fancybox-video-skin");
                }
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

    if ($('body').hasClass('checkout-cart-index')) {
        if ($('#co-shipping-method-form .fieldset.rates').length > 0 && $('#co-shipping-method-form .fieldset.rates :checked').length === 0) {
            $('#block-shipping').on('collapsiblecreate', function () {
                $('#block-shipping').collapsible('forceActivate');
            });
        }
    }

    $('.panel.header > .header.links').clone().appendTo('#store\\.links');

    keyboardHandler.apply();

    Forix.Scripts.init();
});
