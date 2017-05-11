<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">

    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $ms_shipping_method_heading; ?></h1>
            <div class="pull-right">
                <a href="<?php echo $add; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if (isset($success) && $success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_shipping_method_heading; ?></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form">

                        <table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-shipping-methods">

                            <thead>
                            <tr>
                                <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
                                <td class="small">Name</td>
                                <td class="large">Description</td>
                                <td class="small">Status</td>
                                <td class="small">Action</td>
                            </tr>
                            <tr class="filter">
                                <td></td>
                                <td><input type="text"/></td>
                                <td><input type="text"/></td>
                                <td><input type="text"/></td>
                                <td></td>
                            </tr>
                            </thead>

                            <tbody>
                            </tbody>
                        </table>

                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
</script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#list-shipping-methods').dataTable( {
            "sAjaxSource": "index.php?route=multimerch/shipping-method/getTableData&token=<?php echo $token; ?>",
            "aoColumns": [
                { "mData": "checkbox", "bSortable": false },
                { "mData": "name" },
                { "mData": "description" },
//                { "mData": "logo", "bSortable": false },
                { "mData": "status" },
                { "mData": "actions", "bSortable": false, "sClass": "text-right" }
            ],
            "initComplete": function(settings, json) {
                var api = this.api();
                var statusColumn = api.column('#status_column');

                $('#status_select').change( function() {
                    statusColumn.search( $(this).val() ).draw();
                });
            }

        });
    });
</script>
<?php echo $footer; ?>