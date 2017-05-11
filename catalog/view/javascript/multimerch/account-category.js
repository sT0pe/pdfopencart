$(function() {


	// Categories list

	$('#list-categories').dataTable( {
		"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-category/getTableData",
		"aoColumns": [
			{ "mData": "name"},
			{ "mData": "status" },
			{ "mData": "sort_order" },
			{ "mData": "actions", "bSortable": false, "sClass": "text-right" }
		]
	});

	$(document).on('click', '.icon-remove.ms_remove_category', function(e) {
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

});