define([
    'Magento_Ui/js/form/element/file-uploader'
], function (Element) {
    'use strict';

    return Element.extend({
        /**
         * Defines initial value of the instance.
         *
         * @returns {FileUploader} Chainable.
         */
        setInitialValue: function () {
            var value = this.getInitialValue();

            if (typeof value == 'string') {
                var array = [];
                array[0] = {
                    name: value,
                    url: this.uploaderConfig.baseUrl + value
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
