/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Ui/js/form/element/abstract',
    'Magento_Ui/js/lib/validation/validator',
    'mage/validation',
    'mage/translate'
], function ($, Abstract, validator, validation, $t) {
    'use strict';


    return Abstract.extend({
        defaults: {
            elementTmpl: 'ui/form/element/input'
        },

        /**
         * Validates itself by it's validation rules using validator object.
         * If validation of a rule did not pass, writes it's message to
         * 'error' observable property.
         *
         * @returns {Object} Validate information.
         */
        validate: function () {
            var self = this,
                value = this.value(),
                result = validator(this.validation, value, this.validationParams),
                message = !this.disabled() && this.visible() ? result.message : '',
                isValid = this.disabled() || !this.visible() || result.passed;

            var arrFullName = value.split(' ');
            arrFullName  = arrFullName .filter(function(x) {
                return (x !== (undefined || null || ''));
            });
            if (arrFullName.length <= 1) {
                isValid = false;
                message = $t('Please enter both your first and last name.');
            }
            this.error(message);
            this.bubble('error', message);

            //TODO: Implement proper result propagation for form
            if (!isValid) {
                this.source.set('params.invalid', true);
            }

            return {
                valid: isValid,
                target: this
            };
        }
    });
});