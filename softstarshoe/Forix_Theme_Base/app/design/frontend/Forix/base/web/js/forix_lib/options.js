define([
    'jquery',
    'matchMedia',
    "jquery/ui",
    "forix/libs",
    'domReady!'
], function($){
    "use strict";

    $.widget('forix.optionsProduct', {
        options: {
            header: "[data-role=title]",
            content: "[data-role=content]",
            trigger: "[data-role=trigger]",
            counterLine: 1,
            activeCls: 'active',
            toggleTitle: "",
            toogleTitleCur: ""
        },

        _create: function() {
            this._processPanels();
            this._bind("click");
        },

        _processPanels: function(){
            this.element.attr("data-options", "true");
            this.element.attr('aria-selected','false');
            this.container = this.element;
            if (typeof this.options.header === "object") {
                this.header = this.options.header;
            } else {
                var headers = this.element.find(this.options.header);
                if(headers.length > 0) {
                    this.header = headers.eq(0);
                } else {
                    this.header = this.element;
                }
            }

            if (typeof this.options.content === "object") {
                this.content = this.options.content;
            } else {
                this.content = this.header.next(this.options.content).eq(0);
            }
            if (typeof this.options.trigger === "object") {
                this.trigger = this.options.trigger;
            } else {
                var triggers = this.header.find(this.options.trigger);
                if(triggers.length > 0) {
                    this.trigger = triggers.eq(0);
                } else {
                    this.trigger = this.header;
                }
            }
            // hidden Options
            this._forceHidden();
        },

        _destroy: function(){

        },

        _bind: function (event) {
            this.events = {
            };
            var self = this;
            if (event) {
                $.each(event.split(" "), function (index, eventName) {
                    self.events[ eventName ] = "_eventHandler";
                });
            }
            this._off(this.trigger);
            if(!this.options.disabled) {
                this._on(this.trigger, this.events);
            }
        },

        _eventHandler: function (event) {
            var seft = this,
                ele = seft.element,
                miniCart = $('[data-block=\'minicart\']');
            if(ele.attr('aria-selected') ==='true'){
                ele.attr('aria-selected','false');
                ele.removeClass(this.options.activeCls);
                seft._forceHidden();
                if (miniCart.data('forixScrollData')) {
                    miniCart.scrollData('updateScroll');
                }
                this._trigger('afterClick');
            }else{
                ele.attr('aria-selected','true');
                ele.addClass(this.options.activeCls);
                seft._refeshHidden();
                if (miniCart.data('forixScrollData')) {
                    miniCart.scrollData('updateScroll');
                }
                this._trigger('afterClick');
            }
            event.preventDefault();

        },

        _forceHidden: function(){
            var seft = this,
                checkHidden = 0,
                curLabel = (seft.header.data('change'))? seft.header.data('change') : seft.options.toogleTitleCur;

            // hidden Options
            this.container.find('.label').each(function(i){
                if(seft.options.counterLine > -1 && (i > (seft.options.counterLine - 1))){
                    checkHidden ++;
                    $(this).addClass("hidden");
                }
            });
            this.container.find('.values').each(function(i){
                if(seft.options.counterLine > -1 && (i > (seft.options.counterLine - 1))){
                    checkHidden ++;
                    $(this).addClass("hidden");
                }
            });
            if(checkHidden = 0){
                seft.header.hide();
            }
            if( seft.options.toggleTitle && seft.options.toogleTitleCur){
                seft.header.text(curLabel);
            }
        },

        _refeshHidden: function(){
            var seft = this,
                newLabel = (seft.header.data('current'))? seft.header.data('current') : seft.options.toggleTitle;
            this.container.find('.label').removeClass("hidden");
            this.container.find('.values').removeClass("hidden");
            if(seft.options.toggleTitle && seft.options.toogleTitleCur){
                seft.header.text(newLabel);
            }
        }
    });

    return $.forix.optionsProduct;
});