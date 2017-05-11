$(function() {
    $("#ms-button-pay").click(function(e) {
        e.preventDefault();
        if($("#list-payment-requests tbody input:checkbox:checked").length == 0) {
            show_error(msGlobals.ms_pg_request_error_select_payment_request);
        } else {
            $(".error-holder").html('');
            $("#form").submit();
        }
    });

    $("#ms-button-delete").click(function(e) {
        e.preventDefault();
        var request_ids = $("#list-payment-requests tbody input:checkbox:checked");
        var data = [];

        if(request_ids.length == 0) {
            show_error(msGlobals.ms_pg_request_error_select_payment_request);
        } else {
            $.map(request_ids, function(item) {
                data.push($(item).val());
            });

            if(confirm('Are you sure?')) {
                $.ajax({
                    url: 'index.php?route=multimerch/payment-request/jxDelete&token=' + msGlobals.token,
                    data: {request_ids: data},
                    type: 'post',
                    dataType: 'json',
                    success: function(json) {
                        if(json.errors) {
                            // todo fix errors showing
                            var html = '';
                            html += '<ul style="list-style: none;">';
                            $.map(json.errors, function(item) {
                                html += '<li>' + item + '</li>';
                            });
                            html += '</ul>';

                            show_error(html);
                        } else if(json.success) {
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