/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 13/09/2018
 * Time: 11:14
 */


define([
    'uiComponent',
    'jquery',
    'ko',
    'mage/translate',
    'underscore',
    'checkWizardAvailable',
    '../model/url-builder',
    'forix/plugins/select2'
], function (Component, jquery, ko, $t, _, checkWizardAvailable, urlBuilder) {
    'use strict';

    return Component.extend({
        defaults: {
            resultHtml: '',
            noResultId: ''
        },
        callSelectsJS: function () {
            var _self = this;
            var abc = jquery(_self.selectElement).select2({
                dropdownParent: jquery('.configurator-form .rig-model-wraper'),
                escapeMarkup: function (m) {
                    return m;
                },
                language: {
                    noResults: function () {
                        return "";
                    }
                },
                matcher: function (params, data) {
                    abc.term = params.term;
                    if (jquery.trim(params.term) === '') {
                        return data;
                    }
                    if (typeof data.text === 'undefined') {
                        return null;
                    }

                    var termUpper = params.term.toUpperCase(), modifiedData = null,
                        index = data.text.toUpperCase().indexOf(termUpper);
                    if (index > -1) {
                        modifiedData = jquery.extend({}, data, true);
                        var text = modifiedData.text, sub = text.substring(index, index + termUpper.length);
                        text = text.replaceBetween(index,index + termUpper.length,'<span class="bold">' + sub + '</span>');
                        modifiedData.text = text;
                    }
                    return modifiedData;
                }
            });
            abc.on('select2:select', _self._onDropDownSelect.bind(this));
        },
        isLoading: ko.observable(false),
        initialize: function () {
            this._super();
        },
        _onDropDownSelect: function (e) {
            var _self = this;
            jquery(_self.noResultId).hide();
            //e.params.data.element.value
            if(e.params.data.element.value) {
                var _value = e.params.data.element.value, _label = e.params.data.text, _name = e.params.data.element.parentElement.name;
                (new checkWizardAvailable({
                    validateData: function (res) {
                        if(res.wizard_id) {
                            if (_self.currentTargetWizardId != res.wizard_id) {
                                var request = {};
                                request[_name] = _value;
                                var message = _self.resultHtml
                                    .replace('{configurator_title}', res.title)
                                    .replace('{value}', _label)
                                    .replace('{configurator_url}', _self.baseUrl +  res.identifier + "?" +
                                        Object.keys(request).map(k => encodeURIComponent(k) + '=' + encodeURIComponent(request[k]))
                                        .join('&'));
                                jquery(_self.noResultId).html(message).show();
                                return false;
                            }
                            return true;
                        }else{
                            if(_self.finalMessage) {
                                jquery(_self.noResultId).html(_self.finalMessage.replace('{value}', _label)).show();
                            }
                        }
                        return false;
                    }
                })).submitForm(urlBuilder.createUrl(this.validateWizardUrl, {}), {'param': JSON.stringify({ 'option_value':  _value , 'wizard_id': _self.currentTargetWizardId})}, this.isLoading);
            }
            return false;
        }
    });

});