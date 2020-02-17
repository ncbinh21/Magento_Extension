/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 14/06/2018
 * Time: 10:55
 */

define([
    'uiComponent',
    'jquery',
    'ko',
    'mage/translate',
    'underscore',
    'Forix_ProductWizard/js/model/wizard-data',
    'Forix_ProductWizard/js/action/next-step',
    'Forix_ProductWizard/js/action/set-value',
    'tabs-title',
    "configuratorAddToCart",
    'configuratorTabs',
    'validation',
    'forix/plugins/select2'
], function (Component, $, ko, $t, _, wizardData, nextStep, setValue, tabTitles, configuratorAddToCart) {
    'use strict';

    var replaceText = function (term, text) {
        var rx = new RegExp(term, "g");
        return text.replace(rx, '<span class="bold">' + term + '</span>');
    };

    String.prototype.replaceBetween = function (start, end, what) {
        return this.substring(0, start) + what + this.substring(end);
    };
    return Component.extend({
        isLoading: wizardData.isLoading,
        initObservable: function () {
            this._super();
            var _self = this;
            $(document).on('click', '.btn-continue', nextStep);
            $(document).on('show-notify-message', function (event, newValue) {
                var msg = $('.step-message').addClass('no-display');
                if (newValue) {
                    $('.step-message-' + wizardData.get('tab-index')().next).removeClass('no-display');
                }
            });
            return this;
        },
        timeoutCallSelect: [],
        callSelecct: function(element){
            var abc = element.select2({
                escapeMarkup: function (m) {
                    return m;
                },
                matcher: function (params, data) {
                    if ($.trim(params.term) === '') {
                        return data;
                    }
                    if (typeof data.text === 'undefined') {
                        return null;
                    }
                    var termUpper = params.term.toUpperCase(), modifiedData = null,
                        index = data.text.toUpperCase().indexOf(termUpper);
                    if (index > -1) {
                        modifiedData = $.extend({}, data, true);
                        var text = modifiedData.text, sub = text.substring(index, index + termUpper.length);
                        text = text.replaceBetween(index,index + termUpper.length, '<span class="bold">' + sub + '</span>');
                        modifiedData.text = text;
                    }
                    return modifiedData;
                }
            });
        },
        callSelectsJS: function () {
            this.isLoading(true);
            var self = this;
            $('#configurator-step-functions').configuratorTabs({"openedState": "active"});
            var selectControls = $('.wizard-control-select.enabled-select');
            this.callSelecct(selectControls);
            $(document).on('product-wizard-items-depended', function(e, optionKey){
                 var _option = $('option[data-option="' + optionKey + '"]');
                 if(_option.length) {
                     if(!self.timeoutCallSelect[_option.parent()[0].className]) {
                         self.timeoutCallSelect[_option.parent()[0].className] = setTimeout(function(){
                             self.callSelecct(_option.parent());
                             self.timeoutCallSelect[_option.parent()[0].className] = false;
                         }, 500);
                     }
                 }
            });
            this.isLoading(false);
        },
        /**
         * Call this function to show all element not recommended
         * @param event
         */
        showAll: function (obj, event) {
            var element = event.currentTarget;
            if (undefined !== element) {
                var itemClass = element.dataset.items;
                $(itemClass).removeClass('no-display').addClass('display');
                $('.btn-continue',$(itemClass)).prop('disabled',false).attr('disabled', false);
                $(itemClass + '.show-all').hide();
            }
        },
        addSummaryTotalOption: function (key, value) {
            /* Store selected options to summary at final step */
            wizardData.addOptionKeys(key);
            wizardData.set(key, value);
            if (wizardData.get('tab-index')().current >= 0) {
                wizardData.buildSharedUrlPath(wizardData.get('tab-index')().current);
            }
        },
        optionsAdditionSelected: function (event, key, data) {
            var element = event.currentTarget;
            if (undefined !== element) {
                var value = {
                    'title': data.title,
                    'value': element.selectedOptions[0].dataset.option,
                    'attribute_code': element.dataset.attribute,
                    'label': element.selectedOptions[0].innerHTML
                };
                this.addSummaryTotalOption(key, value);
            }
        },
        setGroupItemValue: function (key, value) {
            wizardData.set(key, value);
        },
        translateText: function (element) {
            const regex = /({.*})/gm;
            var m;
            element.innerHTML = (element.dataset.text);
            //This line to make sure function will be re-call after tab changed
            wizardData.get('tab-index').subscribe(function () {
                var text = element.dataset.text;
                while ((m = regex.exec(text)) !== null) {
                    m.forEach(function (match, groupIndex) {
                        var attr = match.replace('{', '').replace('}', '');
                        var _value = $('[data-attribute="' + attr + '"] option:selected').text();
                        text = text.replace(match, _value);
                        return false;
                    });
                }
                element.innerHTML = (text);
            });
        },

        triggerClick: function (element) {
            setValue(element);
        }
    });

});