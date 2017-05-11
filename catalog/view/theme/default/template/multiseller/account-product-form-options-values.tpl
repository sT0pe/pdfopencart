<div class="row mm_bg_header">
	<label class="mm_label no_padding no_margin col-sm-1"><b><?php echo $option['name']; ?></b></label>
	<div class="mm_form col-sm-11 no_margin no_padding">
		<label class="mm_label no_padding no_margin">Required?</label>

		<input type="checkbox" name="product_option[<?php echo $option_index; ?>][required]" value="1" <?php echo isset($option['required']) && $option['required'] ? "checked='checked'" : ""; ?>/>
		<input type="hidden" name="product_option[<?php echo $option_index; ?>][option_id]" value="<?php echo $option['option_id']; ?>" />

		<div class="mm_remove_option">Remove <a class="icon-remove mm_vtop mm_remove" title="Remove"><i class="fa fa-times"></i></a></div>
	</div>
</div>

<?php if (!empty($values)) { ?>
	<div class="row">


		<div class="col-sm-12 mm_values">
			<table class="table table-borderless table-hover">
				<thead>
				<tr>
					<td>Values</td>
					<td class="mm_size_tiny"><?php echo $ms_options_quantity; ?></td>
					<td class="mm_size_tiny"><?php echo $ms_options_subtract; ?></td>
					<td><?php echo $ms_options_price; ?></td>
					<td class="mm_size_tiny"></td>
				</tr>
				</thead>

				<tbody>
				<tr class="ffSample option_value">
					<td class="option_name">
						<input type="hidden" name="product_option[<?php echo $option_index; ?>][product_option_value][0][option_value_id]" value="">
					</td>

					<td><input class="option_quantity form-control inline" type="text" name="product_option[<?php echo $option_index; ?>][product_option_value][0][quantity]" value="" size="5"></td>

					<td><input type="checkbox" name="product_option[<?php echo $option_index; ?>][product_option_value][0][subtract]" value="1" /></td>

					<td>
						<select name="product_option[<?php echo $option_index; ?>][product_option_value][0][price_prefix]" class="form-control inline mm_size_small">
							<option value="+">+</option>
							<option value="-">-</option>
						</select>

						<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
						<input type="text" class="form-control inline mm_size_small mm_price" placeholder="<?php echo $ms_options_price; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][0][price]" value="" size="5">
						<?php echo $this->currency->getSymbolRight(); ?>
					</td>

					<td><a class="icon-remove mm_vtop mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
				</tr>

				<?php if (!empty($product_option_values)) { ?>
					<?php $i = 1; ?>
					<?php foreach ($product_option_values as $value) { ?>
						<tr class="option_value">
							<td class="option_name">
								<input type="hidden" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][option_value_id]" value="<?php echo $value['option_value_id']; ?>">
								<?php echo $value['name']; ?>
							</td>

							<td><input class="option_quantity form-control inline" type="text" placeholder="<?php echo $ms_options_quantity; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][quantity]" value="<?php echo $value['quantity']; ?>" size="5"></td>

							<td><input type="checkbox" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][subtract]" value="1" <?php echo ($value['subtract'] ? 'checked="checked"' : '') ?>/></td>

							<td>
								<select name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][price_prefix]" class="form-control inline mm_size_small">
									<option value="+" <?php echo ($value['price_prefix'] == '+' ? 'selected' : '') ?>>+</option>
									<option value="-" <?php echo ($value['price_prefix'] == '-' ? 'selected' : '') ?>>-</option>
								</select>

								<span class="mm_currency"><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
								<input type="text" class="form-control inline mm_size_small mm_price" placeholder="<?php echo $ms_options_price; ?>" name="product_option[<?php echo $option_index; ?>][product_option_value][<?php echo $i; ?>][price]" value="<?php echo $this->MsLoader->MsHelper->trueCurrencyFormat($value['price']); ?>" size="5"></td>
							<?php echo $this->currency->getSymbolRight(); ?>
							</td>

							<td><a class="icon-remove mm_vtop mm_remove" title="Delete"><i class="fa fa-times"></i></a></td>
						</tr>
						<?php $i++; ?>
					<?php } ?>
				<?php } ?>
				</tbody>
			</table>
			<div class="control-inline">
				<select class="select_option_value form-control form-control-inline" id="select_option_value<?php echo $option['option_id']; ?>">
					<option value="0" disabled="disabled" selected="selected"><?php echo $ms_options_add_value; ?></option>
					<?php foreach($values as $value) { ?>
						<option value="<?php echo $value['option_value_id']?>"><?php echo $value['name']; ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	</div>
<?php } ?>