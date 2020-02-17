define([
    'jquery',
    'underscore',
    'uiComponent',
    './lib/html2canvas'
], function ($, _, Component, html2canvas) {
    'use strict';
    
    return Component.extend({
        defaults: {
            formSelector:   '#feedback-form',
            buttonSelector: '#feedback-button'
        },
        
        initialize: function () {
            this._super();
            
            _.bindAll(this, 'onSubmit');
            
            this.form = $(this.formSelector);
            this.button = $(this.buttonSelector);
            
            this.form.on('submit', this.onSubmit);
        },
        
        onSubmit: function () {
            this.button.addClass('_loading');
            
            $('*').css('text-shadow', 'none');
            
            try {
                html2canvas($('body')[0]).then(function (canvas) {
                    var $input;
                    
                    $input = $('<input />').attr({
                        name:  'dataURL',
                        value: canvas.toDataURL('image/png'),
                        type:  'hidden'
                    });
                    
                    this.form.append($input);
                    
                    this.button.removeClass('_loading');
                    
                    this.form.unbind('submit', this.onSubmit);
                    this.form.submit();
                }.bind(this));
            } catch (error) {
                this.form.unbind('submit', this.onSubmit);
                this.form.submit();
            }
            
            return false;
        }
    });
});