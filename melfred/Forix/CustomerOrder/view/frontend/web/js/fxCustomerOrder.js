require([
    'jquery',
    'mage/url',
    "forix/plugins/fancybox"
], function ($, url) {
    var urlCurrent = window.location.href;
    var position = urlCurrent.lastIndexOf('?');
    if(position > 1) {
        var param = urlCurrent.slice(position+1);
        var paramArray = param.split("&");
        paramArray.forEach(function(element) {
            if(element.includes('limit')) {
                var number = element.replace('limit=' , '');
                if(number != ''){
                    $('#limit_page').val(parseInt(number));
                }
            }
        });
    }
    if($('#limit_page').val() == "") {
        $('#limit_page').val(10);
    }
    $('.order-container .row-item').each(function (index, el) {
        var $item = $(this).find('.order-status');
        $(this).on('click', '.order-status', function () {
            $(this).closest('.row-item').addClass("order-status-edit-box").siblings().removeClass('order-status-edit-box');
        })

        $(document).on('click', function (e) {
            var sOrder = $('.order-status-edit');
            if (!sOrder.is(e.target) && !sOrder.has(e.target).length) {
                $('.order-status-edit').closest('.row-item').removeClass("order-status-edit-box");
            }
        });
    });

    $('.order-status').click(function () {
        var status = $(this).data('orderStatus');
        $('#' + status).removeClass('no-display');
        //$(this).closest('.order-status-edit').addClass("order-status-edit-box");
    });

    $('.change-status').click(function () {
        var linkUrl = url.build('sales/orders/changestatus');
        var linkRedirect = url.build('sales/orders/manage');
        $.ajax({
            url: linkUrl,
            type: 'GET',
            data: {
                "order_status_exe": $(this).find(".order-status-exe")[0].innerText,
                "order_id_exe": $(this).find(".order-id-exe").val()
            },
            showLoader: true
        }).done(function (data) {
            location.reload();
        }).fail(function () {
            location.reload();
        });
    });

    // $(document).ajaxComplete(function (event, xhr, settings) {

        // console.log("event URL ", settings.url);
        //binding View Detail Button
        $(".row-item.row-details").hide();

        $(".delete-tracking").unbind();
        $(".delete-tracking").bind("click", function () {
            $('#fx_action').val('delete');
            this.form.submit();
            console.log(this.form);
        });

        $(".row-item .action.view").unbind();
        $(".row-item .action.view").bind("click", function () {
            var orderID = $(this).data("orderId");
            $(this).toggleClass("show");
            $(this).closest('.row-item').toggleClass("show");
            $(".orders-history .row-details[data-order-id=" + orderID + "]").toggleClass("show");
        });

        //bind tracking button
        $('.tracking-button').unbind();
        $('.tracking-button').bind("click", function () {
            var curEl = $(this);
            var data = curEl.data("trackingdata").split(',');
            $('#fx_action').val(data[0]);
            $('#fx_orderId').val(data[1]);
            $('#fx_itemId').val(data[2]);
            $(".check-qty" ).addClass('no-display');
            switch (data[0]) {
                case "edit":
                    $('.fancybox-inner-wrap').removeClass('add-tracking');
                    $('#tracking-title').text("Edit Tracking #");
                    $('.delete-tracking').hide();
                    $('#fx_trackNumber').val(data[3]);
                    $("#fx_qtyNumber").val('');
                    if (data[4]) {
                        $('#fx_trackId').val(data[4]);
                    }
                    if (data[5]) {
                        $('#fx_shipmentId').val(data[5]);
                    }
                    if ($('#fx_trackNumber').val() != '') {
                        $('#tracking-title').text("Edit Tracking #");
                        $('.delete-tracking').show();
                    }
                    break;
                default:
                    $('.fancybox-inner-wrap').removeClass('add-tracking');
                    $('.delete-tracking').hide();
                    $('#tracking-title').text("Add An Order Tracking");
                    if ($('#fx_itemId').val()) {
                        $('.fancybox-inner-wrap').addClass('add-tracking');
                        var partNo = curEl.closest('tr').find('.product-sku').html();
                        partNo = partNo.replace('Part #: ','').trim();
                        $('#tracking-title').text("Add A Tracking # For  " + partNo);
                        $(".check-qty" ).removeClass('no-display');
                    }
                    $('#fx_trackNumber').val('');
            }

            $.fancybox.open($('.trackingBox'), {
                helpers: {
                    overlay: {
                        locked: false
                    }
                }
            });


        });
    // })
});