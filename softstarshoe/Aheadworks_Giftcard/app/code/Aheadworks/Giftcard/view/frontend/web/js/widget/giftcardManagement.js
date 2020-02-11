/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery'
], function($){
    'use strict';

    $.widget('mage.awGiftCardManagement', {
        options: {
            checkCodeSelector: '[data-role=aw-giftcard-check-code-action]',
            applyCodeSelector: '[data-role=aw-giftcard-apply-action]',
            resultSelector: '#aw_giftcard__code_info'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._bind();
        },

        /**
         * Event binding
         */
        _bind: function () {
            var handlers = {};

            handlers['click ' + this.options.checkCodeSelector] = this._onClickCheckCode;
            handlers['click ' + this.options.applyCodeSelector] = this._onSubmitForm;
            this._on(handlers);
        },

        /**
         * Click on check code action
         *
         * @param {Object} event
         * @private
         */
        _onClickCheckCode: function(event) {
            event.preventDefault();
            $(this.options.resultSelector).html('');
            if (!this.validate()) {
                return;
            }
            var self = this,
                data = $(this.element).serializeArray(),
                url = $(this.options.checkCodeSelector).data('url');

            $('body').trigger('processStart');
            $.ajax({
                url: url,
                data: data,
                method: 'post',
                context: this
            }).success(function(response) {
                if (response) {
                    $(self.options.resultSelector).html(response);
                }
            }).always(function() {
                $('body').trigger('processStop');
            });
        },

        /**
         * Click on apply code action
         *
         * @param {Object} event
         * @private
         */
        _onSubmitForm: function(event) {
            event.preventDefault();
            var url = $(this.options.applyCodeSelector).data('url');

            if (!this.validate()) {
                return;
            }

            $('body').trigger('processStart');
            $(this.element).attr('action', url).submit();
        },

        /**
         * Coupon form validation
         *
         * @returns {Boolean}
         */
        validate: function () {
            return $(this.element) && $(this.element).validation() && $(this.element).validation('isValid');
        }
    });

    return $.mage.awGiftCardManagement;
});
