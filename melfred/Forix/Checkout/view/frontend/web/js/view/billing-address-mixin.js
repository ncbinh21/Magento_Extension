define(function () {
    'use strict';

    var mixin = {
        updateAddress: function () {
            if(this.source.get(this.dataScopePrefix + '.custom_attributes')['fullname']) {
                var fullName = this.source.get(this.dataScopePrefix + '.custom_attributes')['fullname'].trim().split(' ');
                if (fullName[0] !== undefined){
                    this.source.get(this.dataScopePrefix)['firstname'] = fullName[0];
                }
                if (fullName[1] !== undefined) {
                    this.source.get(this.dataScopePrefix)['lastname'] = this.source.get(this.dataScopePrefix + '.custom_attributes')['fullname'].replace(fullName[0], '').trim();
                }

                this.source.get(this.dataScopePrefix + '.custom_attributes')['fullname'] = '';
            }
            this._super();
        }
    };
    return function (target) {
        return target.extend(mixin);
    };
});
