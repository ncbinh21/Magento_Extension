/**
 * Created by Hidro Le.
 * Job Title: Magento Developer
 * Project Name: M2 - Soft StartShoes
 * Date: 1/27/18
 * Time: 1:42 PM
 */

define([
    "jquery",
    'Magento_Ui/js/modal/modal'
], function ($, modal) {
    "use strict";
    return function (config, element) {
        $(element).on('click', function(){
            var modalWindow = $(config.target);
            modal({
                type: 'popup',
                responsive: true,
                innerScroll: true,
                title: config.title,
                modalClass: 'category-cms-popup-modal ',
                buttons: [{
                    text: 'Close'
                }]
            }, modalWindow);
            modalWindow.modal('openModal'); 
        });
    };
});