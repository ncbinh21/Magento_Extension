var config = {
    map: {
        '*': {
            'libs/datatables': "Forix_CustomerProduct/js/libs/datatables.min"
        }
    },
    paths: {
        'libs/datatables': 'Forix_CustomerProduct/js/libs/datatables.min',
    },
    shim: {
        'libs/datatables': {
            'deps': ['jquery']
        }
    }
};