<?php echo $header; ?>
<div class="container">
	<ul class="breadcrumb">
		<?php foreach ($breadcrumbs as $breadcrumb) { ?>
		<li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
		<?php } ?>
	</ul>
	<div class="row"><?php echo $column_left; ?>
		<?php if ($column_left && $column_right) { ?>
		<?php $class = 'col-sm-6'; ?>
		<?php } elseif ($column_left || $column_right) { ?>
		<?php $class = 'col-sm-9'; ?>
		<?php } else { ?>
		<?php $class = 'col-sm-12'; ?>
		<?php } ?>
		<div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
			<h1><?php echo $ms_account_stats_heading; ?></h1>

			<ul class="nav nav-tabs">
				<li class="active"><a href="#tab-summary" data-toggle="tab"><?php echo $ms_account_stats_tab_summary; ?></a></li>
				<li><a href="#tab-by-product" data-toggle="tab"><?php echo $ms_account_stats_tab_by_product; ?></a></li>
				<li><a href="#tab-by-year" data-toggle="tab"><?php echo $ms_account_stats_tab_by_year; ?></a></li>
			</ul>
			<div class="tab-content">
				<div id="tab-summary" class="tab-pane active">
					<p><?php echo $ms_account_stats_summary_comment; ?></p>

					<table class="list table table-bordered table-hover" id="table-summary-1">
						<thead>
						<tr>
							<td class="large"><?php echo $ms_account_stats_sales_data ?></td>
							<td class="medium"><?php echo $ms_account_stats_number_of_orders; ?></td>
							<td class="medium"><?php echo $ms_account_stats_total_revenue; ?></td>
							<td class="medium"><?php echo $ms_account_stats_average_order; ?></td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td><?php echo $ms_account_stats_today . date($date_format_short, strtotime($today)); ?></td>
							<td><?php echo (int)$summary_today['order_num']; ?></td>
							<td><?php echo $this->currency->format($summary_today['total_revenue'], $this->config->get('config_currency')); ?></td>
							<td><?php echo $this->currency->format($summary_today['average_revenue'], $this->config->get('config_currency')); ?></td>
						</tr>
						<tr>
							<td><?php echo $ms_account_stats_yesterday . date($date_format_short, strtotime($yesterday)); ?></td>
							<td><?php echo (int)$summary_yesterday['order_num']; ?></td>
							<td><?php echo $this->currency->format($summary_yesterday['total_revenue'], $this->config->get('config_currency')); ?></td>
							<td><?php echo $this->currency->format($summary_yesterday['average_revenue'], $this->config->get('config_currency')); ?></td>
						</tr>
						<tr>
							<td><?php echo $ms_account_stats_daily_average . date($ms_account_stats_date_month_format, strtotime($today)); ?></td>
							<td><?php echo $summary_month_daily['order_num']; ?></td>
							<td><?php echo $this->currency->format($summary_month_daily['total_revenue'], $this->config->get('config_currency')); ?></td>
							<td><?php echo $this->currency->format($summary_month_daily['average_revenue'], $this->config->get('config_currency')); ?></td>
						</tr>
						<tr>
							<td><?php echo $ms_account_stats_projected_totals . date($ms_account_stats_date_month_format, strtotime($today)); ?></td>
							<td><?php echo $summary_month_projected['order_num']; ?></td>
							<td><?php echo $this->currency->format($summary_month_projected['total_revenue'], $this->config->get('config_currency')); ?></td>
							<td></td>
						</tr>
						</tbody>
					</table>

					<table class="list table table-bordered table-hover" id="table-summary-2">
						<thead>
						<tr>
							<td class="left" colspan="2"><?php echo $ms_account_stats_statistics; ?></td>
						</tr>
						</thead>
						<tbody>
						<tr>
							<td class="left"><?php echo $ms_account_stats_grand_total_sales; ?></td>
							<td class="tiny right"><?php echo $this->currency->format($grand_total, $this->config->get('config_currency')); ?></td>
						</tr>
						</tbody>
					</table>
				</div>

				<div id="tab-by-product" class="tab-pane">
					<table class="list table table-bordered table-hover" id="table-by-products">
						<thead>
						<tr>
							<td class="tiny"><?php echo $ms_id; ?></td>
							<td class="large"><?php echo $ms_account_stats_product; ?></td>
							<td class="tiny"><?php echo $ms_account_stats_sold; ?></td>
							<td class="medium"><?php echo $ms_account_stats_total; ?></td>
						</tr>
						<tr class="filter">
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
							<td><input type="text"/></td>
						</tr>
						</thead>

						<tbody></tbody>
					</table>
				</div>

				<div id="tab-by-year" class="tab-pane">
					<div>
						<div style="float: right; margin: 5px;">
							<?php echo $ms_account_stats_show_orders; ?>
							<select id="year_select">
								<?php foreach($years as $key=>$year){ ?>
								<option value="<?php echo $key; ?>"><?php echo $year; ?></option>
								<?php } ?>
							</select>
						</div>
						<div style="float: left; margin: 5px;">
							<?php echo sprintf($ms_account_stats_year_comment, $sales); ?>
						</div>
					</div>

					<table class="list table table-bordered table-hover" id="table-by-year">
						<thead>
						<tr>
							<td class="medium"><?php echo $ms_account_stats_month; ?></td>
							<td class="tiny"><?php echo $ms_account_stats_num_of_orders; ?></td>
							<td class="medium"><?php echo $ms_account_stats_total_r; ?></td>
							<td class="medium"><?php echo $ms_account_stats_average_order; ?></td>
						</tr>
						</thead>

						<tbody></tbody>
					</table>

					<table class="list table table-bordered table-hover" id="table-by-year-total">
						<thead>
						<tr>
							<td class="medium"></td>
							<td class="tiny"><?php echo $ms_account_stats_num_of_orders; ?></td>
							<td class="medium"><?php echo $ms_account_stats_total_r; ?></td>
							<td class="medium"><?php echo $ms_account_stats_average_order; ?></td>
						</tr>
						</thead>

						<tbody></tbody>
					</table>
				</div>
			</div>

			<div class="buttons clearfix">
				<div class="pull-left"><a href="<?php echo $link_back; ?>" class="btn btn-default"><?php echo $button_back; ?></a></div>
			</div>
			<?php echo $content_bottom; ?>
		</div>
		<?php echo $column_right; ?>
	</div>
