define([
    'Magento_Ui/js/grid/columns/column',
    'uiRegistry'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'report/grid/cells/column'
        },

        getComparisonLabel: function (record) {
            if (record['c_' + this.index] !== undefined) {
                return record['c_' + this.index];
            }

            return null;
        },

        getDiffLabel: function (record) {
            if (record['c_' + this.index] !== undefined && record[this.index] !== undefined) {
                var a = Math.abs(record[this.index]);
                var b = Math.abs(record['c_' + this.index]);

                if (a == b) {
                    return null;
                }

                if (a == 0) {
                    return '∞';
                }

                return Math.round((a - b) / a * 100) + "%";
            }

            return null;
        },

        getDiffSign: function (record) {
            if (record['c_' + this.index] !== undefined && record[this.index] !== undefined) {
                var a = Math.abs(record[this.index]);
                var b = Math.abs(record['c_' + this.index]);

                if (a > b) {
                    return "positive";
                } else {
                    return "negative";
                }
            }
        }
    });
});
