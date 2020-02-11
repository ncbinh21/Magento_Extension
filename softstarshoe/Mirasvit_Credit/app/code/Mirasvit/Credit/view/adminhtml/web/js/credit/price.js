define(
    [
        'jquery',
        'ko',
        'uiComponent'
    ],
    function(
        $,
        ko,
        Component
    ) {
        var options = [
            {value: 'fixed', 'label': 'Fixed'},
            {value: 'range', 'label': 'Range'}
        ];
        var priceTypes = options;


        return Component.extend({
            priceTypes: priceTypes
        });
    }
);