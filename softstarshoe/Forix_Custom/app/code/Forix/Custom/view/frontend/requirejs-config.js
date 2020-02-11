 /**
  * Copyright © Magento, Inc. All rights reserved.
  * See COPYING.txt for license details.
  */

 var config = {
     config: {
         mixins: {
             "Magento_Swatches/js/swatch-renderer": {
                 'Forix_Custom/js/swatch/swatch-renderer-mixins': true
             }
         }
     },
     map: {
         '*': {
             "forix/homepage": "Forix_Custom/js/forix-homepage"
         }
     },
     paths: {
         owl: "Forix_Custom/js/slider/owl.carousel",
         banner: "Forix_Custom/js/slider/banner-slider",
         mousewheel: "Forix_Custom/js/mousewheel/jquery.mousewheel.min"
     },
     shim: {
         owl: {
             deps: ['jquery']
         },
         banner: {
             deps: ['jquery']
         },
         mousewheel: {
             deps: ['jquery']
         }
     }
 };
