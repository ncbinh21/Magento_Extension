/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 22/06/2018
 * Time: 04:26
 */
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'jquery',
    'Magento_Checkout/js/model/url-builder'
], function ($, urlBuilder) {
    'use strict';

    return $.extend(urlBuilder, {
        storeCode: window.productWizard.storeCode
    });
});
