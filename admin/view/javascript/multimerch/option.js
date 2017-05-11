$(function() {
    // Options
    $('#list-options').dataTable({
        "sAjaxSource": "index.php?route=multimerch/option/getTableData&token=" + msGlobals.token,
        "aoColumns": [
            { "mData": "checkbox", "bSortable": false },
            { "mData": "name" },
            { "mData": "values", "bSortable": false },
            { "mData": "seller" },
            { "mData": "status" },
            { "mData": "sort_order" },
            { "mData": "actions", "bSortable": false, "sClass": "text-right"}
        ],
        "initComplete": function(settings, json) {
            var api = this.api();
            var statusColumn = api.column('#status_column');

            $('#status_select').change(function() {
                statusColumn.search($(this).val()).draw();
            });
        }
    });

    $(document).on('click', '.ms-change-status, .ms-assign-seller', function(e) {
        e.preventDefault();

        var button = $(this);
        var option_id = button.closest('tr').children('td:first').find('input:checkbox').val();
        var url = 'index.php?route=multimerch/option/jxUpdateOption&option_id=' + option_id;

        if (button.hasClass('ms-change-status')) {
            var status_id = button.data('status');
            url += '&option_status=' + status_id;
        } else if (button.hasClass('ms-assign-seller')) {
            var seller_id = button.siblings('select').val();
            url += '&seller_id=' + seller_id + (seller_id == 0 ? '&option_status=' + msGlobals.status_active : '');
        }

        $.ajax({
            type: "get",
            dataType: "json",
            url: url + '&token=' + msGlobals.token,
            success: function(json) {
                if (json.success) {
                    window.location.reload();
                } else if (json.error){
                    $('.alert-danger').text(json.error).show();
                }
            }
        });
    });

    $(document).on('click', '.ms-delete', function(e) {
        var button = $(this);
        var option_id = button.closest('tr').children('td:first').find('input:checkbox').val();

        $.ajax({
            type: "get",
            dataType: "json",
            url: 'index.php?route=multimerch/option/jxDeleteOption&option_id=' + option_id + '&token=' + msGlobals.token,
            success: function(json) {
                if (json.success) {
                    $('.alert-danger').hide();
                    window.location.reload();
                } else if (json.error){
                    $('.alert-danger').text(json.error).show();
                }
            }
        });
    });

    // Global actions with options
    $(document).on('click', '#ms-opts-approve', function(e) {
        e.preventDefault();

        var selected_options = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_options.push(parseInt($(item).val()));
        });

        if(selected_options.length > 0) {
            $.ajax({
                url: 'index.php?route=multimerch/option/jxUpdateOption&option_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
                type: 'post',
                data: {selected_options: selected_options},
                dataType: 'json',
                success: function (json) {
                    if (json.success) {
                        $('.alert-danger').hide();
                        window.location.reload();
                    } else if (json.error) {
                        $('.alert-danger').text(json.error).show();
                    }
                }
            });
        } else {
            $('.alert-danger').text(msGlobals.error_not_selected).show();
        }
    });

    $(document).on('click', '#ms-opts-delete', function(e) {
        e.preventDefault();

        var selected_options = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_options.push(parseInt($(item).val()));
        });

        if(selected_options.length > 0) {
            if(confirm('Are you sure?')) {
                $.ajax({
                    url: 'index.php?route=multimerch/option/jxDeleteOption&token=' + msGlobals.token,
                    type: 'post',
                    data: {selected_options: selected_options},
                    dataType: 'json',
                    success: function (json) {
                        if (json.success) {
                            $('.alert-danger').hide();
                            window.location.reload();
                        } else if (json.errors) {
                            var errors_html = '';
                            $.map(json.errors, function(item) {
                                errors_html += item + '<br/>';
                            });

                            $('.alert-danger').html(errors_html).show();
                        }
                    }
                });
            }
        } else {
            $('.alert-danger').text(msGlobals.error_not_selected).show();
        }
    });
});