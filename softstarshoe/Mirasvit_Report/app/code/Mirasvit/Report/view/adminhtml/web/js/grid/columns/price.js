define([
    'Magento_Catalog/js/price-utils',
    'Mirasvit_Report/js/grid/columns/number',
    'uiRegistry'
], function (priceUtils, Number, registry) {
    'use strict';

    return Number.extend({
        defaults: {
            bodyTmpl: 'report/grid/cells/number'
        },

        /**
         * @returns $this
         */
        initConfig: function () {
            this._super();

            this.priceFormat = registry.get('mreport.mreport').priceFormat;


            return this;
        },

        /**
         * @returns {String} Formatted price.
         */
        getLabel: function () {
            var number = this._super();

            return priceUtils.formatPrice(number, this.priceFormat);
        },

        /**
         * @returns {String} Formatted price.
         */
        getComparisonLabel: function () {
            var number = this._super();
            if (number !== null) {
                return priceUtils.formatPrice(number, this.priceFormat);
            }
        }
    });
});
