$(function() {
    function getPaymentInfo() {
        if($('input[name="payment_method"]').length > 0) {
            var pg_code = $('input[name="payment_method"]:checked').val().replace('ms_pg_', '');

            $.ajax({
                url: 'index.php?route=multimerch/payment/' + pg_code + '/jxGetPaymentForm',
                type: 'post',
                data: {pg_code: pg_code},
                dataType: 'json',
                beforeSend: function () {
                    $('.methods-info').fadeOut(10);
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
    $(document).on('click', 'input[name="payment_method"]', getPaymentInfo);
});