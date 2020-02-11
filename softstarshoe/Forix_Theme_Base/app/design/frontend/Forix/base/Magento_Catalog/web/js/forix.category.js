define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    "forix/libs",
    'forix/plugins/dotdotdot',
    'forix/libs',
    'forix/scrolltop',
    'domReady!'
], function($){
    "use strict";

    if (typeof Forix == "undefined") {
        var Forix = {};
    }

    Forix.Category={
        init:function(){
            this.backToTop();
        },
        backToTop:function(){
            $('.toolbar-bottom .toolbar').scrollToTop({
                "text":" Back to top"
            });
        }
    }

    return Forix.Category.init();

});