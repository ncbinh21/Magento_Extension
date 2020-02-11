define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    //  Responsive menu
    $.widget('forix.scrollToTop', {
        options: {
            text: "Back to top",
            position: "before",// [before|after]
            duration: 700,
            clsName: 'back-to-top',
            scrollTo: ''// .class|#id that will scroll to.
        },

        _create: function() {
            this._process();

            this._bind();
        },

        _process: function(){
            var self = this,
                ele ="<div id='"+self.options.clsName+"' class='"+self.options.clsName+"'><span>"+self.options.text+"</span></div>";

            self.ele = $('#'+self.options.clsName);

            if($(self.element).length){
                if(self.options.position == 'after'){
                    $(self.element).append(ele);
                }

                if(self.options.position == 'before'){
                    $(self.element).prepend(ele);
                }
            }
        },

        _bind: function(){
            var self = this,
                events = {
                click: "_toTop"
            };

            // this._off('.'+self.options.clsName);
            this._on('.'+self.options.clsName, events);
        },

        _toTop: function(event){
            var self = this,
                scrollTop = 0;

            event.preventDefault();

            if(self.options.scrollTo){
                scrollTop = $(self.options.scrollTo).offset().top;
            }

            $('html,body').animate({
                scrollTop: scrollTop
            }, self.options.duration);
        }
    });

    return $.forix.scrollToTop;
});
