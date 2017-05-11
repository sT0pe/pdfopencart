<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
          <a href="<?php echo $insert; ?>" data-toggle="tooltip" title="<?php echo $button_add; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
      </div>
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
   <?php if (isset($success) && $success) { ?>
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
        <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
		<table class="list table table-bordered table-hover" style="text-align: center" id="list-seller-groups">
			<thead>
				<tr>
					<td><?php echo $ms_seller_groups_column_id; ?></td>
					<td style="width: 100px"><?php echo $ms_seller_groups_column_name; ?></td>
					<td><?php echo $ms_description; ?></td>
					<td style="width: 450px"><?php echo $ms_commission_actual; ?></td>
					<td><?php echo $ms_seller_groups_column_action; ?></td>
				</tr>
				<tr class="filter">
					<td><input type="text"/></td>
					<td><input type="text"/></td>
					<td><input type="text"/></td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			<tbody></tbody>
		</table>
		</form>
		</div>
      </div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#list-seller-groups').dataTable( {
		"sAjaxSource": "index.php?route=multimerch/seller-group/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "name" },
			{ "mData": "description" },
			{ "mData": "rates", "bSortable": false },
			{ "mData": "actions", "bSortable": false, "sClass": "large text-right" }
		]
	});

	$(document).on('click', '.ms-button-delete', function() {
    	return confirm("<?php echo $this->language->get('text_confirm'); ?>");
	});
});
</script>
<?php echo $footer; ?>