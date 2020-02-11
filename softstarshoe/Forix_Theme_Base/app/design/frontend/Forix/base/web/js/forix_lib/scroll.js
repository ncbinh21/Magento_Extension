define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    "forix/libs",
    "forix/libs",
    'forix/plugins/jscrollpane',
    'forix/plugins/mousewheel',
    'domReady!'
], function($){
    "use strict";

    $.widget('forix.scrollData', {
        options: {
            eleScroll: "[data-action=scroll]",
            item: "[data-role=product-item]",
            numberScroll: 3,
            useMobile: false,
            deplay: 1
        },

        _create: function() {
            this._initContent();
            this.resize();
        },

        _init: function(){
            var target = this.element.find(this.options.item),
                counter = this.options.numberScroll;
            if(target.length > counter) {
                this._applyScroll();
            }
        }
        ,

        _initContent: function(){
            var elem = this.element;
            if (typeof this.options.eleScroll === "object") {
                this.eleScroll = this.options.eleScroll;
            } else {
                var eleScroll = this.element.find(this.options.eleScroll);
                if(eleScroll.length > 0) {
                    this.eleScroll = eleScroll.eq(0);
                } else {
                    this.eleScroll = this.element;
                }
            }
            if (typeof this.options.item === "object") {
                this.item = this.options.item;
            } else {
                var item = this.element.find(this.options.item);
                if(item.length > 0) {
                    this.item = item.eq(0);
                } else {
                    this.item = this.element;
                }
            }
        },

        _calHeight: function(){
            var height = 0,
                target = this.element.find(this.options.item),
                outerHeight,
                scrollEle = this.element.find(this.options.eleScroll),
                counter = this.options.numberScroll;
            if(target.length > 0) {
                target.each(function (i) {
                    outerHeight = $(this).outerHeight();
                    if (i < counter) {
                        height += outerHeight;
                    }
                });
            }
            scrollEle.height(height);
        },

        _applyScroll: function(){
            var seft = this.element,
                target = this,
                lgtItems = this.element.find(this.options.item).length;
            if(this.options.useMobile){
                if(seft.find(target.options.eleScroll).length){
                    if(seft.find(target.options.eleScroll).data('jsp')){
                        seft.find(target.options.eleScroll).data('jsp').reinitialise();
                        setTimeout(function () {
                            if(target.options.numberScroll>1)
                                target._calHeight();
                            if (seft.find(target.options.eleScroll).data('jsp')) {
                                seft.find(target.options.eleScroll).data('jsp').reinitialise();
                            }
                        },  (target.options.deplay/2));
                    }else{
                        if(lgtItems >= target.options.numberScroll){
                            self.jScrollpane = seft.find(this.options.eleScroll).jScrollPane({mouseWheelSpeed:30}).data().jsp;
                            setTimeout(function () {
                                if(target.options.numberScroll>1)
                                    target._calHeight();
                                if (seft.find(target.options.eleScroll).data('jsp')) {
                                    seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                }
                            },  target.options.deplay);
                        }
                    }
                }
            }else{
                mediaCheck({
                    media: '(max-width: 767px)',
                    entry: $.proxy(function() {
                        if(self.jScrollpane){
                            self.jScrollpane.destroy();
                            self.jScrollpane ='';
                        }
                        seft.find(this.options.eleScroll).height('auto');
                        seft.attr('data-mobile','1');
                    }, this),
                    exit: $.proxy(function() {

                    }, this)
                });
                mediaCheck({
                    media: '(min-width: 768px)',
                    entry: $.proxy(function() {
                        if(seft.find(target.options.eleScroll).length){
                            if(seft.find(target.options.eleScroll).data('jsp')){
                                seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                setTimeout(function () {
                                    if(target.options.numberScroll>1)
                                        target._calHeight();
                                    if (seft.find(target.options.eleScroll).data('jsp')) {
                                        seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                    }
                                }, (target.options.deplay/2));
                            }else{
                                if(lgtItems >= target.options.numberScroll) {
                                    self.jScrollpane = seft.find(target.options.eleScroll).jScrollPane({mouseWheelSpeed: 30}).data().jsp;
                                    setTimeout(function () {
                                        if (target.options.numberScroll > 1)
                                            target._calHeight();
                                        if (seft.find(target.options.eleScroll).data('jsp')) {
                                            seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                        }
                                    }, target.options.deplay);
                                }
                            }
                            seft.attr('data-mobile','0');
                        }
                    }, this),
                    exit: $.proxy(function() {

                    }, this)
                });
            }
        },

        updateScroll: function(){
            var target = this.element.find(this.options.item),
                counter = this.options.numberScroll;
            if(target.length > counter) {
                this._applyScroll();
            }
        },
        destroy: function(){
            var seft = this.element;
            if(self.jScrollpane){
                self.jScrollpane.destroy();
                self.jScrollpane ='';
            }
            seft.find(this.options.eleScroll).height('auto');
        },
        resize: function(){
            var $window = $(window),
                _wWidth = $window.width(),
                _wHeight = $window.height(),
                watchInt = null,
                seft = this.element,
                target = this,
                lgtItems = this.element.find(this.options.item).length;

            $window.resize(function() {
                if ( _wWidth != $window.width() || _wHeight != $window.height())
                {
                    _wWidth = $window.width();
                    _wHeight = $window.height();

                    if ( watchInt )
                    {
                        clearInterval( watchInt );
                    }
                    watchInt = setTimeout(
                        function()
                        {
                            if(seft.attr('data-mobile')==0 || !seft.attr('data-mobile')){
                                if(seft.find(target.options.eleScroll).length){
                                    if(seft.find(target.options.eleScroll).data('jsp')){
                                        seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                        setTimeout(function () {
                                            if(target.options.numberScroll>1)
                                                target._calHeight();
                                            if (seft.find(target.options.eleScroll).data('jsp')) {
                                                seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                            }
                                        }, (target.options.deplay/2));
                                    }else{
                                        if(lgtItems >= target.options.numberScroll) {
                                            self.jScrollpane = seft.find(target.options.eleScroll).jScrollPane({mouseWheelSpeed: 30}).data().jsp;
                                            setTimeout(function () {
                                                if (target.options.numberScroll > 1)
                                                    target._calHeight();
                                                if (seft.find(target.options.eleScroll).data('jsp')) {
                                                    seft.find(target.options.eleScroll).data('jsp').reinitialise();
                                                }
                                            }, target.options.deplay);
                                        }
                                    }
                                    seft.attr('data-mobile','0');
                                }
                            }else{
                                if(self.jScrollpane){
                                    self.jScrollpane.destroy();
                                    self.jScrollpane ='';
                                }
                                seft.find(target.options.eleScroll).height('auto');
                                seft.attr('data-mobile','1');
                            }
                        }, 100
                    );
                }
            });
        }
    });

    return $.forix.scrollData;
});