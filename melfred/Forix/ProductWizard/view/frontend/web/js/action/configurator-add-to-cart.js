/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/storage',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/customer-data',
    'jquery/ui'
], function ($, storage, $t, globalMessageList, customerData) {
    'use strict';

    return function (_options) {
        this.options = $.extend({}, {
            processStart: null,
            processStop: null,
            minicartSelector: '[data-block="minicart"]',
            addToCartButtonSelector: '.action.configurator-tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        }, _options);


        this.submitForm = function (url, formData, isLoading, messageContainer) {
            var self = this;
            messageContainer = messageContainer || globalMessageList;
            self.disableAddToCartButton();
            $(self.options.minicartSelector).trigger('contentLoading');
            $.ajax({
                url: url,
                data: formData,
                type: 'post',
                dataType: 'json',

                /** @inheritdoc */
                beforeSend: function () {
                    isLoading(true);
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStart);
                    }
                },

                /** @inheritdoc */
                success: function (res) {
                    if (self.isLoaderEnabled()) {
                        $('body').trigger(self.options.processStop);
                    }
                    if (res.messages) {
                        messageContainer.add(res.messages);
                    }
                    self.enableAddToCartButton();
                    isLoading(false);
                },
                error: function(res){
                    messageContainer.addErrorMessage(res.statusText);
                    self.enableAddToCartButton("Failed");
                    isLoading(false);
                }
            });
        };

        /**
         * @return {Boolean}
         */
        this.isLoaderEnabled = function () {
            return this.options.processStart && this.options.processStop;
        };



        this.disableAddToCartButton = function () {
            var addToCartButtonTextWhileAdding = this.options.addToCartButtonTextWhileAdding || $t('Adding...'),
                addToCartButton = $(this.options.addToCartButtonSelector);

            addToCartButton.addClass(this.options.addToCartButtonDisabledClass);
            addToCartButton.find('span').text(addToCartButtonTextWhileAdding);
            addToCartButton.attr('title', addToCartButtonTextWhileAdding);
        };

        this.enableAddToCartButton = function (text) {
            var addToCartButtonTextAdded = (text ? text : (this.options.addToCartButtonTextAdded || $t('Added'))),
                self = this,
                addToCartButton = $(this.options.addToCartButtonSelector);

            addToCartButton.find('span').text(addToCartButtonTextAdded);
            addToCartButton.attr('title', addToCartButtonTextAdded);

            var check_cookie = $.cookie('domestic-cookie');
            var addTo = 'Add to Quote';
            if(check_cookie == 1){
                addTo = 'Add To Cart';
            }
            setTimeout(function () {
                var addToCartButtonTextDefault = self.options.addToCartButtonTextDefault || $t(addTo);

                addToCartButton.removeClass(self.options.addToCartButtonDisabledClass);
                addToCartButton.find('span').text(addToCartButtonTextDefault);
                addToCartButton.attr('title', addToCartButtonTextDefault);
            }, 1000);
        }

    };
});
