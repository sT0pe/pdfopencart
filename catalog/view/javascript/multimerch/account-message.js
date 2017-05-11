$(function() {
	$('.error_text').hide();
	$("#ms-message-reply").click(function() {
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=account/msmessage/jxSendMessage',
			data: $(this).closest("form").serialize(),
			beforeSend: function() {
				$('#ms-message-form a.button').hide().before('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			success: function(jsonData) {
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-message-form a.button').show().prev('span.wait').remove();
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						$('.error_text').show();
						$('#error_text').text(jsonData.errors[error]);
						window.scrollTo(0,0);
					}
				} else {
					location = jsonData['redirect'];
				}
			}
		});
	});
	
	$("#ms-message-text").focus(function() {
		$(this).val('').unbind('focus');
	});

	$("body").on('click', ".ms-order-message", function() {
		var button = $(this);
		var suborder_id = button.data('suborder_id');
		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=account/msmessage/jxSendOrderMessage',
			data: $(this).parents("form").serialize(),
			beforeSend: function() {
				//$('#ms-message-form'+ suborder_id +' .btn').button('loading');
			},
			success: function(jsonData) {
				$('#ms-message-form'+ suborder_id +' .btn').button('reset');
				$('.error').text('');
				if (!jQuery.isEmptyObject(jsonData.errors)) {
					$('#ms-message-form a.button').show().prev('span.wait').remove();
					for (error in jsonData.errors) {
						if (!jsonData.errors.hasOwnProperty(error)) {
							continue;
						}
						$('#message_alert'+suborder_id).find('span').text(jsonData.errors[error]);
						$('#message_alert'+suborder_id).show();
					}
				} else {
					var route = button.closest('.ms-account-order-info').length > 0 ? 'sellerOrderConversation' : 'customerOrderConversation';
					$('#seller_history').load('index.php?route=multimerch/account_order/'+route+'&tab='+suborder_id+'&order_id='+jsonData['order_id'],function(){
						//$('#message_success'+suborder_id).show();
					});
				}
			}
		});
	});

    $(document).on('click', '.ms-message-upload', function() {
        var $this = $(this);

        $('#form-upload').remove();

        $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file" /></form>');

        $('#form-upload input[name="file"]').trigger('click');

        if (typeof timer != 'undefined') {
            clearInterval(timer);
        }

        timer = setInterval(function() {
            if ($('#form-upload input[name="file"]').val() != '') {
                clearInterval(timer);

                $.ajax({
                    url: 'index.php?route=multimerch/account_order/jxUploadAttachment',
                    type: 'post',
                    dataType: 'json',
                    data: new FormData($('#form-upload')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $this.button('loading');
                    },
                    complete: function() {
                        $this.button('reset');
                    },
                    success: function(json) {
                        $('.text-danger').remove();

                        if (json['error']) {
                            alert(json['error']);
                        }

                        if (json['success']) {
                            alert(json['success']);

                            var html = '<li>';
                            html += '<input type="hidden" name="attachments[]" value="' + json['code'] + '" />';
                            html += json['filename'];
                            html += '<span class="ms-remove"><i class="fa fa-times"></i></span>';
                            html += '</li>';
                            $('ul.attachments').append(html);
                        }
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        }, 500);
    });

    $(document).on('click', '.ms-remove', function() {
        $(this).closest('li').remove();
    });

});
