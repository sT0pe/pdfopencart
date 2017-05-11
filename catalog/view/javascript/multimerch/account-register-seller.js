$(function() {
    $("#ms-submit-button").click(function() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: $('base').attr('href') + 'index.php?route=account/register-seller/jxsavesellerinfo',
            data: $("form#seller-form").serialize(),
            beforeSend: function() {
                $('p.error').remove();
                $('.warning.main').hide();
                $('.form-group').removeClass("has-error");
            },
            complete: function(jqXHR, textStatus) {
                if (textStatus != 'success') {
                    $(".warning.main").text(msGlobals.formError).show();
                    window.scrollTo(0,0);
                }
            },
            success: function(jsonData) {
                if (!jQuery.isEmptyObject(jsonData.errors)) {
                    $('.error').text('');

                    for (error in jsonData.errors) {
                        if ($('#error_' + error).length > 0) {
                            $('#error_' + error).text(jsonData.errors[error]);
                            $('#error_' + error).parents('.form-group').addClass('has-error');
                        } else if ($('[name="'+error+'"]').length > 0) {
                            $('[name="' + error + '"]').parents('.form-group').addClass('has-error');
                            $('[name="' + error + '"]').parents('div:first').append('<p class="error">' + jsonData.errors[error] + '</p>');
                        } else $(".warning.main").append("<p>" + jsonData.errors[error] + "</p>").show();
                    }
                    window.scrollTo(0,0);
                } else {
                    window.location = jsonData.redirect;
                }
            }
        });
    });
});