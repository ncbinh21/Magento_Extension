/**
 * Copyright © 2016 x-mage2(Yosto). All rights reserved.
 * See README.md for details.
 */

var config = {
    map: {
        '*': {
            lightbox: 'Yosto_InstagramConnect/js/lightbox',
            lightboxshoppingpage: 'Yosto_InstagramConnect/js/lightboxshoppingpage',
            gridstack: 'Yosto_InstagramConnect/js/gridstack',
            gridstackJQueryUI: 'Yosto_InstagramConnect/js/gridstackJQueryUI',
            lodash: 'Yosto_InstagramConnect/js/lodash'
        }
    },
    shim: {
        "Yosto_InstagramConnect/js/lightbox":["jquery"],
        "Yosto_InstagramConnect/js/lightboxshoppingpage":["jquery"],
        "Yosto_InstagramConnect/js/gridstackJQueryUI":["jquery","jquery/ui"]
    }

};