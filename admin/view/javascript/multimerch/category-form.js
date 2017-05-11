$(function(){
    // Show only selected language's fields
    var lang_inputs = $('.lang-select-field');
    var current_language = msGlobals.current_language;
    for(var i = 0; i < lang_inputs.length; i++) {
        if($(lang_inputs[i]).data('lang') != current_language) {
            $(lang_inputs[i]).hide();
            $(lang_inputs[i]).siblings('.lang-img-icon-input').hide();
        } else {
            $(lang_inputs[i]).show();
            $(lang_inputs[i]).siblings('.lang-img-icon-input').show();
        }
    }

    // Language select
    $(".select-input-lang").on("click", function() {
        var selectedLang = $(this).data('lang');

        $('.lang-select-field').each(function() {
            if ($(this).data('lang') == selectedLang) {
                $(this).show();
                $(this).siblings('.lang-img-icon-input').show();
            } else {
                $(this).hide();
                $(this).siblings('.lang-img-icon-input').hide();
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

    // Category parent
    $('input[name="path"]').autocomplete({
        'source': function(request, response) {
            $.ajax({
                url: 'index.php?route=multimerch/category/jxAutocompleteCategories&token=' + msGlobals.token + '&filter_name=' +  encodeURIComponent(request),
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
        'source': function(request, response) {
            $.ajax({
                url: 'index.php?route=multimerch/category/jxAutocompleteFilters&token=' + msGlobals.token + '&filter_name=' +  encodeURIComponent(request),
                dataType: 'json',
                success: function(json) {
                    response($.map(json, function(item) {
                        return {
                            label: item['name'],
                            value: item['filter_id']
                        }
                    }));
                }
            });
        },
        'select': function(item) {
            $('input[name="filter"]').val('');
            $('#category-filter' + item['value']).remove();

            var filter_row_html = '';
            filter_row_html += '<div id="category-filter' + item['value'] + '">';
            filter_row_html += '	<i class="fa fa-minus-circle"></i> ' + item['label'] + '<input type="hidden" name="category_filter[]" value="' + item['value'] + '" />';
            filter_row_html += '</div>';

            $('#category-filter').append(filter_row_html);
        }
    });

    // Remove category filter
    $('#category-filter').delegate('.fa-minus-circle', 'click', function() {
        $(this).parent().remove();
    });

    $('#ms-submit-button').click(function() {
        var button = $(this);

        $.ajax({
            type: 'post',
            url: 'index.php?route=multimerch/category/jxSaveCategory&token=' + msGlobals.token,
            data: $('#ms-category-form').serialize(),
            dataType: 'json',
            success: function(json) {
                if(json.errors) {
                    for (error in json.errors) {
                        if ($('[name^="' + error + '"]').length > 0) {
                            $('[name^="' + error + '"]').closest('div').addClass('has-error');
                            $('[name^="' + error + '"]').parents('div:first').append('<div class="text-danger" id="error_' + error + '">' + json.errors[error] + '</div>');
                        } else {
                            $('#error-holder').empty().append(json.errors[error] + '<BR>').show();
                            window.scrollTo(0,0);
                        }
                    }
                } else {
                    window.location = json.redirect.replace('&amp;', '&');
                }
            }
        });
    })
});