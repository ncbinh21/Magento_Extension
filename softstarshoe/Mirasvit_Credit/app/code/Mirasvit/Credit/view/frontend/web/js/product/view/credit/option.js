define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    'priceBox',
    'jquery/ui'
], function ($, _, mageTemplate, utils) {
    'use strict';

    var globalOptions = {
        fromSelector:        '#product_addtocart_form',
        optionType:          '',
        priceHolderSelector: '.price-box',
        optionsSelector:     '.credit-option'
    };

    $.widget('mage.priceOptions', {
        options: globalOptions,

        _init: function initPriceBundle() {
            $(this.options.optionsSelector, this.element).trigger('change');
        },

        _create: function createCreditOptions() {
            var form = this.element,
                options = $(this.options.optionsSelector, form);

            options.on('change', this._onCreditOptionChanged.bind(this));
        },

        /**
         * @private
         */
        _onCreditOptionChanged: function onOptionChanged(event) {
            var changes,
                option = $(event.target),
                optionPrice = 0;

            if (option[0].nodeName == "INPUT") { // range
                var pricePerCredit = $(option).attr('data-price');
                optionPrice = ($(option).val() * pricePerCredit).toFixed(2);
            } else { // fixed
                optionPrice = $(option[0].selectedOptions).attr('data-price');
            }

            option.data('optionContainer', option.closest(this.options.controlContainer));

            changes = {
                creditOptionField: {
                    basePrice: {
                        amount: optionPrice
                    },
                    finalPrice: {
                        amount: optionPrice
                    },
                    oldPrice: {
                        amount: optionPrice
                    }
                }
            };

            $(this.options.priceHolderSelector).trigger('updatePrice', changes);
        },

        _setOptions: function setOptions(options) {
            $.extend(true, this.options, options);
            this._super(options);

            return this;
        }
    });

    return $.mage.priceOptions;
});
