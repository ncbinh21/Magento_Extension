/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'underscore',
    'mageUtils',
    'Magento_Ui/js/grid/columns/multiselect'
], function (_, utils, Multiselect) {
    'use strict';

    return Multiselect.extend({
        defaults: {
            customParamKeys: [],
            imports: {
                params: '${ $.provider }:params'
            }
        },

        /**
         * {@inheritdoc}
         */
        getFiltering: function () {
            var result = this._super(),
                customParams = _.pick(this.params, this.customParamKeys);

            return utils.extend({}, result, customParams);
        }
    });
});
