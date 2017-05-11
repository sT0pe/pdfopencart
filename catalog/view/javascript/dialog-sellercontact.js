$(function() {
    $("#sendMessage").click(function() {
        $.ajax({
            type: "POST",
            dataType: "json",
            url: $('base').attr('href') + 'index.php?route=seller/catalog-seller/jxSubmitContactDialog',
            data: $("#contactForm").serialize(),
            beforeSend: function() {
            },
            complete: function() {
            },
            success: function(data) {
                $('#contactDialog').find('.alert-danger, .alert-success').remove();

                if (!jQuery.isEmptyObject(data.errors)) {
                    console.log(data.errors);
                    for (error in data.errors) {
                        $('#contactDialog .modal-body').append('<div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i>' + data.errors[error] + '</div>');
                    }
                }

                if (data.success) {
                    $('#contactDialog').find('textarea').val('');
                    $('#contactDialog .modal-body').append('<div class="alert alert-success"><i class="fa fa-exclamation-circle"></i>' + data.success + '</div>');
                }
            }
        });
    });
});
