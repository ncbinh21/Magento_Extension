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
            this._processMobile();
        },
        _processMobile: function(){
            mediaCheck({
                media: '(max-width: 1023px)',
                entry: $.proxy(function() {
                    var self = this,
                        id = "#"+ self.options.idElm;

                    if( $('.right-second-nav > .size-guide').length && !$('.level-top.size-guide').length ) {
                        $('<li />', {
                            "class": 'level0 level-top devices-level size-guide',
                            html: $('.right-second-nav > .size-guide').children().clone()
                        }).appendTo('.main-nav .navigation > ul');
                    }

                    if (!$(id).length) {
                        var content = '<li id="'+self.options.idElm+'" class="nav-item level0 level-top menu-account devices-level devices-submenu">' +
                            '<a target="_self" class="nav-anchor action showlogin"><span>Sign In</span></a>' +
                            '<div class="submenu dropdown-menu block-dropdown"><div class="back-link">Back</div><div class="content-wrap"></div></div></li>';

                        if($('#mini-login').hasClass('logged')) {
                            var content = '<li id="'+self.options.idElm+'" class="nav-item level0 level-top menu-account devices-level devices-submenu">' +
                                '<a target="_self" class="nav-anchor action showlogin"><span>My Account</span></a>' +
                                '<div class="submenu dropdown-menu block-dropdown"><div class="back-link">Back</div><div class="content-wrap"></div></div></li>';
                        }
                        if ($('.level-top.size-guide').length) {
                            $(content).insertAfter($('.level-top.size-guide'));
                        } else {
                            $('.main-nav .navigation > ul').append(content);
                        }

                        if ( $('.level-top.menu-account > a').length ){
                            $('<span class="opener"></span>').insertAfter( $('.level-top.menu-account > a'));
                        }

                        $(id).find('.content-wrap').append($('#mini-login'));

                        $(id).megamenu({"static":true});

                        $('.menu-account .block-customer-logged .block-content > ul').removeAttr('class');
                    }

                    if( $('.right-second-nav > .help').length && !$('.level-top.help').length ) {
                        $('<li />', {
                            "class": 'nav-item level0 level-top help devices-level devices-submenu',
                            html: $('.right-second-nav > .help').children().clone()
                        }).insertAfter($(id));
                        if ( $('.level-top.help > a').length ){
                            $('<span class="opener"></span>').insertAfter( $('.level-top.help > a'));
                        }
                        if ( $('.level-top.help > .block-dropdown').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.help > .block-dropdown') );
                            $('.level-top.help').megamenu({"static":true});
                        }
                    }

                    if( $('.right-second-nav > .tel').length && !$('.level-top.tel').length ) {
                        $('<li />', {
                            "class": 'nav-item level0 level-top tel devices-level',
                            html: $('.right-second-nav > .tel').children().clone()
                        }).insertAfter('.level-top.help');
                    }

                    if( $('.left-second-nav > .minimal-shoes-handmade').length && !$('.level-top.minimal-shoes-handmade').length ) {
                        $('<li />', {
                            "class": 'nav-item level0 level-top minimal-shoes-handmade devices-level devices-submenu',
                            html: $('.left-second-nav > .minimal-shoes-handmade').children().clone()
                        }).insertAfter('.level-top.tel');
                        if ( $('.level-top.minimal-shoes-handmade > a').length ){
                            $('<span class="opener"></span></span>').insertAfter( $('.level-top.minimal-shoes-handmade > a'));
                        }
                        if ( $('.level-top.minimal-shoes-handmade > .block-dropdown').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.minimal-shoes-handmade > .block-dropdown') );
                            $('.level-top.minimal-shoes-handmade').megamenu({"static":true});
                        }
                    }

                    if( $('.left-second-nav > .freeshipping').length && !$('.level-top.freeshipping').length ) {
                        $('<li />', {
                            "class": 'nav-item level0 level-top freeshipping devices-level devices-submenu',
                            html: $('.left-second-nav > .freeshipping').children().clone()
                        }).insertAfter('.level-top.minimal-shoes-handmade');
                        if ( $('.level-top.freeshipping > a').length ){
                            $('<span class="opener"></span></span>').insertAfter( $('.level-top.freeshipping > a'));
                        }
                        if ( $('.level-top.freeshipping > .block-dropdown').length ){
                            $('<div class="back-link">Back</div>').prependTo( $('.level-top.freeshipping > .block-dropdown') );
                            $('.level-top.freeshipping').megamenu({"static":true});
                        }
                    }
                }, this),
                exit: $.proxy(function() {
                    if ( $('#menu-login').length ) {
                        $('#menu-login').find('#mini-login').appendTo( $('.minilogin-container') );

                        $('#menu-login').remove();
                    }
                    if( $('.level-top.devices-level').length ) {
                        $('.level-top.devices-level').remove();
                    }
                }, this)
            });
        }
    });
    return $.forix.loggedMobileMenu;
});
