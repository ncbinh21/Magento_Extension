define([
    'ko',
    'jquery',
    'jquery/ui'
], function (ko, $) {
    return function(Group){
        return Group.extend({
            defaults: {
                additionalAddressVisible: false
            },
            initialize: function () {
                this._super();
                return this;
            },
            toggleVisible: function(){
                this.switchStatus();
                $('.add-more-line').toggleClass('active');
                this.getChild(1).visible(this.additionalAddressVisible());
            },
            initObservable: function () {
                this._super()
                    .observe('additionalAddressVisible');
                return this;
            },
            switchStatus: function(){
                this.additionalAddressVisible(!this.additionalAddressVisible());
                return this;
            },
        });
    }
});