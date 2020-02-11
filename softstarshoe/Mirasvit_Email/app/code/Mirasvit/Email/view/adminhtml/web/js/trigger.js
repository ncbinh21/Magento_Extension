define([
    'jquery'
], function ($) {
    'use strict';

    var trigger = {
        sendTestEmail: function (button, send, email, url) {
            if (send) {
                $.ajax({
                    showLoader: true,
                    url: url,
                    data: {email: $('#test_email').val()},
                    type: 'GET',
                    dataType: 'json'
                });

            } else {
                $(button).hide();
                $(button).parent().html($(button).parent().html() +
                    '<input type="text" id="test_email" value="' + email + '" class="input-text admin__control-text" style="margin-left: 5px; width:200px">'
                    + '<button type="button" class="scalable" onclick="trigger.sendTestEmail(this, true, null, \'' + url + '\')"><span>Send</span></button>');
            }
        }
    };

    window.trigger = trigger;
});