</div>

<script>
	$(function() {
		$('#table-summary-1').dataTable( {
			"aoColumns": [
				{ "sClass": "text-left" },
				{ "sClass": "text-left" },
				{ "sClass": "text-left" },
				{ "sClass": "text-left" }
			],
			"bPaginate": false,
			"bInfo": false,
			"bSort": false,
			"bServerSide": false
		});

		$('#table-by-products').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-stats/getByProductData",
			"aoColumns": [
				{ "mData": "product_id" },
				{ "mData": "product_html", "bSortable": false, "sClass": "text-left" },
				{ "mData": "sold" },
				{ "mData": "total_formatted" }
			]

		});

		window.table_year = $('#table-by-year').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-stats/getByYearData",
			"aoColumns": [
				{ "mData": "date_added", "sClass": "text-left" },
				{ "mData": "order_num", "sClass": "text-left" },
				{ "mData": "total_revenue", "sClass": "text-left" },
				{ "mData": "average_revenue", "sClass": "text-left" }
			],
			"bPaginate": false,
			"bInfo": false,
			"bSort": false,
			"bServerSide": true
		});

		year = $("#year_select").val();
		window.table_year_total = $('#table-by-year-total').dataTable( {
			"sAjaxSource": $('base').attr('href') + "index.php?route=seller/account-stats/getTotalByYear&year=" + year,
			"aoColumns": [
				{ "mData": "total_text", "sClass": "text-left" },
				{ "mData": "order_num", "sClass": "text-left" },
				{ "mData": "total_revenue", "sClass": "text-left" },
				{ "mData": "average_revenue", "sClass": "text-left" }
			],
			"bPaginate": false,
			"bInfo": false,
			"bSort": false,
			"bServerSide": true
		});

	});

	$("#year_select").on("change", function(){
		year = $(this).val();
		window.table_year.fnSettings().sAjaxSource = "index.php?route=seller/account-stats/getByYearData&year=" + year;
		window.table_year.fnClearTable();

		window.table_year_total.fnSettings().sAjaxSource = "index.php?route=seller/account-stats/getTotalByYear&year=" + year;
		window.table_year_total.fnClearTable();

		$.getJSON("index.php?route=seller/account-stats/getSalesByYear&year=" + year, function(data){
			$("#sales_num").text(data.sales);
		});

	});


</script>
<?php echo $footer; ?>