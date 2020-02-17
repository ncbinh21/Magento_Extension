
define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'ko',
    'jquery',
    'jquery/ui'
], function (_, registry, Abstract, ko, $) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        // REF-PATCH START avoid premature ZIP code validation (by using less strict compare method), 08.12.2016
        /**
         * Initializes observable properties of instance
         *
         * @returns {Abstract} Chainable.
         */
        initObservable: function () {
            this._super();

            this.value.equalityComparer = function(a, b) {
                return (!a && !b) || (a == b);
            };

            return this;
        },
        // REF-PATCH END

        /**
         * @param {String} value
         */
        update: function (value) {
            var country = registry.get(this.parentName + '.' + 'country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            option = options[value];

            if (option['is_zipcode_optional']) {
                this.error(false);
                this.validation = _.omit(this.validation, 'required-entry');
            } else {
                this.validation['required-entry'] = true;
            }

            this.required(!option['is_zipcode_optional']);
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
                appendTo: '#autocomplete_div'
            });
        },

        autoFillCityState: function (data, event){
            var id = $(this)[0].uid;
            var form = $('#'+id).parents('form');
            var val = $('#'+id).val();
            var allZips = $.parseJSON(window.checkoutConfig.zipcode_full);

            if(typeof allZips[val] != 'undefined'){
                form.find('input[name="city"]').val(allZips[val][0]);
                form.find('input[name="region"]').val(allZips[val][2]);
                form.find('select[name="region_id"]').val(allZips[val][1]);
                form.find('select[name="region_id"] option').filter(function() {
                    return $(this).val() === allZips[val][1];
                }).attr('selected', true);
                form.find('input[name="postcode"]').val(val);
                form.find('[name="region_id"]').trigger("change");
                form.find('input[name="city"]').trigger("change");
                setTimeout(function(){
                    $('.message.warning').hide();
                }, 2000);
            }
        },
        selectedValue: ko.observable('')
    });
});
