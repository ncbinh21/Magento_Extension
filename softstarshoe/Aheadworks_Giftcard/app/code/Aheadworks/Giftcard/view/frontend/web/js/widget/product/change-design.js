/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery'
], function($){
    'use strict';

    $.widget('mage.awGiftCardChangeDesign', {
        options: {
            templateValueSelector: '#aw_gc_template',
            value: ''
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this.element.on('click', $.proxy(this.onClick, this));
        },

        /**
         * Click on element
         * @private
         */
        onClick: function() {
            this.element.siblings('.aw-gc-product-form-options__template-option').removeClass('selected');
            this.element.addClass('selected');
            $(this.options.templateValueSelector).val(this.options.value).trigger('change');
        }
    });

    return $.mage.awGiftCardChangeDesign;
});
