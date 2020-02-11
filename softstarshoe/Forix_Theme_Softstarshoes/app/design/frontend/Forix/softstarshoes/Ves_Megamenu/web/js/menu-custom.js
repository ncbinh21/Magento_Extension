/**
 * Created by Pinkie on 24/05/2017.
 * Action: logged menu for mobile
 */
define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    'domReady!',
    'uiComponent',
    'forix/menu'
], function($) {
    "use strict";

    $.widget('forix.loggedMobileMenu', {
        options: {
            idElm: 'menu-login'
        },
        _create: function() {
            // mediaCheck({
            // media: '(max-width: 1023px)',
            // entry: $.proxy(function () {

            this._processMobile();
            // }, this),
            // exit: $.proxy(function () {
            //
            // }, this)
            // });
        },
        _processMobile: function(){
            mediaCheck({
                media: '(max-width: 1023px)',
                entry: $.proxy(function() {
                    var self = this,
                        id = "#"+ self.options.idElm;
                    if (!$(id).length) {
                        var content = '<li id="'+self.options.idElm+'" class="level0 level-top last  parent menu-account devices-level devices-submenu">' +
                            '<a target="_self" class="nav-anchor action showlogin"><span>Sign In</span></a>' +
                            '<ul class="submenu dropdown"><div class="back-link">Back</div><div class="content-wrap"></div></ul></li>';

                        if ($('.level-top.header-phone').length) {
                            $(content).insertBefore($('.level-top.header-phone'));
                        } else {
                            $('.main-nav .navigation > ul').append(content);
                        }
                        $(id).find('.content-wrap').append($('#mini-login'));

                        $(id).megamenu({"static":true});

                        $('.menu-account .block-customer-logged .block-content > ul').removeAttr('class');
                    }

                    if( $('.header-top-info-right > .header-phone').length && !$('.level-top.header-phone').length ) {
                        $('<li />', {
                            "class": 'header-phone level0 level-top devices-level',
                            html: $('.header-top-info-right > .header-phone').children().clone()
                        }).insertAfter($(id));

                        if ( $('.level-top.header-phone > .dropdown-top').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.header-phone > .dropdown-top') );
                            $('.level-top.header-help').megamenu({"static":true});
                        }
                    }

                    if( !$('.navigation > ul > li.header-chat').length || !$('.navigation > ul > li.header-text').length ) {
                        var htmlChat = ( $('.header-top-info-right .contact-help').find('.chat-help').length ) ? $('.header-top-info-right .contact-help').find('.chat-help').parents('a').clone() : '',
                            htmlText = ( $('.header-top-info-right .contact-help').find('.icon-text').length ) ? $('.header-top-info-right .contact-help').find('.icon-text').parents('a').clone() : '';

                        $('<li />', {
                            "class": 'header-chat level0 level-top devices-level',
                            html: htmlChat
                            // html: '<a title="Live Chat" href="javascript:$zopim.livechat.window.show()"><span class="chat-help">Live Chat</span></a>'
                        }).insertAfter($('.level-top.header-phone'));

                        $('<li />', {
                            "class": 'header-text level0 level-top devices-level',
                            html: htmlText
                            // html: '<a title="Live Chat" href="javascript:$zopim.livechat.window.show()"><span class="chat-help">Live Chat</span></a>'
                        }).insertAfter($('.level-top.header-chat'));
                    }

                    if( $('.header-top-info-right > .header-track-order').length && !$('.navigation > ul > li.header-track-order').length ) {
                        var $eleAfter = $('.level-top.header-help');

                        if ( $('.header-top-info-right .contact-help').find('.mail-help').length && $('.header-top-info-right .contact-help').find('.icon-text').length ) {
                            $eleAfter = $('.level-top.header-text');
                        } else if ($('.header-top-info-right .contact-help').find('.mail-help').length && !$('.header-top-info-right .contact-help').find('.icon-text').length) {
                            $eleAfter = $('.level-top.header-help');
                        } else if (!$('.header-top-info-right .contact-help').find('.mail-help').length && $('.header-top-info-right .contact-help').find('.icon-text').length) {
                            $eleAfter = $('.level-top.header-text');
                        }

                        var classTrackOrder = ( $('.level-top.header-track-order > .dropdown-top').length ) ? 'parent' : '';

                        $('<li />', {
                            "class": 'header-track-order level0 level-top devices-level ' + classTrackOrder,
                            html: $('.header-top-info-right > .header-track-order').children().clone()
                        }).insertAfter($eleAfter);

                        if ( $('.level-top.header-track-order > .dropdown-top').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.header-track-order > .dropdown-top') );
                            $('.level-top.header-help').megamenu({"static":true});
                        }
                    }

                    if( $('.header-top-info-right > .header-help').length && !$('.navigation > ul > li.header-help').length ) {
                        $('<li />', {
                            "class": 'header-help parent level0 level-top devices-level devices-submenu',
                            html: $('.header-top-info-right > .header-help').children().clone()
                        }).insertAfter($('.level-top.header-track-order'));

                        if ( $('.level-top.header-help > .dropdown-top').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.header-help > .dropdown-top') );
                            $('.level-top.header-help').megamenu({"static":true});

                        }
                    }

                    if( $('.header-top-info-right > .header-guides').length && !$('.navigation > ul > li.header-guides').length ) {
                        $('<li />', {
                            "class": 'header-guides parent level0 level-top devices-level devices-submenu',
                            html: $('.header-top-info-right > .header-guides').children().clone()
                        }).insertAfter($('.level-top.header-help'));

                        if ( $('.level-top.header-guides > .dropdown-top').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.header-guides > .dropdown-top') );
                            $('.level-top.header-guides').megamenu({"static":true});

                        }
                    }

                    if( $('.footer.content .block-social').length && !$('.navigation > ul > li.menu-social').length )  {
                        $('<li />', {
                            "class": 'menu-social parent level0 level-top devices-level',
                            html: $('.footer.content .block-social').children().clone()
                        }).insertAfter($('.level-top.header-guides'));
                    }

                    if( $('.header.content .back-to-store').length && !$('.navigation > ul > li.back-to-store').length )  {
                        $('<li />', {
                            "class": 'menu-social parent level0 level-top back-to-store devices-level',
                            html: $('.header.content .back-to-store').children().clone()
                        }).insertAfter($('.level-top.header-phone'));
                    }
                }, this),
                exit: $.proxy(function() {
                    if ( $('#menu-login').length ) {
                        $('#menu-login').find('#mini-login').insertAfter( $('.header.content .minicart-wrapper') );

                        $('#menu-login').remove();
                    }
                }, this)
            });
        }
    });
    return $.forix.loggedMobileMenu;
});
