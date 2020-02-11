/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/form/element/file-uploader',
    'mageUtils',
    'underscore'
], function (Component, utils, _) {
    'use strict';

    return Component.extend({
        /**
         * Performs data type conversions
         *
         * @param {*} value
         * @returns {Array}
         */
        normalizeData: function (value) {
            return !utils.isEmpty(value) && _.isObject(value) && !_.isArray(value) ? new Array(value) : [];
        },

        /**
         * Retrieve value for hidden input
         *
         * @returns {String}
         */
        getFilePathValue: function () {
            return !utils.isEmpty(this.value()) && this.value().length
                ? this.value().first().file
                : '';
        },

        /**
         * Checks if image placeholder is visible
         *
         * @returns {Boolean}
         */
        isVisibleImagePlaceholder: function () {
            return !(!utils.isEmpty(this.value()) && this.value().length);
        }
    });
});
