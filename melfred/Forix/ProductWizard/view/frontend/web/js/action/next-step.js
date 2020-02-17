/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2.2.3-EE - Melfredborzall
 * Date: 20/12/2018
 * Time: 14:52
 */

define([
    'uiComponent',
    'jquery',
    'ko',
    'mage/translate',
    'underscore',
    'Forix_ProductWizard/js/action/set-value',
], function (Component, $, ko, $t, _, setValue) {
    'use strict';
    return function (event) {
        var element = event.currentTarget;
        return setValue(element);
    }
});