/**
 * @author    Amasty Team
 * @copyright Copyright (c) Amasty Ltd. ( http://www.amasty.com/ )
 * @package   Amasty_Shopby
 */

var config = {
    map: {
        '*': {
            stockMessages: 'Forix_Product/js/stock-messages'
        }
    },
    shim: {
        stockMessages: {
            'deps': ['Magento_ConfigurableProduct/js/configurable', 'Magento_Swatches/js/swatch-renderer']
        },
    }
};
