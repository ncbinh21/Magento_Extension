/**
 * Created by YOGA on 8/17/2017.
 */
var config = {
    config: {
        mixins: {
            'Magento_Ui/js/form/components/group': {
                'Forix_CustomShipping/js/address/street/moreline':true
            },
            'Magento_Checkout/js/view/shipping' : {
                'Forix_CustomShipping/js/checkout/view/shipping-address' : true
            },
        }
    }
};