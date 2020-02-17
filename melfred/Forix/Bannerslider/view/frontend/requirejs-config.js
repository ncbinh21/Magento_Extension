var config = {
	map: {
		'*': {
			'forix/note': 'Forix_Bannerslider/js/jquery/slider/jquery-ads-note',
			'forix/impress': 'Forix_Bannerslider/js/report/impress',
			'forix/clickbanner': 'Forix_Bannerslider/js/report/clickbanner',
		},
	},
	paths: {
		'forix/flexslider': 'Forix_Bannerslider/js/jquery/slider/jquery-flexslider-min',
		'forix/evolutionslider': 'Forix_Bannerslider/js/jquery/slider/jquery-slider-min',
		'forix/popup': 'Forix_Bannerslider/js/jquery.bpopup.min',
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
