/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 13/11/2018
 * Time: 17:26
 */
define([
    'jquery',
    'mage/storage',
    'mage/translate',
    'mage/url',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Ui/js/model/messageList',
    'jquery/ui'
], function ($, storage, $t, url,urlBuilder, globalMessageList) {
    return function (zipcode) {
        var deferred = $.Deferred();

        var formData = {
            "searchCriteria": {
                "filter_groups": [
                    {
                        "filters": [
                            {
                                "field": "zipcode",
                                "value": zipcode,
                                "condition_type": "eq"
                            }]
                    }]
            }
        };
        return $.ajax({
                url:  url.build(urlBuilder.createUrl('/distributor/zipcodes/', {})),
                data: formData,
                type: 'get',
            }).done(function (response) {
                deferred.resolve();
            }).fail(function (response) {
                deferred.reject();
            });
    }
});