/**
 * Created by nghiaho on 10/10/2016.
 */
require([
    'Magento_Customer/js/customer-data'
], function (customerData) {
    var sections = ['cart'];
    customerData.invalidate(sections);
    customerData.reload(sections, true);
});