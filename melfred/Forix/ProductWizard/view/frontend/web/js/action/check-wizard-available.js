/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'mage/storage',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'jquery/ui'
], function ($, storage, $t, globalMessageList) {
    'use strict';

    return function (_options) {
        this.options = $.extend({}, {
            startWizardButtonSelector: '.configurator-form #configurator-submit',
            startWizardDisabledClass: 'disabled',
            startWizardTextWhileValidate: 'Validating...',
            startWizardTextDefault: 'Start Configuring',
            validateData: function(res){return true;},
        }, _options);


        this.submitForm = function (url, formData, isLoading) {
            var self = this;
            self.disablestartWizardButton();

            $.ajax({
                url: url,
                data: formData,
                type: 'get',
                /** @inheritdoc */
                beforeSend: function () {
                    isLoading(true);
                },

                /** @inheritdoc */
                success: function (res) {
                    if(self.options.validateData(res)){
                        self.enablestartWizardButton();
                    }else{
                        self.disablestartWizardButton(self.options.startWizardTextDefault);
                    }
                    isLoading(false);
                },
                error: function(res){
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



        this.disablestartWizardButton = function (text) {
            var startWizardButtonTextWhileAdding = (text? text:  this.options.startWizardTextWhileValidate || $t('Validating...')),
                startWizardButton = $(this.options.startWizardButtonSelector);
            startWizardButton.prop('disabled', true);
            startWizardButton.addClass(this.options.startWizardDisabledClass);
            startWizardButton.find('span').text(startWizardButtonTextWhileAdding);
            startWizardButton.attr('title', startWizardButtonTextWhileAdding);
        };

        this.enablestartWizardButton = function (text) {
            var startWizardButtonTextAdded = (text ? text : (this.options.startWizardButtonTextValidated || $t('Start Configuring'))),
                self = this,
                startWizardButton = $(this.options.startWizardButtonSelector);
            startWizardButton.prop('disabled', false);
            startWizardButton.find('span').text(startWizardButtonTextAdded);
            startWizardButton.attr('title', startWizardButtonTextAdded);

            setTimeout(function () {
                var startWizardButtonTextDefault = self.options.startWizardTextDefault;
                startWizardButton.removeClass(self.options.startWizardDisabledClass);
                startWizardButton.find('span').text(startWizardButtonTextDefault);
                startWizardButton.attr('title', startWizardButtonTextDefault);
            }, 1000);
        }
    };
});
