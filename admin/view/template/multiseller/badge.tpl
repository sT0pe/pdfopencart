<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-badge-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
		  <a href="<?php echo $insert; ?>" data-toggle="tooltip" class="btn btn-primary"><i class="fa fa-plus"></i></a>
		  <button type="button" data-toggle="tooltip" title="" class="btn btn-danger" onclick="$('form').submit()" data-original-title="<?php echo $button_delete; ?>"><i class="fa fa-trash-o"></i></button>
      </div>
      <h1><?php echo $ms_catalog_badges_heading; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_catalog_badges_heading; ?></h3>
      </div>
      <div class="panel-body">
	  <form action="<?php echo $delete; ?>" method="post" enctype="multipart/form-data" id="form">
		<div class="table-responsive">
			<table class="list table table-bordered table-hover" style="text-align: center" id="list-badges">
			<thead>
				<tr>
					<td class="tiny"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
					<td class="tiny"><?php echo $ms_badges_column_id; ?></td>
					<td class="large"><?php echo $ms_badges_column_name; ?></td>
					<td><?php echo $ms_description; ?></td>
					<td class="medium"><?php echo $ms_badges_image; ?></td>
					<td class="medium"><?php echo $ms_badges_column_action; ?></td>
				</tr>

				<tr class="filter">
					<td></td>
					<td><input type="text"/></td>
					<td><input type="text"/></td>
					<td><input type="text"/></td>
					<td></td>
					<td></td>
				</tr>
			</thead>
			<tbody></tbody>
			</table>
		</div>
	  </form>	
      </div>
	</div>
  </div>
</div>

<script type="text/javascript">
$(function() {
	$('#list-badges').dataTable( {
		"sAjaxSource": "index.php?route=multimerch/badge/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "checkbox", "bSortable": false },
			{ "mData": "id" },
			{ "mData": "name" },
			{ "mData": "description" },
			{ "mData": "image", "bSortable": false },
			{ "mData": "actions", "bSortable": false, "sClass": "right" }
		]
	});
});
</script>

<?php echo $footer; ?> 