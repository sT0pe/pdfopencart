<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <h1><?php echo $heading; ?></h1>
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
        <?php if ($success) { ?>
            <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php } ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $heading; ?></h3>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <form class="form-inline" action="" method="post" enctype="multipart/form-data" id="form">
                        <table class="list mmTable table table-bordered table-hover" style="text-align: center" id="list-payment-gateways">
                            <thead>
                                <tr>
                                    <td class="small"><?php echo $ms_name; ?></td>
                                    <td class="large"><?php echo $ms_logo; ?></td>
                                    <td class="small"><?php echo $ms_config_status; ?></td>
                                    <td class="small"><?php echo $ms_action; ?></td>
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
        $('#list-payment-gateways').dataTable( {
            "sAjaxSource": "index.php?route=multimerch/payment-gateway/getTableData&token=<?php echo $token; ?>",
            "aoColumns": [
                { "mData": "name", "bSortable": false },
                { "mData": "logo", "bSortable": false },
                { "mData": "status", "bSortable": false  },
                { "mData": "actions", "bSortable": false, "sClass": "text-right" }
            ]
        });

        $(document).on('click', '.pg_uninstall', function(e) {
            confirm("<?php echo $ms_pg_uninstall_warning; ?>") ? location.href($(this).attr('href')) : e.preventDefault();
        });
    });
</script>
<?php echo $footer; ?>