$(document).on('ready', function() {
    var maxlength = parseInt($('#rating-comment').attr('maxlength'));
    $(".rating-comment-note").html(maxlength + " characters left");

    $(document).on('keyup', '#rating-comment', function() {
        if(this.value.length > maxlength) {
            return false;
        }
        $(".rating-comment-note").html((maxlength - this.value.length) + " characters left");
    });

    // Form validation
    $(document).on('click', '.form-rate-submit', function(e) {
        if($('#rating-input-xs').val() == 0 || $('#rating-comment').val() == '') {
            e.preventDefault();
            $('.form-rate-error').html('Please, check all the fields are filled!');
            console.log($('.form-rate-error'))
            $('.form-rate-error').removeClass('hidden');
        } else {
            $('.form-rate-error').addClass('hidden');
        }
    });
});

