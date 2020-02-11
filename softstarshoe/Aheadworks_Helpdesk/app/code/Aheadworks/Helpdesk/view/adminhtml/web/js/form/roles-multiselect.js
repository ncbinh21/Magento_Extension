/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/multiselect',
    'jquery'
], function (_, utils, Select, $) {
    'use strict';

    return Select.extend({

        /**
         * @inheritdoc
         */
        onUpdate: function () {
            var value = this.value(),
                allRolesValue = '0';

            if ($.inArray(allRolesValue, value) != -1) {
                this.value(allRolesValue);
                this.bubble('update', false);
            } else {
                this.bubble('update', this.hasChanged());
            }
            this.validate();
        }
    });
});
