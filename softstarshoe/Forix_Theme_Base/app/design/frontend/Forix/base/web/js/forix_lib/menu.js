define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    "forix/libs",
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    //  Responsive menu
    $.widget('forix.megamenu', {
        options: {
            static: false,
            linkDevice: true,
            toggleBtn: $('[data-action="toggle-nav"]'),
            swipeArea: $('.nav-sections'),
            responsive: true,
            swiped: true,
            expanded: false,
            delay: 300,
            toggleSubcls:"opener",
            arrowSubcls:"ico-arrow",
            textBack: "Back",
            useNameforBack: true,
            pushCloseCls:'push-menu-close',
            pushClosehtml: '',
            containerMenu: $('.main-nav')
        },

        _create: function () {

        },

        _init: function () {
            var self = this;
            if (this.options.static) {
                this._initStaicMenu();
            }

            this._initArrow();
            this._breakLinksOnDevice();

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

            this._listen();
        },

        _initStaicMenu: function () {
            this.element.find("li").addClass("level0 level-top");
            this.element.find("li li").removeAttr('class').addClass("level1");
            this.element.find("li li li").removeAttr('class').addClass("level2");
            this.element.find("ul").addClass("level0 submenu");
            this.element.find("ul ul").removeAttr('class').addClass("level1 submenu");
            this.element.find(".submenu").each(function () {
                if ($(this).find('.submenu'))
                    $(this).closest('li').addClass("parent");
            });
        },

        _initArrow: function () {
            var linkback="<div class='back-link'>"+this.options.textBack+"</div>",
                arrowOpen = "<span class='"+this.options.toggleSubcls+"'></span>",
                arrowIcon = "<span class='"+this.options.arrowSubcls+"'></span>",
                pushClose = "<div class='"+this.options.pushCloseCls+"'>"+this.options.pushClosehtml+"</div>";

            this.element.find("li.parent > a").after(arrowOpen);
            this.element.find("li.parent > a").append(arrowIcon);
            this.element.find("li.parent > .submenu").prepend(linkback);
            var containerMenu = this.options.containerMenu;
            if( containerMenu !='' && !$('.'+this.options.pushCloseCls).length){
                containerMenu.append(pushClose);
            }
        },

        _breakLinksOnDevice: function () {
            var self = this;
            mediaCheck({
                media: '(min-width: 768px)',
                entry: $.proxy(function () {
                    if (self.options.linkDevice && $.checkDevice("mobile") != -1) {
                        this._off($("li.level0.parent > a"), "click");
                        this._on({"click li.level0.parent > a": this._breadkLink});
                    }
                }, this),
                exit: $.proxy(function () {
                    this._off($("li.level0.parent > a"), "click");
                }, this)
            });
        },

        _breadkLink: function (event) {
            if ($(event.target).hasClass("selected")) {
                $(event.target).removeClass("selected");
            } else {
                $("li.level0.parent").removeClass("selected");
                $("li.level0.parent > a > span").removeClass("selected");
                $(event.target).addClass("selected");
                event.preventDefault();
            }
        },

        _listen: function () {
            var controls = this.options,
                toggle = this.toggle,
                ele = this.element,
                controlDocument = $(document),
                toggleOutsite = this.toggleOutsite,
                toggeSub = ele.find("." +controls.toggleSubcls),
                arrowSub = ele.find("." +controls.arrowSubcls),
                swipeSub = this.swipeSub,
                collapsibleSub = this.collapsibleSub,
                backBtn = $(".back-link"),
                backLink = this.backLink,
                pushCloseBtn = $('.'+this.options.pushCloseCls),
                pushClose = this.pushClose;

            this._on(controls.toggleBtn, {'click': toggle});
            this._on(controlDocument, {'click': toggleOutsite});
            if(controls.swiped){
                this._on(toggeSub, {'click': swipeSub});
                this._on(arrowSub, {'click': swipeSub});
            }
            if(controls.expanded){
                this._on(toggeSub, {'click': collapsibleSub});
                this._on(arrowSub, {'click': collapsibleSub});
            }
            this._on(backBtn, {'click': backLink});
            this._on(pushCloseBtn, {'click': pushClose});
            //this._on(controls.swipeArea, {'swipeleft': toggle});
        },

        _toggleMobileMode: function () {

        },

        _toggleDesktopMode: function () {
            this._destroy();
        },

        toggle: function () {
            if ($('html').hasClass('nav-open')) {
                setTimeout(function () {
                    $('html').removeClass('nav-open');
                }, 80);
                setTimeout(function () {
                    $('html').removeClass('nav-before-open');
                }, 300);
            } else {
                $('html').addClass('nav-before-open');
                setTimeout(function () {
                    $('html').addClass('nav-open');
                }, 80);
            }
        },

        toggleOutsite: function (e) {
            var controls = this.options;
            // if element is opened and click target is hide it
            if (!controls.swipeArea.is(e.target) && !controls.swipeArea.has(e.target).length && !controls.containerMenu.is(e.target) && !controls.containerMenu.has(e.target).length && !controls.toggleBtn.is(e.target) && !controls.toggleBtn.has(e.target).length) {
                this._destroy();
            }
        },

        swipeSub: function(event){
            if(!$(event.target).hasClass('swiped')){
                $('.main-nav').animate({
                    scrollTop: 0
                }, 100);

                $(event.target).addClass('swiped');
                $(event.target).closest('li').addClass("swiped");
                $(event.target).closest('li').children(".submenu").addClass("swiped");
                $(".back-link").removeClass('clicked');

                var addingClass = "submenu "+$(event.target).closest('li').children('.submenu').attr("class");

                if(this.options.useNameforBack){
                    var nameSub = $(event.target).closest('li').children('a').text();
                    $(event.target).closest('li').children('.submenu').children('.back-link').text(nameSub).attr("data-text",nameSub);
                }

                if($('html').attr('data-sub')){
                    var checkSub = $('html').attr('data-sub');
                    switch (checkSub) {
                        case '0':
                            $('html').attr('data-sub',1);
                            break;
                        case '1':
                            $('html').attr('data-sub',2);
                            break;
                        case '2':
                            $('html').attr('data-sub',3);
                            break;
                        case '3':
                            $('html').attr('data-sub',4);
                            break;
                    }
                }else{
                    $('html').attr('data-sub',0);
                }

                $('html').removeClass("level"+checkSub);
                $('html').addClass("submenu level"+$('html').attr('data-sub'));
            }
        },

        collapsibleSub: function(e){
            // coming soon

        },

        backLink: function(event){
            if(!$(event.target).hasClass('clicked')){
                $(".back-link").removeClass('clicked');
                $(event.target).addClass('clicked');
                $(event.target).closest('.submenu').removeClass('swiped');
                $(event.target).closest('.submenu').closest('li').find("." +this.options.toggleSubcls).removeClass('swiped');
                $(event.target).closest('.submenu').closest('li').find("." +this.options.arrowSubcls).removeClass('swiped');
                $(event.target).closest('.submenu').closest('li').removeClass('swiped');
                // $(event.target).text(this.options.textBack).removeAttr("data-text");

                if($('html').attr('data-sub')){
                    var checkSub = $('html').attr('data-sub');
                    switch (checkSub) {
                        case '0':{
                            $('html').removeClass("level"+checkSub);
                            $(".back-link").removeClass('clicked');
                            $('html').removeAttr('data-sub');
                            $('html').removeClass("submenu");
                            break;
                        }
                        case '1':{
                            $('html').attr('data-sub',0);
                            $('html').removeClass("level"+checkSub);
                            $('html').addClass("submenu level"+$('html').attr('data-sub'));
                            break;
                        }
                        case '2':{
                            $('html').attr('data-sub',1);
                            $('html').removeClass("level"+checkSub);
                            $('html').addClass("submenu level"+$('html').attr('data-sub'));
                            break;
                        }
                        case '3':{
                            $('html').attr('data-sub',2);
                            $('html').removeClass("level"+checkSub);
                            $('html').addClass("submenu level"+$('html').attr('data-sub'));
                            break;
                        }
                    }
                }else{
                    $(".back-link").removeClass('clicked');
                    $('html').removeAttr('data-sub');
                    $('html').removeClass("submenu");
                }
            }
        },

        pushClose: function(){
            this._destroy();
        },

        _destroy: function(){
            var seft = this;

            $(".back-link").removeClass('clicked');

            if(this.options.containerMenu){
                this.options.containerMenu.find('li').removeClass('swiped');
                this.options.containerMenu.find('.opener').removeClass('swiped');
                this.options.containerMenu.find('.submenu').removeClass('swiped');
            }

            $('html').removeAttr('data-sub');
            $('html').removeClass("submenu level0 level1 level2 level3");

            $("li.level0.parent").removeClass("selected");
            $("li.level0.parent > a > span").removeClass("selected");

            setTimeout(function () {
                $('html').removeClass('nav-open');
            }, 80);

            setTimeout(function () {
                $('html').removeClass('nav-before-open');
            }, 300);
        }
    });
    return $.forix.megamenu;
});