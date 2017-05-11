$(function() {
	function getPaymentInfo() {
		var pg_code = $('#payment-method').val().replace('ms_pg_', '');

		var request_ids = [];
		$.map($('input[name="request_ids[]"]'), function(item) {
			request_ids.push(parseInt($(item).val()));
		});

		if(request_ids.length > 0) {
			$.ajax({
				url: 'index.php?route=multimerch/payment/' + pg_code + '/jxGetPaymentForm&token=' + msGlobals.token,
				type: 'post',
				data: {pg_code: pg_code, request_ids: request_ids},
				dataType: 'json',
				beforeSend: function() {
					$('.methods-info').fadeOut(10);
				},
				complete: function() {
                    $('head').append('<link type="text/css" href="view/stylesheet/multimerch/payment/' + pg_code + '.css" rel="stylesheet" media="screen" />');
				},
				success: function (json) {
					$('.methods-info').html(json).fadeIn();
				},
				error: function (xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		}
	}

	$(document).on('ready', getPaymentInfo);
	$(document).on('change', '#payment-method', getPaymentInfo);
});
