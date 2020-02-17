define([
    "underscore",
    "jquery"
], function (_, $) {
    'use strict';
    return function(options){
        var beforeValue = [];
        $(document).on("sync", "[data-amshopby-filter]", function () {
            syncFilters(this);
        });

        function syncFilters(element) {
            var currentForm = $(element);
            var attributeCode = currentForm.attr('data-amshopby-filter');

            $('[data-amshopby-filter="' + attributeCode + '"]').each(function(){
                if (this !== currentForm.get(0)){
                    beforeValue = $(this).serializeArray();

                    var data = normalizeData(currentForm.serializeArray());
                    delete data['amshopby[attr_price_from][]'];
                    delete data['amshopby[attr_price_to][]'];

                    _(data).each(function(values, name){
                        var element = $(this).find('[name="' + name + '"]'),
                            rate = element.parent().find('.range').attr('rate');

                        if (name == 'amshopby[price][]'
                            && values[0]
                            && !element.closest('.am-filter-items-attr_price').find('[data-am-js="slider-container"]').length
                        ) {
                            var splitValues = values[0].split('-');
                            if (!rate) {
                                rate = element.parent('ol').find('.range').attr('rate');
                            }

                            values[0] = (splitValues[0] * rate) + '-' + (splitValues[1] * rate);
                        }

                        //disabled sync from dropdown to from-to
                        if (!($('[name="' + name + '"]').is('select') && element.is('input[type="hidden"]'))) {
                            element.val(values);
                            element.trigger("amshopby:sync_change", [values]);
                            element.trigger("chosen:updated");
                        }
                    }.bind(this));
                }
            });
        }
        function normalizeData(data)
        {
            _(beforeValue).each(function(beforeItem){
                var item = _.filter(data, function(item){
                    return item.name === beforeItem.name;
                });

                if (item.length === 0){
                    data.push({
                        name: beforeItem.name,
                        value: ''
                    });
                }
            });

            var normalizedData = {};
            _(data).each(function(item){
                if (!normalizedData[item.name]){
                    normalizedData[item.name] = [];
                }

                normalizedData[item.name].push(item.value);
            });
            return normalizedData;
        }
    }
});
