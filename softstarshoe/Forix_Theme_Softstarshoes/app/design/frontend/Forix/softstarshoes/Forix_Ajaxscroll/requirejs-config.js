var config = {
    paths: {
        'ias':                  'Forix_Ajaxscroll/js/ias',
        'ias-core':             'Forix_Ajaxscroll/js/ias/jquery-ias',
        'ias-callback':         'Forix_Ajaxscroll/js/ias/callbacks',
        'ias-history':          'Forix_Ajaxscroll/js/ias/extension/history',
        'ias-noneleft':         'Forix_Ajaxscroll/js/ias/extension/noneleft',
        'ias-paging':           'Forix_Ajaxscroll/js/ias/extension/paging',
        'ias-paging-next':      'Forix_Ajaxscroll/js/ias/extension/paging-next',
        'ias-spinner':          'Forix_Ajaxscroll/js/ias/extension/spinner',
        'ias-trigger':          'Forix_Ajaxscroll/js/ias/extension/trigger',
        'ias-trigger-next':     'Forix_Ajaxscroll/js/ias/extension/trigger-next',
        'ias-trigger-prev':     'Forix_Ajaxscroll/js/ias/extension/trigger-prev',
        'ias-cache':            'Forix_Ajaxscroll/js/ias/extension/cache',
        'ias-forix-blog':       'Forix_Ajaxscroll/js/ias_blog',
        'ias-forix-photo':      'Forix_Ajaxscroll/js/ias_photo'
    },
    shim: {
        "ias-core": {deps: ["jquery", "ias-callback"]},
        "ias-history": {deps: ["ias-core"], exports: 'IASHistoryExtension'},
        "ias-noneleft": {deps: ["ias-core"], exports: 'IASNoneLeftExtension'},
        "ias-paging": {deps: ["ias-core"], exports: 'IASPagingExtension'},
        "ias-paging-next": {deps: ["ias-core"], exports: 'IASPagingNextExtension'},
        "ias-spinner": {deps: ["ias-core"], exports: 'IASSpinnerExtension'},
        "ias-trigger": {deps: ["ias-core"], exports: 'IASTriggerExtension'},
        "ias-trigger-next": {deps: ["ias-core"], exports: 'IASTriggerNextExtension'},
        "ias-trigger-prev": {deps: ["ias-core"], exports: 'IASTriggerExtension'},
        "ias-cache": {deps: ["ias-core"], exports: 'IASCacheExtension'}
    }
};

