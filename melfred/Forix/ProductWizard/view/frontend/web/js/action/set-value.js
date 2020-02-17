/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 09/01/2019
 * Time: 20:31
 */


define([
    'jquery',
    'underscore',
    'Forix_ProductWizard/js/model/wizard-data',
], function ($, _, wizardData) {
    'use strict';
    return function (element) {
        if (undefined !== element) {
            var stepCurrent = element.dataset.current, nextTo = element.dataset.next, dataKey = element.dataset.key;
            if (nextTo) {
                wizardData.set('tab-index', {
                    'current': parseInt(stepCurrent), /* Step index using 0, 1, 2 */
                    'next': parseInt(nextTo), /* Step key using 0,1,2 */
                    'switch': element.dataset.switch
                });
            }
            if (dataKey) {
                var value = {
                    'value': element.dataset.option,
                    'label': element.dataset.product
                };
                wizardData.set(dataKey, value);
            }
            if (element.dataset.product) {
                /*var dataForm = "";
                for (var i = 1; i < nextTo + 1; i++) {
                    dataForm += ($('.wizard-form-main-' + i).serialize()) + "&";
                }*/
                $(document).trigger('product-wizard-items-clear');
                wizardData.getProductData(element.dataset.product, element.dataset.itemset, wizardData.isLoading);
            }
        }
        return true;
    }
});