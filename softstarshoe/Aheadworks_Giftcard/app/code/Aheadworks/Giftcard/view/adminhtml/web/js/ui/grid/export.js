/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'underscore',
    'mageUtils',
    'Magento_Ui/js/grid/export'
], function (_, utils, Export) {
    'use strict';

    return Export.extend({
        defaults: {
            customParamKeys: [],
            imports: {
                params: '${ $.provider }:params'
            }
        },

        /**
         * {@inheritdoc}
         */
        getParams: function () {
            var result = this._super(),
                customParams = _.pick(this.params, this.customParamKeys);

            customParams = _.mapObject(customParams, function(val, key) {
                return _.isEmpty(val) ? 'new' : val;
            });

            return utils.extend({}, result, customParams);
        }
    });
});
