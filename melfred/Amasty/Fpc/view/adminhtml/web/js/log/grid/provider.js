define([
    'Magento_Ui/js/grid/provider'
], function (Element) {
    'use strict';

    return Element.extend({
        reload: function (options) {
            var matches = window.location.hash.match(/#(\d+)/);
            if (matches) {
                this.params.filters.status = matches[1];
                window.location.hash = '';
            }

            this._super();
        }
    });
});
