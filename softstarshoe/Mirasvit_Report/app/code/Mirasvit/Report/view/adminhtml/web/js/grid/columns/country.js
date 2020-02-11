define([
    'Magento_Ui/js/grid/columns/select'
], function (Select) {
    'use strict';

    return Select.extend({
        defaults: {
            bodyTmpl: 'report/grid/cells/country'
        },

        initConfig: function () {
            this._super();
            return this;
        },

        getLabel: function (record) {
            var value = record[this.index] + "";
            var text = this._super();
            if (value !== "") {
                return '<img src="http://www.geonames.org/flags/x/' + value.toLowerCase() + '.gif">' + ' ' + text;
            } else {
                return text;
            }
        }
    });
});
