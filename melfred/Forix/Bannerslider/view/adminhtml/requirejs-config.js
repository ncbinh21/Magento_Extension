var config = {
    map: {
        '*': {
            'forix/note': 'Forix_Bannerslider/js/jquery/slider/jquery-ads-note',
        },
    },
    paths: {
        'forix/flexslider': 'Forix_Bannerslider/js/jquery/slider/jquery-flexslider-min',
        'forix/evolutionslider': 'Forix_Bannerslider/js/jquery/slider/jquery-slider-min',
        'forix/zebra-tooltips': 'Forix_Bannerslider/js/jquery/ui/zebra-tooltips',
    },
    shim: {
        'forix/flexslider': {
            deps: ['jquery']
        },
        'forix/evolutionslider': {
            deps: ['jquery']
        },
        'forix/zebra-tooltips': {
            deps: ['jquery']
        },
    }
};
