define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (__, uiRegistry, select) {
    'use strict';
    return select.extend({

        initialize: function () {
            this._super();

            this.setVisibility();

            return this;
        },

        onUpdate: function (value) {
            var product_template_onoptions_first = uiRegistry.get('index = description_template');
            if (product_template_onoptions_first
                && product_template_onoptions_first.visibleValue == value) {
                product_template_onoptions_first.show();
            } else if (product_template_onoptions_first) {
                product_template_onoptions_first.hide();
            }

            return this._super();
        },

        setVisibility: function () {
            var value = this.value();
            if (!value) {
                value = 1;
            }

            this.onUpdate(value);
        },
    });
});