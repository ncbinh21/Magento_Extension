/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/switcher'
], function ($, registry, Component) {
    'use strict';

    return Component.extend({
        /**
         * {@inheritdoc}
         */
        applyAction: function (action) {
            var target,
                callback;

            if (action.target) {
                target = registry.get(action.target);
            }
            if (action.selector) {
                target = $(action.selector);
            }

            if (target) {
                callback = target[action.callback];
                callback.apply(target, action.params || []);
            }
        }
    });
});
