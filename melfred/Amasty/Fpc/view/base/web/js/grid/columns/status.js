define([
    'Magento_Ui/js/grid/columns/select'
], function (Column) {
    'use strict';

    return Column.extend({
        getLabel: function (record) {
            var result = this._super();

            // Display status code if code is unknown
            // Without this customization you will see nothing
            if (!result) {
                result = record[this.index];
            }

            return result;
        }
    });
});
