$(function() {
    $("#ms-button-delete").click(function(e) {
        e.preventDefault();
        var payment_ids = $("#list-payments tbody input:checkbox:checked");
        var data = [];

        if(payment_ids.length == 0) {
	        show_error(msGlobals.ms_pg_payment_error_no_methods);
        } else {
            $.map(payment_ids, function(item) {
                data.push($(item).val());
            });

            if(confirm('This will also delete all linked payment requests. Are you sure you want to delete the payment(s)?')) {
                $.ajax({
                    url: 'index.php?route=multimerch/payment/jxDelete&token=' + msGlobals.token,
                    data: {payment_ids: data},
                    type: 'post',
                    dataType: 'json',
                    success: function (json) {
                        if (json.errors) {
                            // todo fix errors showing
                            var html = '';
                            html += '<ul style="list-style: none;">';
                            $.map(json.errors, function (item) {
                                html += '<li>' + item + '</li>';
                            });
                            html += '</ul>';

                            show_error(html);
                        } else if (json.success) {
                            window.location.reload();
                        }
                    }
                });
            }
        }
    });

    function show_error(error) {
        var html = '';
        html += '<div class="cl"></div>';
        html += '<div class="alert alert-danger">'
        html += '<i class="fa fa-exclamation-circle"></i> ';
        html += error;
        html += '<button type="button" class="close" data-dismiss="alert">&times;</button>';
        html += '</div>';
        $(".error-holder").html(html);
    }
});
