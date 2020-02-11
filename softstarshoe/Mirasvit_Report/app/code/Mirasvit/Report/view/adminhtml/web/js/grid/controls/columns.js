define([
    'underscore',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/grid/controls/columns'
], function (_, utils, $t, Columns) {
    'use strict';

    return Columns.extend({
        defaults: {
            template: 'report/grid/controls/columns',
            exports:  {
                columns: '${ $.provider }:params.columns'
            }
        },

        isDisabled: function (elem) {
            var disabled = this._super();

            return disabled || elem.isDisabled;
        },

        countVisible: function () {
            var columns = [];
            _.each(this.elems.filter('visible'), function (item) {
                columns.push(item.index);
            });

            if (this.get('columns') == undefined || columns.length > this.get('columns').length) {
                // set and reload
                this.set('columns', columns);
            }

            return this.elems.filter('visible').length;
        },

        getElementsByType: function () {
            var avg = [];
            var sum = [];
            var none = [];
            var etc = [];
            var filter = [];
            _.each(this.elems(), function (item) {
                if (item.isFilterOnly == false) {
                    switch (item.aggregationType) {
                    case 'avg':
                        avg.push(item);
                        break;
                    case 'sum':
                        sum.push(item);
                        break;
                    case 'none':
                        none.push(item);
                        break;
                    default:
                        etc.push(item);
                    }
                } else {
                    filter.push(item);
                }
            });

            var result = [];

            if (sum.length) {
                result.push(sum);
            }
            if (none.length) {
                result.push(none);
            }
            if (avg.length) {
                result.push(avg);
            }
            if (etc.length) {
                result.push(etc);
            }
            if (filter.length) {
                result.push(filter);
            }

            return result;
        }
    });
});
