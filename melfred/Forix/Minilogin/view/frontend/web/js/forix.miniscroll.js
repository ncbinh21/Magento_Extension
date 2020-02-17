define([
    'jquery',
    'matchMedia',
    'forix/plugins/jscrollpane',
    'domReady!'
], function($){
    "use strict";

    $.widget('forix.scrollMini', {
        options: {

        },
        _create: function() {
            this.initScroll();
            this.resize();
            this.checkMessage();
        },

        initScroll: function(){
            var self = this;

                self.checkHeight();
        },

        checkHeight: function(){
            var self = this,
                target = this.element,
                height = $(window).outerHeight() - $('.page-header').outerHeight();

            if($('.block-customer-login').outerHeight() <=height){
                target.css("height",$('.block-customer-login').outerHeight()+1);
            }else{
                target.css("height",height - 32);
            }

            if(target.data('jsp')){
                target.data('jsp').reinitialise();
            }else{
                self.jScrollpane = target.jScrollPane({mouseWheelSpeed: 30}).data().jsp;
            }
        },

        updateScroll: function(){
            this.initScroll();
        },

        destroyScroll: function(){
            var self = this,
                target = this.element;

            if(self.jScrollpane){
                target.css("height",900);
                target.removeAttr("style");
                self.jScrollpane.destroy();
                self.jScrollpane ='';
                target.data('jsp',false);
            }

        },

        resize: function(){
            var self = this;
            $(window).resize(function(){
                if(self.checkHeight()){
                    self.updateScroll();
                }
            });
        },

        reInit: function(){
            var self = this,
                target = this.element,
                height = $(window).outerHeight() - $('.page-header').outerHeight();

            if($('.block-customer-login').outerHeight() <=height){
                target.css("height",$('.block-customer-login').outerHeight()+1);
            }else{
                target.css("height",height - 32);
            }

            if(target.data('jsp')){
                target.data('jsp').reinitialise();
            }
        },

        checkMessage:function(){
            var self = this,
                target = this.element,
                miniloginTimer = 0;
                target.bind("DOMNodeInserted DOMSubtreeModified DOMNodeRemoved",function(){
                    clearTimeout(miniloginTimer);
                    miniloginTimer = setTimeout(function(){
                        self.updateScroll();
                    },100);
                });
        }
    });

    return $.forix.scrollMini;
});