define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    "forix/libs",
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    //  Responsive menu
    $.widget('forix.pushdata', {
        options: {
            container: '#mini-login',
            toggleBtn: '[data-trigger=authentication]',
            swipeArea: '.swipe-minilogin',
            pushCloseCls:'push-close',
            closeBtnCls:'toggle-login-close',
            clsPush:'login',
            noEffect:'.minicart-wrapper',
            responsive: true,
            swiped: 'right', //['left'|'right']
            expanded: false, //[true|false]
            delay: 300,
            type: '', // type = form -> fixed bug when hover on autocomplete input dropdown hidden
            typeClass: 'minilogin-wrapper' // only apply when type='form'
        },

        _create: function() {

        },

        _init: function () {
            this.url='';

            if (this.options.responsive === true) {
                mediaCheck({
                    media: '(max-width: 767px)',
                    entry: $.proxy(function () {
                        this._toggleMobileMode();
                    }, this),
                    exit: $.proxy(function () {
                        this._toggleDesktopMode();
                    }, this)
                });
            }

            if(this.options.type === 'form'){
                this._processAutocompleteInput();
            }
        },

        _initContent: function(){
            var swipeArea = $(this.element),
                pushClose = "<div class='"+this.options.pushCloseCls+"'></div>",
                closeBtn = "<div class='"+this.options.closeBtnCls+"'><span>Close</span></div>";

            if( swipeArea.length && !swipeArea.find('.'+this.options.pushCloseCls).length){
                swipeArea.append(pushClose);
            }

            if( swipeArea.length && !swipeArea.find('.'+this.options.closeBtnCls).length){
                swipeArea.append(closeBtn);
            }
        },

        _toggleMobileMode: function(){
            this._initContent();
            this._listen();
            //this._processLink('mobile');
        },

        _toggleDesktopMode: function(){
            var toggleBtn = $(this.options.toggleBtn),
                swipeArea = $(this.element);

            swipeArea.find('.'+this.options.pushCloseCls).remove();
            swipeArea.find('.'+this.options.closeBtnCls).remove();
            this._off(toggleBtn, 'click');
            this._destroy();
            this._processLink('desktop');
        },

        _listen: function(){
            var controls = this.options,
                swipeArea = $(this.element),
                toggleBtn = $(this.options.toggleBtn),
                swipeLeft = this.swipeLeft,
                swipeRight = this.swipeRight,
                controlDocument = $(document),
                toggleOutsite = this.toggleOutsite,
                pushCloseBtn = swipeArea.find('.'+this.options.pushCloseCls),
                pushClose = this.pushClose,
                CloseBtn = swipeArea.find('.'+this.options.closeBtnCls),
                close = this.close;

            if(controls.swiped){
                if(controls.swiped === 'left'){
                    this._on(toggleBtn, {'click': swipeLeft});
                }
                if(controls.swiped === 'right'){
                    this._on(toggleBtn, {'click': swipeRight});
                }
            }

            if(controls.expanded){

            }

            // this._on(controlDocument, {'click': toggleOutsite});
            this._on(pushCloseBtn, {'click': pushClose});
            this._on(CloseBtn, {'click': close});
        },

        toggleOutsite: function(e){
            var seft = this,
                controls = this.options;

            // if element opened and click target is hide it
            if (!$(this.element).is(e.target) && !$(this.element).has(e.target).length && !$(controls.container).is(e.target) && !$(controls.container).has(e.target).length && !$(controls.toggleBtn).is(e.target) && !$(controls.toggleBtn).has(e.target).length) {
                if(!$(seft.options.noEffect).is(e.target) && !$(seft.options.noEffect).has(e.target).length){
                    this._destroy();
                }else{
                    this._removePushData();
                }
            }
        },

        _swipe:function(add){
            var seft = this;

            if(add === 'add'){
                $('html').addClass(seft.options.clsPush + '-before-open');
                setTimeout(function () {
                    $('html').addClass(seft.options.clsPush + '-open');
                }, 80);
            }

            if(add === 'remove'){
                setTimeout(function () {
                    $('html').removeClass(seft.options.clsPush + '-open');
                }, 80);
                setTimeout(function () {
                    $('html').removeClass(seft.options.clsPush + '-before-open');
                }, 300);
            }
        },

        swipeLeft: function(e){
            var seft = this;
            e.preventDefault();
            seft.addCounterNumber();

            if(!$(e.target).hasClass('swiped')) {
                if ($('html').hasClass(seft.options.clsPush + '-open')) {
                    $('html').removeClass('push left');
                    setTimeout(function () {
                        $('html').removeClass('push left');
                    }, 300);
                    this._swipe('remove');
                } else {
                    $('html').removeClass('push right');
                    $('html').addClass('push left');
                    this._swipe('add');
                }
            }
        },

        swipeRight: function(e){
            var seft = this;
            e.preventDefault();
            seft.addCounterNumber();

            if(!$(e.target).hasClass('swiped')) {
                if ($('html').hasClass(seft.options.clsPush + '-open')) {
                    setTimeout(function () {
                        $('html').removeClass('push right');
                    }, 300);
                    this._swipe('remove');
                } else {
                    $('html').removeClass('push left');
                    $('html').addClass('push right');
                    this._swipe('add');
                }
            }
        },

        addCounterNumber: function(){
            var toggleBtn = $(this.options.toggleBtn),
                pushClose = $('.'+this.options.pushCloseCls),
                counterBtn = $('.'+this.options.pushCloseCls+' .counter');

            counterBtn.remove();

            if(toggleBtn.find('.counter-number').length){
                var number= toggleBtn.find(".counter-number").text();
                if(number === undefined )
                    number= 0;
                else
                    number= parseInt(number);
                pushClose.append('<span class="counter"><span class="counter-number">'+number+'</span></span>');
            }
        },

        pushClose: function () {
            this._destroy();
        },

        close: function (){
            this._destroy();
        },

        _removePushData: function(){
            var seft = this;

            setTimeout(function () {
                $('html').removeClass(seft.options.clsPush + '-open');
            }, 80);

            setTimeout(function () {
                $('html').removeClass(seft.options.clsPush + '-before-open');
            }, 300);
        },

        _destroy: function(){
            var seft = this;

            setTimeout(function () {
                $('html').removeClass('push left');
            }, 300);

            setTimeout(function () {
                $('html').removeClass('push right');
            }, 300);

            setTimeout(function () {
                $('html').removeClass(seft.options.clsPush + '-open');
            }, 80);

            setTimeout(function () {
                $('html').removeClass(seft.options.clsPush + '-before-open');
            }, 300);
        },

        _processLink: function(version){
            var toggleBtn = $(this.options.toggleBtn);

            if(toggleBtn.attr('href')){
                this.url = toggleBtn.attr('href');
            }
            if(version === 'mobile'){
                // process double click Links
            }
            if(version === 'desktop'){
                // process on device double click Links or : Click 1 show, click 2 link
                var toggleBtn = $(this.options.toggleBtn);
                var toggleLinkDesktop = this.toggleLinkDesktop;
                this._on(toggleBtn, {'click': toggleLinkDesktop});
            }
        },

        toggleLinkDesktop: function(e){
            var seft= this;

            if((seft.url.indexOf("http")!= -1) && ($.checkDevice("mobile") != -1)){
                e.preventDefault();
                // process on device
                if($(e.target).hasClass('clicked')){
                    $(e.target).removeClass('clicked');
                    $(location).attr('href', seft.url);
                }else{
                    $(e.target).addClass('clicked');
                }
            }
        },

        _processAutocompleteInput: function(){
            var seft= this;

            mediaCheck({
                media: '(min-width: 768px)',
                entry: $.proxy(function () {
                    $(seft.options.container).hover(function () {
                        $(this).addClass('active');
                    });
                    $(document).mousemove(function (e) {
                        var target = $(e.target);
                        if(!target.parents().hasClass(seft.options.typeClass)){
                            $(seft.options.container).removeClass('active');
                        }
                    });
                }, this),
                exit: $.proxy(function () {
                    $(seft.options.container).removeClass('active');
                }, this)
            });
        }
    });

    return $.forix.pushdata;
});