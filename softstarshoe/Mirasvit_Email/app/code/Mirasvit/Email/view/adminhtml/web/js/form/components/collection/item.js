define([
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/components/collection/item'
], function(_, utils, Item) {
    'use strict';

    var previewConfig = {
        separator: ' ',
        prefix: '',
        suffix: '',
        fallback: '',
        depends: '',
        isAcceptRawValue: false
    };

    return Item.extend({
        defaults: {
            label: '',
            uniqueNs: 'activeCollectionItem',
            previewTpl: 'Mirasvit_Email/ui/form/components/collection/preview'
        },

        /**
         * Extends instance with default config, calls initializes of parent class
         */
        initialize: function () {
            _.bindAll(this, 'delay');

            return this._super();
        },

        /**
         * Creates string view of previews
         *
         * @param  {Object} data
         * @return {String} - formatted preview string
         */
        buildPreview: function (data) {
            var preview  = this.getPreview(data.items),
                prefix   = data.prefix,
                suffix   = data.suffix,
                depends  = data.depends,
                callback = this[data.callback],
                value    = preview.join(data.separator),
                fallback = data.fallback;

            if (depends) {
                var dependsValue = this.getPreview([depends]).join();
                if (!dependsValue) {
                    value = '';
                }
            }

            if (callback) {
                if (data.isAcceptRawValue) {
                    value = callback(preview);
                } else {
                    value = callback(value);
                }
            }

            if (fallback && !value) {
                return fallback;
            }

            if (!value) {
                return '';
            }

            return prefix + value + suffix;
        },

        /**
         * Adds element to observable indexed object of instance
         *
         * @param  {Object} elem
         * @return {Object} - reference to instance
         */
        insertToIndexed: function (elem) {
            var indexed = this.indexed(),
                self    = this;

            if (elem.componentType == 'fieldset' || elem.componentType == 'container') {
                elem._elems.forEach(function(item) {
                    self.insertToIndexed(item);
                });
            } else {
                indexed[elem.index] = elem;
            }

            this.indexed(indexed);

            return this;
        },

        /**
         * Format value to human readable format "1d 5h 32m" => "1 day 5 hours 32 minutes"
         *
         * @param {string} value
         * @return {string} value
         */
        delay: function (value) {
            var amount = 0,
                period = '',
                prefix = '',
                result = [];

            for (var i = 0; i < value.length; i++) {
                amount = parseInt(value[i]);
                if (!amount) {
                    continue;
                }

                prefix = amount > 1 ? 's' : '';

                if (value[i].indexOf('hour') != -1) {
                    period = 'hour';
                } else if (value[i].indexOf('minute') != -1) {
                    period = 'minute';
                } else {
                    period = 'day';
                }

                result.push('<b>' + amount + '</b> ' + period + prefix);
            }

            return result.join(' ');
        }
    });
});