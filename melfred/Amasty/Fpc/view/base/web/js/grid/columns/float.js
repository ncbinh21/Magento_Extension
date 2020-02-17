define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            precision: 3
        },

        getLabel: function (record) {
            return (+record[this.index]).toFixed(this.precision);
        }
    });
});
