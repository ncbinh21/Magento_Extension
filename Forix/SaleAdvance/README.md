### Function 
1. Prevent list region at checkout page
 
 ## Prevent at payment page: 
    - view/payment/default.js
 ## Prevent at shipping page
    - view/shipping.js
    - action/select-shipping-address.js
    - model/address-converter.js.
* Fix bug core magento 2:
    -   addressData.region = {
            region_id: addressData.regionId,
            region_code: addressData.regionCode,
            region: regionName
        };


