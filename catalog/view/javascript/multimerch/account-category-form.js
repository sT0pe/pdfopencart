$(function() {


    // Categories form

    var lang_inputs = $('.lang-select-field');
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

        $('.lang-chooser img').each(function () {
            if ($(this).data('lang') == selectedLang) {
                $(this).addClass('active');
            } else {
                $(this).removeClass('active');
            }
        });
    });

    // Category parent
    $('input[name="path"]').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: 'index.php?route=seller/account-category/jxAutocompleteCategories&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    json.unshift({
                        category_id: 0,
                        name: msGlobals.text_none
                    });

                    response($.map(json, function(item) {
                        if(item['category_id'] != $('input[name="category_id"]').val()) {
                            return {
                                label: item['name'],
                                value: item['category_id']
                            }
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name="path"]').val(item['label']);
            $('input[name="parent_id"]').val(item['value']);
        }
    });

    // Category filters
    $('input[name="filter"]').autocomplete({
        'source': function (request, response) {
            $.ajax({
                url: 'index.php?route=catalog/filter/autocomplete&filter_name=' + encodeURIComponent(request),
                dataType: 'json',
                success: function (json) {
                    response($.map(json, function (item) {
                        return {
                            label: item['name'],
                            value: item['filter_id']
                        }
                    }));
                }
            });
        },
        'select': function (item) {
            $('input[name="filter"]').val('');
            $('#category-filter' + item['value']).remove();
            $('#category-filter').append('<div id="category-filter' + item['value'] + '"><i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category_filter[]" value="' + item['value'] + '" /></div>');
        }
    });

    $('#category-filter').delegate('.fa-minus-circle', 'click', function () {
        $(this).parent().remove();
    });

    $("#ms-submit-button").click(function () {
        var button = $(this);

        if (msGlobals.config_enable_rte == 1) {
            for (var instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        }

        $.ajax({
            type: "POST",
            dataType: "json",
            url: $('base').attr('href') + 'index.php?route=seller/account-category/jxSaveCategory',
            data: $("form#ms-new-category").serialize(),
            beforeSend: function () {
                $('.error').html('');
                $('#error-holder').hide();
                $('div.has-error').removeClass('has-error');
            },
            complete: function (jqXHR, textStatus) {
                if (textStatus != 'success') {
                    $('#error-holder').empty().text(msGlobals.formError).show();
                    window.scrollTo(0, 0);
                }
            },
            error: function () {
                $('#error-holder').empty().text(msGlobals.formError).show();
                window.scrollTo(0, 0);
            },
            success: function (json) {
                if (json.errors) {
                    button.button('reset');
                    $('#error-holder').empty();

                    for (error in json.errors) {
                        if ($('[name^="' + error + '"]').length > 0) {
                            $('[name^="' + error + '"]').closest('div').addClass('has-error');
                            $('[name^="' + error + '"]').parents('div:first').append('<p class="error" id="error_' + error + '">' + json.errors[error] + '</p>');
                        }

                        $('#error-holder').append(json.errors[error] + '<BR>').show();
                    }
                    window.scrollTo(0, 0);
                } else {
                    window.location = json.redirect;
                }
            }
        });
    });
});