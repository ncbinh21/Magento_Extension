/**
* Copyright 2016 aheadWorks. All rights reserved.
* See LICENSE.txt for license details.
*/

define([
    'Magento_Ui/js/dynamic-rows/dynamic-rows'
], function (Component) {
    'use strict';

    return Component.extend({
        /**
         * Show element
         *
         * @returns {Object} Chainable
         */
        show: function () {
            this.visible(true);

            return this;
        },

        /**
         * Hide element
         *
         * @returns {Object} Chainable
         */
        hide: function () {
            this.visible(false);

            return this;
        },

        /**
         * Delete record
         *
         * @param {Number} index - row index
         */
        deleteRecord: function (index, recordId) {
            if (typeof this.reinitRecordData != 'undefined') {
                var recordInstance,
                    childs;

                recordInstance = _.find(this.elems(), function (elem) {
                    return elem.index === index;
                });
                recordInstance.destroy();
                this.elems([]);
                this._updateCollection();
                this.removeMaxPosition();
                this.recordData()[recordInstance.index][this.deleteProperty] = this.deleteValue;
                this.recordData.valueHasMutated();
                childs = this.getChildItems();

                if (childs.length > this.elems().length) {
                    this.addChild(false, childs[childs.length - 1][this.identificationProperty], false);
                }

                this._reducePages();
                this._sort();
            } else {
                this._super(index, recordId);
            }
        },
    });
});
