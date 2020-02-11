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
                    var nowValue = currentForm.serializeArray();

                    var data = normalizeData(currentForm.serializeArray());
                    _(data).each(function(values, name){
                        var element = $(this).find('[name="' + name + '"]');

                        if (values[0]) {
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