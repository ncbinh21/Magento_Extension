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
                this.getChild(1).visible(this.additionalAddressVisible());
                if(this.additionalAddressVisible() == true) {
                    $(".add-more-line").text("- Hide Additional Address Line");
                }
                else {
                    $(".add-more-line").text("+ Add Additional Address Line");
                }

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