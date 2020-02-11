define([
    'Magento_Ui/js/form/element/abstract',
    'mage/url',
    'ko',
    'jquery',
    'jquery/ui'
], function (Abstract, url, ko, $) {
    'use strict';

    return Abstract.extend({
        defaults: {
            elementTmpl: 'Forix_CustomShipping/autocomplete/postcodeInput'
        },
        selectedOption: ko.observable(''),
        setSearchElement: function (element) {
            var zipcodes = window.checkoutConfig.zipcode_us;
            var availableTags = $.parseJSON(zipcodes);
            $(element).autocomplete({
                source: function( request, response ) {
                    var matcher = new RegExp("^" + $.ui.autocomplete.escapeRegex(request.term), "i");
                    response($.grep(availableTags, function (item) {
                            return matcher.test(item);
                        })
                    );
                },
                appendTo: '.autocomplete_billing'
            });
        },
        autoFillCityState: function (data, event){
            var id = $(this)[0].uid;
            var form = $('#'+id).parents('form');
            var val = $('#'+id).val();
            var allZips = $.parseJSON(window.checkoutConfig.zipcode_full);
            /* var shippingAddress = quote.shippingAddress();*/
            /*if (shippingAddress['customAttributes'] === undefined) {
             shippingAddress['customAttributes'] = {};
             }*/
            if(typeof allZips[val] != 'undefined') {
                if (form.find('select[name="country_id"]').val() == 'US') {
                    /*  shippingAddress['city'] = allZips[val][0];
                     shippingAddress['region'] = allZips[val][2];
                     shippingAddress['region_id'] = allZips[val][1];*/
                    form.find('input[name="city"]').val(allZips[val][0]);
                    form.find('input[name="region"]').val(allZips[val][2]);
                    form.find('select[name="region_id"]').val(allZips[val][1]);
                    form.find('select[name="region_id"] option').filter(function () {
                        return $(this).val() === allZips[val][1];
                    }).attr('selected', true);
                    form.find('[name="region_id"]').trigger("change");
                    form.find('input[name="city"]').trigger("change");
                    setTimeout(function () {
                        $('.message.warning').hide();
                    }, 2000);
                }
            }
        },
        selectedValue: ko.observable('')
    });
});