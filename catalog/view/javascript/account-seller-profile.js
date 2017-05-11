$(function() {
	$("#ms-submit-button").click(function() {
		$('.success').remove();
		var button = $(this);
		var id = $(this).attr('id');

        if (msGlobals.config_enable_rte == 1) {
			for (var instance in CKEDITOR.instances) {
				CKEDITOR.instances[instance].updateElement();
			}
        }

		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=seller/account-profile/jxsavesellerinfo',
			data: $("form#ms-sellerinfo").serialize(),
			beforeSend: function() {
				//button.hide();
				$('p.error').remove();
			},
			complete: function(jqXHR, textStatus) {
				if (textStatus != 'success') {
					button.show().prev('span.wait').remove();
					$(".warning.main").text(msGlobals.formError).show();
					//window.scrollTo(0,0);
				}
			},
			success: function(jsonData) {
				if (!jQuery.isEmptyObject(jsonData.errors)) {
                    $('#ms-submit-button').show().prev('span.wait').remove();
                    $('.error').text('');

                    for (var error in jsonData.errors) {
                        if ($('#error_' + error).length > 0) {
                            $('#error_' + error).text(jsonData.errors[error]);
                            $('#error_' + error).parents('.form-group').addClass('has-error');
                        } else if ($('[name="'+error+'"]').length > 0) {
                            $('[name="' + error + '"]').parents('.form-group').addClass('has-error');
                            $('[name="' + error + '"]').parents('div:first').append('<p class="error">' + jsonData.errors[error] + '</p>');
                        } else $(".warning.main").append("<p>" + jsonData.errors[error] + "</p>").show();
                    }

                    $("html, body").animate({ scrollTop: 0 }, "slow");
                } else if (!jQuery.isEmptyObject(jsonData.data) && jsonData.data.amount) {
					$(".ms-payment-form form input[name='custom']").val(jsonData.data.custom);
					$(".ms-payment-form form input[name='amount']").val(jsonData.data.amount);
					$(".ms-payment-form form").submit();
                    window.location.reload();
				} else if (jsonData.redirect) {
					window.location = jsonData.redirect;
				}
			}
		});
	});

	$("#sellerinfo_avatar_files, #sellerinfo_banner_files").delegate(".ms-remove", "click", function() {
		var par = $(this).parent();
		par.addClass('hidden');
		par.find('input').val('');
		par.parent().find('.dragndropmini').show();
		par.parent().find('.dragndropmini').removeClass('hidden');
	});

	
    var avatar = initUploader('ms-avatar', 'index.php?route=seller/account-profile/jxUploadSellerAvatar', 'mini', false, false);
    var banner = initUploader('ms-banner', 'index.php?route=seller/account-profile/jxUploadSellerAvatar', 'mini', false, false);

	$("select[name='seller[country]']").on('change', function() {
        $.ajax({
            url: 'index.php?route=account/account/country&country_id=' + this.value,
            dataType: 'json',
            beforeSend: function() {
               $("select[name='seller[country]']").after(' <i class="fa fa-circle-o-notch fa-spin"></i>');
            },
            complete: function() {
                $('.fa-spin').remove();
            },
            success: function(json) {
                html = '<option value="">' + msGlobals.zoneSelectError + '</option>';

                if (json['zone']) {
                    for (i = 0; i < json['zone'].length; i++) {
                        html += '<option value="' + json['zone'][i]['zone_id'] + '"';

                        if (json['zone'][i]['zone_id'] == msGlobals.zone_id) {
                            html += ' selected="selected"';
                        }

                    html += '>' + json['zone'][i]['name'] + '</option>';
                }
                } else {
                    html += '<option value="0" selected="selected">' + msGlobals.zoneNotSelectedError + '</option>';
                }

                $("select[name='seller[zone]']").html(html);
            },
            error: function(xhr, ajaxOptions, thrownError) {
                alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
            }
        });
    }).trigger('change');
	
});

$(document).ready(function(){
	// Group select
	$('.change_group_block .select_plan').click(function(){
		$('#ms_group').val($(this).data('group_id'));
		$('.change_group_block .head').removeClass('active_group');
		$(this).closest('.change_group_block').find('.head').addClass('active_group');
	});

	// language select
	$(".select-input-lang").on( "click", function() {
		var selectedLang = $(this).data('lang');
		$('.lang-select-field').each(function() {
			if ($(this).data('lang') == selectedLang) {
				$(this).show();
			} else {
				$(this).hide();
			}
		});
		$('.lang-chooser img').each(function() {
			if ($(this).data('lang') == selectedLang) {
				$(this).addClass('active');
			} else {
				$(this).removeClass('active');
			}
		});
	});
});

