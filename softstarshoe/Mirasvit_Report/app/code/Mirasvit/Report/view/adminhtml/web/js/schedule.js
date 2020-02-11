require([
    'jquery',
    'Mirasvit_Report/js/schedule/prettycron',
    'Mirasvit_Report/js/schedule/later'
], function ($, cron) {
    'use strict';

    var $input = $('.schedule');
    var $container = $('<div />');

    $container.insertAfter($input);

    $input.on('change keyup', function (e) {
        render($(e.target), $container);
    });

    $input.on('change', function (e) {
        render($(e.target), $container);
        validate($(e.target));
    });

    render($input, $container);

    function render($input, $container) {
        $container.html('');

        var val = $input.val();

        var readable = cron.toString(val);

        var $p = $('<p />')
            .css('font-size', '13px')
            .css('background', '#eb5202')
            .css('padding', '5px')
            .css('color', '#fff')
            .html(readable);
        $container.append($p);

        later.schedule(later.parse.cron(val)).next(3).forEach(function(next) {
            var $p = $('<p />').css('font-size', '11px').html(next.toGMTString());
            $container.append($p);
        })
    }

    function validate($input)  {
        var val = $input.val();

        var parts = val.split(' ');

        parts = parts.filter(function(item) {
            if (item.trim()) {
                return item;
            }
        });

        if (parts.length != 5) {
            if (parts.length < 5) {
                while (parts.length != 5) {
                    parts.push("*")
                }
            } else if(parts.length > 5) {
                while (parts.length != 5) {
                    parts.pop()
                }
            }
        }

        var newVal = parts.join(" ");

        if (val != newVal) {
            $input.val(newVal);
        }
    }
});