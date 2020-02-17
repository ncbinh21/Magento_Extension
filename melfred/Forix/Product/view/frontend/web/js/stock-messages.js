/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 01/08/2018
 * Time: 13:22
 */

define([
    "jquery",
    "underscore",
    "Magento_ConfigurableProduct/js/configurable",
    "Magento_Swatches/js/swatch-renderer",
], function ($, _, configurable, swatchRender) {
    "use strict";
    $.widget('mage.stockMessages', {
        options: {
            stockContentClass: '.stock-message-content',
            timeoutTracker: null,
        },
        _create: function () {
            this.options.parentStockMessage = $(this.options.stockContentClass).html();
            this.options.stockContent = $(this.options.stockContentClass);
            $(swatchRender.prototype.options.selectorProductPrice).on('updatePrice', this._updatePrice.bind(this));
            $(configurable.prototype.options.priceHolderSelector).on('updatePrice', this._updatePrice.bind(this));
        },
        _updatePrice: function (e) {
            var _self = this;
            clearTimeout(this.options.timeoutTracker);
            // noinspection JSValidateTypes
            this.options.timeoutTracker = setTimeout(function () {
                var selectedKey = [];
                _self.settingsForKey = $('select.super-attribute-select, div.swatch-option.selected, select.swatch-select');
                if (_self.settingsForKey.length) {
                    for (var i = 0; i < _self.settingsForKey.length; i++) {
                        if (_self.settingsForKey[i].id !== 'attribute' + _self.options.config.rigModelId) {
                            let _value = '';
                            if (parseInt(_self.settingsForKey[i].value) > 0) {
                                _value = _self.settingsForKey[i].value;
                            }
                            if (parseInt($(_self.settingsForKey[i]).attr('option-id')) > 0) {
                                _value = $(_self.settingsForKey[i]).attr('option-id');
                            }
                            selectedKey.push(_value);
                        }
                    }
                }
                _self._reloadMessageContent(selectedKey.join(','));
            }, 1000);
        },
        _reloadMessageContent: function (key) {
            if (key) {

                if (undefined !== this.options.config.messages && undefined !== this.options.config.messages[key]) {
                    this.options.stockContent.html(this.options.config.messages[key].stock_message);
                } else {
                    this.options.stockContent.html(this.options.parentStockMessage);
                }
            }
        }
    });
    return $.mage.stockMessages;
});