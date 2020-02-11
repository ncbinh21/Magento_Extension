define([
    'Magento_Ui/js/grid/filters/filters'
], function (Filters) {
    'use strict';

    return Filters.extend({
        defaults: {
            templates: {
                filters: {
                    select:    {
                        component: 'Mirasvit_Report/js/grid/filters/multiselect',
                        template:  'ui/grid/filters/elements/ui-select',
                        options:   '${ JSON.stringify($.$data.column.options) }',
                        caption:   ' '
                    }
                }
            }
        }
    });
});
