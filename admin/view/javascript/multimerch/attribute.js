$(function() {
    // Attributes
    $('#list-attributes').dataTable({
        "sAjaxSource": "index.php?route=multimerch/attribute/getAttributeTableData&token=" + msGlobals.token,
        "aoColumns": [
            { "mData": "checkbox", "bSortable": false },
            { "mData": "name" },
            { "mData": "group_name" },
            { "mData": "seller" },
            { "mData": "status" },
            { "mData": "sort_order" },
            { "mData": "actions", "bSortable": false, "sClass": "text-right"}
        ],
        "initComplete": function(settings, json) {
            var api = this.api();
            var statusColumn = api.column('#attr_status_column');

            $('#attr_status_select').change(function() {
                statusColumn.search($(this).val()).draw();
            });
        }
    });

    $(document).on('click', '.ms-attr-change-status, .ms-attr-assign-seller', function(e) {
        e.preventDefault();

        var button = $(this);
        var attribute_id = button.closest('tr').children('td:first').find('input:checkbox').val();
        var url = 'index.php?route=multimerch/attribute/jxUpdateAttribute&attribute_id=' + attribute_id;

        if (button.hasClass('ms-attr-change-status')) {
            var status_id = button.data('status');
            url += '&attribute_status=' + status_id;
        } else if (button.hasClass('ms-attr-assign-seller')) {
            var seller_id = button.siblings('select').val();
            url += '&seller_id=' + seller_id + (seller_id == 0 ? '&attribute_status=' + msGlobals.status_active : '');
        }

        $.ajax({
            type: "get",
            dataType: "json",
            url: url + '&token=' + msGlobals.token,
            success: function(json) {
                if (json.redirect) {
                    window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute';
                    window.scrollTo(0, 0);
                    window.location.reload();
                } else if (json.error) {
                    $('.alert-danger').text(json.error).show();
                }
            }
        });
    });

    $(document).on('click', '.ms-attr-delete', function(e) {
        var button = $(this);
        var attribute_id = button.closest('tr').children('td:first').find('input:checkbox').val();

        if(confirm('Are you sure?')) {
            $.ajax({
                type: "get",
                dataType: "json",
                url: 'index.php?route=multimerch/attribute/jxDeleteAttribute&attribute_id=' + attribute_id + '&token=' + msGlobals.token,
                success: function (json) {
                    if (json.redirect) {
                        $('.alert-danger').hide();
                        window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute';
                        window.scrollTo(0, 0);
                        window.location.reload();
                    } else if (json.error) {
                        $('.alert-danger').text(json.error).show();
                    }
                }
            });
        }
    });

    // Global actions with attributes
    $(document).on('click', '#ms-attrs-approve', function(e) {
        e.preventDefault();

        var selected_attributes = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_attributes.push(parseInt($(item).val()));
        });

        if(selected_attributes.length > 0) {
            $.ajax({
                url: 'index.php?route=multimerch/attribute/jxUpdateAttribute&attribute_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
                type: 'post',
                data: {selected_attributes: selected_attributes},
                dataType: 'json',
                success: function (json) {
                    if (json.redirect) {
                        window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute';
                        window.scrollTo(0, 0);
                        window.location.reload();
                    } else if (json.error) {
                        $('.alert-danger').text(json.error).show();
                    }
                }
            });
        } else {
            $('.alert-danger').text(msGlobals.error_attr_not_selected).show();
        }
    });

    $(document).on('click', '#ms-attrs-delete', function(e) {
        e.preventDefault();

        var selected_attributes = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_attributes.push(parseInt($(item).val()));
        });

        if(selected_attributes.length > 0) {
            if(confirm('Are you sure?')) {
                $.ajax({
                    url: 'index.php?route=multimerch/attribute/jxDeleteAttribute&token=' + msGlobals.token,
                    type: 'post',
                    data: {selected_attributes: selected_attributes},
                    dataType: 'json',
                    success: function (json) {
                        if (json.redirect) {
                            $('.alert-danger').hide();
                            window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute';
                            window.scrollTo(0, 0);
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
            $('.alert-danger').text(msGlobals.error_attr_not_selected).show();
        }
    });

    // Attribute groups
    $('#list-attribute-groups').dataTable({
        "sAjaxSource": "index.php?route=multimerch/attribute/getAttributeGroupTableData&token=" + msGlobals.token,
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
            var statusColumn = api.column('#attr_gr_status_column');

            $('#attr_gr_status_select').change(function() {
                statusColumn.search($(this).val()).draw();
            });
        }
    });

    $(document).on('click', '.ms-attr-gr-change-status, .ms-attr-gr-assign-seller', function(e) {
        e.preventDefault();

        var button = $(this);
        var attribute_group_id = button.closest('tr').children('td:first').find('input:checkbox').val();
        var url = 'index.php?route=multimerch/attribute/jxUpdateAttributeGroup&attribute_group_id=' + attribute_group_id;

        if (button.hasClass('ms-attr-gr-change-status')) {
            var status_id = button.data('status');
            url += '&attribute_group_status=' + status_id;
        } else if (button.hasClass('ms-attr-gr-assign-seller')) {
            var seller_id = button.siblings('select').val();
            url += '&seller_id=' + seller_id + (seller_id == 0 ? '&attribute_group_status=' + msGlobals.status_active : '');
        }

        $.ajax({
            type: "get",
            dataType: "json",
            url: url + '&token=' + msGlobals.token,
            success: function(json) {
                if (json.redirect) {
                    window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute-group';
                    window.scrollTo(0, 0);
                    window.location.reload();
                } else if (json.error){
                    $('.alert-danger').text(json.error).show();
                }
            }
        });
    });

    $(document).on('click', '.ms-attr-gr-delete', function(e) {
        var button = $(this);
        var attribute_group_id = button.closest('tr').children('td:first').find('input:checkbox').val();

        if(confirm('Are you sure?')) {
            $.ajax({
                type: "get",
                dataType: "json",
                url: 'index.php?route=multimerch/attribute/jxDeleteAttributeGroup&attribute_group_id=' + attribute_group_id + '&token=' + msGlobals.token,
                success: function (json) {
                    if (json.redirect) {
                        $('.alert-danger').hide();
                        window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute-group';
                        window.scrollTo(0, 0);
                        window.location.reload();
                    } else if (json.error) {
                        $('.alert-danger').text(json.error).show();
                    }
                }
            });
        }
    });

    // Global actions with attribute groups
    $(document).on('click', '#ms-attr-grs-approve', function(e) {
        e.preventDefault();

        var selected_attribute_groups = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_attribute_groups.push(parseInt($(item).val()));
        });

        if(selected_attribute_groups.length > 0) {
            $.ajax({
                url: 'index.php?route=multimerch/attribute/jxUpdateAttributeGroup&attribute_group_status=' + msGlobals.status_active + '&token=' + msGlobals.token,
                type: 'post',
                data: {selected_attribute_groups: selected_attribute_groups},
                dataType: 'json',
                success: function (json) {
                    if (json.redirect) {
                        window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute-group';
                        window.scrollTo(0, 0);
                        window.location.reload();
                    } else if (json.error) {
                        $('.alert-danger').text(json.error).show();
                    }
                }
            });
        } else {
            $('.alert-danger').text(msGlobals.error_attr_gr_not_selected).show();
        }
    });

    $(document).on('click', '#ms-attr-grs-delete', function(e) {
        e.preventDefault();

        var selected_attribute_groups = [];
        $.map($('input[name="selected[]"]:checked'), function(item) {
            selected_attribute_groups.push(parseInt($(item).val()));
        });

        if(selected_attribute_groups.length > 0) {
            if(confirm('Are you sure?')) {
                $.ajax({
                    url: 'index.php?route=multimerch/attribute/jxDeleteAttributeGroup&token=' + msGlobals.token,
                    type: 'post',
                    data: {selected_attribute_groups: selected_attribute_groups},
                    dataType: 'json',
                    success: function (json) {
                        if (json.redirect) {
                            $('.alert-danger').hide();
                            window.location = json.redirect.replace('&amp;', '&') + '#tab-attribute-group';
                            window.scrollTo(0, 0);
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
            $('.alert-danger').text(msGlobals.error_attr_gr_not_selected).show();
        }
    });

    // General
    setTimeout(function() {
        var dataTables = $('table.dataTable');
        $.map(dataTables, function(item) {
            if($(item).find('tbody tr:first td').length == 1) {
                $(item).find('tbody tr:first td').attr('colspan', '100%');
            }
        });
    }, 500);

    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        window.scrollTo(0, 0);
    }

    $(document).on('click', '.ms-attributes-topbar li a', function() {
        var buttons = $('.page-header .pull-right a');

        $.map($('input[name="selected[]"]:checked'), function(item) {
            $(item).attr('checked', false);
        });

        if($(this).attr('href') == '#tab-attribute') {
            $.map(buttons, function(item) {
                var new_id = $(item).attr('id').replace('attr-grs', 'attrs')
                $(item).attr('id', new_id);
            });
        }

        if($(this).attr('href') == '#tab-attribute-group') {
            $.map(buttons, function(item) {
                var new_id = $(item).attr('id').replace('attrs', 'attr-grs')
                $(item).attr('id', new_id);
            });
        }
    })
});