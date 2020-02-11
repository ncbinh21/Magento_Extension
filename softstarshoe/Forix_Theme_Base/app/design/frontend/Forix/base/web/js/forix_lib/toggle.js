define([
    'jquery',
    "matchMedia",
    "jquery/ui",
    'mage/toggle',
    'domReady!'
], function ($, mediaCheck) {
    'use strict';

    $.widget('forix.toggleAdvanced', $.mage.toggleAdvanced, {

        options: {
            touchOutSite: true ,// When click outsite - will remove all class have adding
            toggleSingle: false,
            toggleNext : false,
            toggleParent: false
        },

        /**
         * Toggle creation
         * @private
         */
        _create: function() {
            this._super();
            if(this.options.touchOutSite)
                this.checkOutSide();
        },

        _init: function(){
            this._trigger('afterCreated');
        },

        _toggleSelectors: function () {
            if(this.options.toggleSingle){
                this.element.toggleClass(this.options.baseToggleClass);
            }else{
                this._super();
            }
            if(this.options.toggleNext){
                this.element.next().toggleClass(this.options.baseToggleClass);
            }
            if(this.options.toggleParent){
                this.element.parent().toggleClass(this.options.baseToggleClass);
            }
            this._trigger('afterToggle');
        },

        checkOutSide: function(){
            var controlDocument = $(document),
                isTouchDevice = typeof document.ontouchstart !== 'undefined',
                typeEvent = 'click',
                seft = this;

            if(isTouchDevice){
                typeEvent = 'touchstart';
            }
            controlDocument.on(typeEvent, $.proxy(function(e) {
                seft.toggleOutsite(e);
            }, this));
        },

        toggleOutsite: function(e){
            var seft = this;
            // if element is opened and click target is outside it, hide it
            if (!$(seft.element).is(e.target) && !$(seft.element).has(e.target).length && !$(seft.options.toggleContainers).is(e.target) && !$(seft.options.toggleContainers).has(e.target).length) {
                if(this.options.toggleParent){
                    this.element.parent().removeClass(this.options.baseToggleClass);
                }
                if(this.options.toggleNext && !$(seft.element).next().is(e.target) && !$(seft.element).next().has(e.target).length){
                    seft._deactive();
                    this.element.next().removeClass(this.options.baseToggleClass);
                }else if(!this.options.toggleNext){
                    seft._deactive();
                }
            }
        },

        _deactive: function(){
            this.element.removeClass(this.options.baseToggleClass);
            $(this.options.toggleContainers).removeClass(this.options.selectorsToggleClass);
        }

    });
    return $.forix.toggleAdvanced;
});