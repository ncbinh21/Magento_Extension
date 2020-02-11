define([
    'Mirasvit_Report/js/grid/columns/column',
    'uiRegistry'
], function (Column, registry) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'report/grid/cells/number',

            imports: {
                totals: '${ $.provider }:data.totals'
            }
        },

        /**
         * @returns $this
         */
        initConfig: function () {
            this._super();

            return this;
        },

        /**
         * @returns {String}
         */
        getLabel: function (record) {
            return this._super(record);
        },

        getPercent: function (row) {
            var total = this.totals[0][this.index];
            var value = row[this.index];

            if (total == 0 || total == undefined || total == '') {
                return 0;
            }

            return ((value / total) * 100).toFixed(1);
        }
    });
});
