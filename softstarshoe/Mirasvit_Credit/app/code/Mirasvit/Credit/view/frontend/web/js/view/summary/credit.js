define([
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote'
], function (Component, quote) {
    "use strict";
    return Component.extend({
        defaults: {
            template: 'Mirasvit_Credit/summary/credit'
        },
        totals: quote.getTotals(),

        isDisplayed: function () {
            return !!this.getValue();
        },
        getValue: function () {
            for (var i in this.totals().total_segments) {
                var total = this.totals().total_segments[i];

                if (total.code == 'credit') {
                    return this.getFormattedPrice(total.value);
                }
            }

            return 0;
        }
    });
});