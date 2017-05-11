$(function() {


	// Options list

	$('#list-options').dataTable( {
		"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-option/getTableData",
		"aoColumns": [
			{ "mData": "name"},
			{ "mData": "values", "bSortable": false },
			{ "mData": "status" },
			{ "mData": "sort_order" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
		]
	});

	$(document).on('click', '.icon-remove.ms_remove_option', function(e) {
		e.preventDefault();
		var url = $(this).attr('href');

        if(confirm('Are you sure?')) {
            $.ajax({
                type: "get",
                dataType: "json",
                url: url,
                beforeSend: function () {
                    if ($('.alert-success').length > 0)
                        $('.alert-success').text('').hide();
                },
                success: function (json) {
                    if (json.error) {
                        $('#error-holder').text('').append(json.error + '<BR>').show();
                    } else {
                        window.location.reload();
                    }
                }
            })
        }
	});


	/************************************************************/


	// Options form

    var lang_inputs = $('.lang-select-field');
    if(lang_inputs.length) {
        var current_language = msGlobals.config_language;
        for (var i = 0; i < lang_inputs.length; i++) {
            if ($(lang_inputs[i]).data('lang') != current_language) {
                $(lang_inputs[i]).hide();
            } else {
                $(lang_inputs[i]).show();
            }
        }

        $(".select-input-lang").on("click", function () {
            var selectedLang = $(this).data('lang');
            $('.lang-select-field').each(function () {
                if ($(this).data('lang') == selectedLang) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    }

    $('select[name=\'type\']').on('change', function() {
        if (this.value == 'select' || this.value == 'radio' || this.value == 'checkbox' || this.value == 'image') {
            $('#option-values').show();
        } else {
            $('#option-values').hide();
        }
    });

    $('select[name=\'type\']').trigger('change');

    $(document).on('click', '.ms_remove_option_value', function() {
        $(this).closest('tr').remove();
    });

    $(document).on('click', '.ms_add_option_value', function() {
        var lastRow = $(this).closest('#option-values').find('tbody tr.option_value:last input:last').attr('name');
        if (typeof lastRow == "undefined") {
            var newRowNum = 1;
        } else {
            var newRowNum = parseInt(lastRow.match(/[0-9]+/g).pop()) + 1;
        }
        var newVal = $(this).closest('#option-values').find('tbody tr.ffSample').clone();

        newVal.find('input,select,textarea').attr('name', function(i,name) {
            if(name !== undefined) return name.replace('option_value[0]','option_value[' + newRowNum + ']');
        });

        $(this).closest('#option-values').find('tbody').append(newVal.removeClass('ffSample'));

        $(this).val(0);
    });

	$("#ms-submit-button").click(function() {
		var button = $(this);

		$.ajax({
			type: "POST",
			dataType: "json",
			url: $('base').attr('href') + 'index.php?route=seller/account-option/jxSaveOption',
			data: $("form#ms-new-option").serialize(),
			beforeSend: function() {
				$('.error').html('');
				$('#error-holder').hide();
			},
			complete: function(jqXHR, textStatus) {
				if (textStatus != 'success') {
					$('#error-holder').empty().text(msGlobals.formError).show();
					window.scrollTo(0,0);
				}
			},
			error: function() {
				$('#error-holder').empty().text(msGlobals.formError).show();
				window.scrollTo(0,0);
			},
			success: function(json) {
				if(json.errors) {
					button.button('reset');
					for (error in json.errors) {
						if ($('[name^="' + error + '"]').length > 0) {
							$('[name^="' + error + '"]').closest('div').addClass('has-error');
							$('[name^="' + error + '"]').parents('div:first').append('<p class="error" id="error_' + error + '">' + json.errors[error] + '</p>');
						} else {
							$('#error-holder').empty().append(json.errors[error] + '<BR>').show();
							window.scrollTo(0,0);
						}
					}
				} else {
					window.location = json.redirect;
				}
			}
		});
	});
});