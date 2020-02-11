/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

var config = {
    map: {
        '*': {
            gridstack: 'Yosto_InstagramConnect/js/gridstack',
            gridstackJQueryUI: 'Yosto_InstagramConnect/js/gridstackJQueryUI',
            lodash: 'Yosto_InstagramConnect/js/lodash',
        }
    },
    shim: {
        "Yosto_InstagramConnect/js/gridstackJQueryUI":["jquery","jquery/ui"]
    }

};