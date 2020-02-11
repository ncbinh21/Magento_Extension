define([
    "jquery",
    "matchMedia",
    "jquery/ui",
    "forix/libs",
    "forix/plugins/fancybox",
    "domReady!"
], function($){
    "use strict";

    $.widget("forix.fancyboxCustom", {
        options: {
            margin: 10,
            padding: 0,
            minWidth: 300,
            maxWidth: '100%',
            addClass: false
        },

        _create: function() {
            this._default();
        },

        _init: function(){

        },

        _default: function(){
            var ele = this.element,
                seft = this;

            ele.fancybox({
                margin: seft.options.margin,
                padding : seft.options.padding,
                minWidth: seft.options.minWidth,
                maxWidth: seft.options.maxWidth,
                autoScale : true,
                autoSize: true,
                autoWidth: true,
                autoHeight: true,
                fitToView : false,
                openEffect: 'elastic',
                closeEffect: 'elastic',
                helpers : {
                    overlay: {
                        fixed: false
                    }
                },
                iframe : {
                    scrolling : 'auto'
                },

                afterShow: function(){
                    if(seft.options.addClass){
                        $(".fancybox-wrap .fancybox-skin").addClass(seft.options.addClass);
                    }

                    setTimeout(function(){
                        console.log("fancybox set height");
                        var $iframeBody = $('.fancybox-wrap .fancybox-iframe').contents().find('body');
                        $('.fancybox-wrap .fancybox-inner').height($iframeBody.height());
                    }, 1500);
                }
            });
        },

        update: function(){
            if($.fancybox){
                $.fancybox.update()
            }
        },

        close: function(){
            if($.fancybox){
                $.fancybox.close();
            }
        }
    });

    return $.forix.fancyboxCustom;
});