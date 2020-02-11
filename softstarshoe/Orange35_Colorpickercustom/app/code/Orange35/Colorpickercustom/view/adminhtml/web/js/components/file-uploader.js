/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'underscore',
    'mageUtils',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/lib/validation/validator',
    'Magento_Ui/js/form/element/file-uploader',
    'jquery/file-uploader'
], function ($, _, utils, uiAlert, validator, Element) {
    'use strict';

    return Element.extend({

        /**
         * Defines initial value of the instance.
         *
         * @returns {FileUploader} Chainable.
         */
        setInitialValue: function () {
            var value = this.getInitialValue();
            var array = [];

            if (typeof value == 'string') {
                array[0] = {
                    name: value,
                    url: this.uploaderConfig.imageUrl + value
                };
                value = array;
            }

            value = value.map(this.processFile, this);

            this.initialValue = value.slice();

            this.value(value);
            this.on('value', this.onUpdate.bind(this));

            return this;
        }
    });
});
