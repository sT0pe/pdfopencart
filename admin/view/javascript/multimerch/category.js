$(function() {
    // Categories
    $('#list-categories').dataTable({
        "sAjaxSource": "index.php?route=multimerch/category/getCategoryTableData&token=" + msGlobals.token,
        "aoColumns": [
            { "mData": "checkbox", "bSortable": false },
            { "mData": "name" },
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

    $(document).on('click', '.ms-cat-change-status, .ms-cat-assign-seller', function(e) {
        e.preventDefault();

        var button = $(this);
        var category_id = button.closest('tr').children('td:first').find('input:checkbox').val();
        var url = 'index.php?route=multimerch/category';

        if (button.hasClass('ms-cat-change-status')) {
            var status_id = button.data('status');
            url += '/jxChangeStatus&category_status=' + status_id;
        } else if (button.hasClass('ms-cat-assign-seller')) {
            var seller_id = button.siblings('select').val();
            url += '/jxChangeSeller&seller_id=' + seller_id + (seller_id == 0 ? '&category_status=' + msGlobals.status_active : '');
        }

        url += '&category_id=' + category_id;

        $.ajax({
            type: "get",
            dataType: "json",
            url: url + '&token=' + msGlobals.token,
            success: function(json) {
                if (json.redirect) {
                    window.location.reload();
                } else if (json.error){
                    $('.alert-danger').text(json.error).show();
                }
            }
        });
    });

    $(document).on('click', '.ms-cat-delete', function(e) {
        var button = $(this);
        var category_id = button.closest('tr').children('td:first').find('input:checkbox').val();

        if(confirm('Are you sure?')) {
            $.ajax({
                type: "get",
                dataType: "json",
                url: 'index.php?route=multimerch/category/jxDeleteCategory&category_id=' + category_id + '&token=' + msGlobals.token,
                success: function (json) {
                    if (json.redirect) {
                        $('.alert-danger').hide();
                        window.location.reload();
                    } else if (json.error) {
                        $('.alert-danger').text(json.error).show();
                    }
                }
            });
        }
    });

    // Global actions
    $(document).on('click', '#ms-cats-approve', function(e) {
        e.preventDefault();

        var selected_categories = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_categories.push(parseInt($(item).val()));
        });

        if(selected_categories.length > 0) {
            $.ajax({
                url: 'index.php?route=multimerch/category/jxChangeStatus&category_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
                type: 'post',
                data: {selected_categories: selected_categories},
                dataType: 'json',
                success: function (json) {
                    if (json.redirect) {
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

    $(document).on('click', '#ms-cats-delete', function(e) {
        e.preventDefault();

        var selected_categories = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_categories.push(parseInt($(item).val()));
        });

        if(selected_categories.length > 0) {
            if(confirm('Are you sure?')) {
                $.ajax({
                    url: 'index.php?route=multimerch/category/jxDeleteCategory&token=' + msGlobals.token,
                    type: 'post',
                    data: {selected_categories: selected_categories},
                    dataType: 'json',
                    success: function (json) {
                        if (json.redirect) {
                            $('.alert-danger').hide();
                            window.location.reload();
                        } else if (json.error) {
                            $('.alert-danger').text(json.error).show();
                        }
                    }
                });
            }
        } else {
            $('.alert-danger').text(msGlobals.error_not_selected).show();
        }
    });

    // General
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        window.scrollTo(0, 0);
    }
});