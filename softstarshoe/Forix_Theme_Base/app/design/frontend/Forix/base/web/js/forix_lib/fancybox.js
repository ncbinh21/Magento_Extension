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
            margin: 9,
            padding: 0
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
                padding : seft.options.padding,
                width: '100%',
                autoSize: true,
                autoWidth: true,
                autoHeight: true,
                fitToView : false,
                helpers : {
                    overlay: {
                        fixed: false
                    }
                },
                iframe : {
                    scrolling : 'auto'
                },
                maxWidth: '100%',
                margin: seft.options.margin,
                'autoScale' : true,
                afterShow: function(){
                    setTimeout(function(){
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