/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 14/06/2018
 * Time: 13:59
 */
define([
    'jquery',
    'rjsResolver',
    'Forix_ProductWizard/js/model/wizard-data',
    'Forix_ProductWizard/js/action/set-value'
], function ($, resolver, wizardData, setValue) {
    'use strict';

    function setDefaultValue(defaultValue, step){
        if (defaultValue.attr) {
            Object.keys(defaultValue.attr).forEach(function (key) {
                if (defaultValue.attr[key]) {
                    try {
                        var event = new Event('change'), _input = null;

                        if (key.indexOf('gp_') === 0) {
                            _input = document.querySelectorAll('.product-wizard-step-' + (step) + ' [name="attr[' + key + ']"]');
                            if(_input.length) {
                                _input[0].dispatchEvent(event);
                            }
                        } else {
                            _input = document.querySelectorAll('.product-wizard-step-' + (step) + ' [data-attribute="' + key + '"]');
                            if(_input.length) {
                                _input[0].dispatchEvent(event);
                            }
                        }
                    } catch (_e) {
                        console.log(_e);
                    }
                }
                return true;
            });
        }
    }
    /**
     * Removes provided loader element from DOM.
     *
     * @param {HTMLElement} $loader - Loader DOM element.
     */
    function hideLoader($loader) {
        var defaultValue = this;
        $loader.parentNode.removeChild($loader);
        wizardData.set('change-url', false);
        try {
            if (defaultValue.step) {
                var step = parseInt(defaultValue.step);
                if(0 === step){
                    setDefaultValue(defaultValue,1);
                    wizardData.set('tab-index', {'current': 0, 'next': -1});
                }
                for (var nextTo = 0; nextTo < step; nextTo++) {
                    setDefaultValue(defaultValue,nextTo + 1);
                    var btnContinue = document.querySelectorAll('.product-wizard-step-' + (nextTo + 1) + ' button.btn-continue');
                    if(btnContinue.length){
                        setValue(btnContinue[0]);
                    }else{
                        btnContinue = document.querySelectorAll('.product-wizard-step-' + (nextTo + 1) + ' .btn-continue[checked="checked"]');
                        if(btnContinue){
                            setValue(btnContinue[0]);
                        }
                    }
                }
            }else{
                wizardData.set('tab-index', {'current': 0, 'next': 0});
            }
        } catch (e) {/* Do nothing make sure we can change url */
        }
        wizardData.set('change-url', true);
    }

    /**
     * Initializes assets loading process listener.
     *
     * @param {Object} config - Optional configuration default data after loaded component
     * @param {HTMLElement} $loader - Loader DOM element.
     */
    function init(config, $loader) {
        resolver(hideLoader.bind(config, $loader));
    }

    return init;
});
