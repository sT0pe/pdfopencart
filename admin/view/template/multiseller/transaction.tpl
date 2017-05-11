<?php echo $header; ?><?php echo $column_left; ?>
<div id="content" class="ms-transaction-page">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right"><a href="<?php echo $link_create_transaction; ?>" data-toggle="tooltip" title="<?php echo $ms_transactions_new; ?>" class="btn btn-primary"><i class="fa fa-plus"></i></a>
      </div>
      <h1><?php echo $ms_transactions_heading; ?></h1>
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
        <h3 class="panel-title"><i class="fa fa-list"></i> <?php echo $ms_transactions_heading; ?></h3>
      </div>
      <div class="panel-body">
		<div class="table-responsive">
		<table class="list table table-bordered table-hover" style="text-align: center" id="list-transactions">
		<thead>
			<tr>
				<td class="tiny"><?php echo $ms_id; ?></td>
				<td class="medium"><?php echo $ms_seller; ?></a></td>
				<td class="small"><?php echo $ms_net_amount; ?></a></td>
				<td><?php echo $ms_description; ?></a></td>
				<td class="medium"><?php echo $ms_date; ?></a></td>
			</tr>
			<tr class="filter">
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text"/></td>
				<td><input type="text" class="input-date-datepicker"/></td>
			</tr>
		</thead>
		<tbody></tbody>
		</table>
		</div>
      </div>
	</div>
  </div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$('#list-transactions').dataTable( {
		"sAjaxSource": "index.php?route=multimerch/transaction/getTableData&token=<?php echo $token; ?>",
		"aoColumns": [
			{ "mData": "id" },
			{ "mData": "seller" },
			{ "mData": "amount" },
			{ "mData": "description" },
			{ "mData": "date_created" },
		],
        "aaSorting":  [[4,'desc']]
	});
});
</script>
<?php echo $footer; ?>