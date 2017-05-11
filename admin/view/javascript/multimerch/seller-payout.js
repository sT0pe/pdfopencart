$(function() {
	$(document).on('click', '#ms-pay, .ms-pay-single', function(e) {
		e.preventDefault();
		var button = $(this);
        var selected_sellers_info = [];

        if(button.attr('id') == 'ms-pay') {
            $.map($('#list-sellers').children('tbody').find('input:checkbox:checked'), function(item) {
                selected_sellers_info.push({
                    id: $(item).val(),
                    amount: $(item).data('available')
                });
            });
        } else {
            var button_classes = button.attr('class').split(' ');
            if($.inArray('ms-pay-single', button_classes) !== -1) {
                selected_sellers_info.push({
                    id: button.closest('tr').find('input:checkbox').val(),
                    amount: button.closest('tr').find('input:checkbox').data('available')
                });
            }
        }

        if(selected_sellers_info.length > 0) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: 'index.php?route=multimerch/payment-request/jxCreate&token=' + msGlobals.token,
                data: {sellers: selected_sellers_info},
                success: function (jsonData) {
					if(jsonData.success) {
						var urlGetParams = '&request_ids=' + jsonData.requests.join(',');
						window.location.href = $('base').attr('href') + "index.php?route=multimerch/payment/create&token=" + msGlobals.token + urlGetParams;
					} else if (jsonData.errors) {
                        show_errors(jsonData.errors)
                    }
                }
            });
        } else {
            show_errors([msGlobals.ms_error_seller_notselected]);
        }
	});

    function show_errors(errors) {
        if($(document).find('.error-holder').length) {
            $(document).find('.error-holder').remove();
        }

        var html = '';
        html += '<div class="alert alert-danger error-holder">';
        html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        html += '<ul style="list-style: none;">';
        $.map(errors, function(error){
            html += '<li><i class="fa fa-exclamation-circle"></i> ' + error + '</li>';
        });
        html += '</ul>';
        html += '</div>';

        $('#content .page-body').prepend(html);
    }

    $(document).on('click', '#list-sellers input:checkbox', function() {
        if($(this).attr('id') == 'check-all') {
            $('input[name*=\'selected\']').prop('checked', $(this).prop('checked'));
        }

        var selected_sellers = $('#list-sellers').children('tbody').find('input:checkbox:checked');
        if(selected_sellers.length > 1) {
            $('#ms-pay').show();
        } else {
            $('#ms-pay').hide();
        }
    });
});
