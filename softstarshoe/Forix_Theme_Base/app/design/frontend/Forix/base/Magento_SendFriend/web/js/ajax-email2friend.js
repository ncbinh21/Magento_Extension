/**
 * Created by Eric on 3/11/2016.
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    return function (config, element) {
        $(element).click(function() {

            if ($('#' + config.form).valid()) {
                var url = $('#' + config.form).attr('action');
                var jform = $('#' + config.form);
                var data = $(jform).serialize();
                var $this = this;

                $.ajax({
                    url: url,
                    type: 'post',
                    data: data,
                    dataType: 'json',
                    showLoader: true,
                    beforeSend: function() {},
                    success: function(data){
                        $('<div />').html(data.message)
                            .modal({
                                title: '',
                                autoOpen: true,
                                closed: function () {
                                    // on close
                                },
                                buttons: [{
                                    text: 'Ok',
                                    class: '',
                                    click: function() {
                                        this.closeModal();
                                        if(data.result == 1) {
                                            //close fancybox
                                            parent.jQuery.fancybox.close();
                                        }
                                    }
                                }]
                            });
                    }
                });
                return false;
            }
            return false;
        });
    }
});