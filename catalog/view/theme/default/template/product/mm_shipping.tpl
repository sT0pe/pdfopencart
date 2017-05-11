<?php if($product_is_digital) { ?>
    <h3><?php echo $mm_product_shipping_digital_product; ?></h3>
<?php } else { ?>
    <?php if(!empty($product_shipping)) { ?>
        <div class="shipping-info">
            <h3><?php echo $mm_product_shipping_title; ?></h3>

            <?php if($product_shipping['free_shipping']) { ?>
                <div class="free-shipping">
                    <strong><?php echo $mm_product_shipping_free; ?></strong>
                </div>
            <?php } else { ?>
                <div class="row">
                    <div class="col-sm-6 general-info">
                        <ul class="list-group">
                            <?php if (isset($product_shipping['from_country_name'])) { ?>
                                <li class="list-group-item">
                                    <span><?php echo $mm_product_shipping_from_country; ?>: </span>
                                    <span><?php echo $product_shipping['from_country_name']; ?></span>
                                </li>
                            <?php } ?>
                            <?php if (isset($product_shipping['processing_time'])) { ?>
                                <li class="list-group-item">
                                    <span><?php echo $mm_product_shipping_processing_time; ?>: </span>
                                    <span><?php echo $product_shipping['processing_time']; ?></span>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="col-sm-6"></div>
                </div>

                <?php if(!empty($product_shipping['locations'])) { ?>
                    <div class="row">
                        <div class="col-sm-12 shipping-methods-list table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <td class="col-sm-3"><?php echo $ms_account_product_shipping_locations_to; ?></td>
                                        <td class="col-sm-3"><?php echo $ms_account_product_shipping_locations_company; ?></td>
                                        <td class="col-sm-2"><?php echo $ms_account_product_shipping_locations_delivery_time; ?></td>
                                        <td class="col-sm-2"><?php echo $ms_account_product_shipping_locations_cost; ?></td>
                                        <td class="col-sm-2"><?php echo $ms_account_product_shipping_locations_additional_cost; ?></td>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php foreach ($product_shipping['locations'] as $location) { ?>
                                    <tr>
                                        <td><?php echo $location['to_geo_zone_id'] == 0 ? $ms_account_product_shipping_elsewhere : $location['to_geo_zone_name']; ?></td>
                                        <td><?php echo $location['shipping_method_name']; ?></td>
                                        <td><?php echo isset($location['delivery_time_name']) ? $location['delivery_time_name'] : '&#8210'; ?></td>
                                        <td>
                                            <span><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
                                            <?php echo $location['cost']; ?>
                                            <span><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
                                            <?php echo $location['additional_cost']; ?>
                                            <span><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <p class="ms-note"><?php echo $mm_product_shipping_locations_note; ?></p>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php } else if (!empty($seller_shipping)) { ?>
        <div class="shipping-info">
            <h3><?php echo $mm_product_shipping_title; ?></h3>

            <div class="row">
                <div class="col-sm-6 general-info">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <span><?php echo $ms_from; ?>: </span>
                            <span><?php echo $seller_shipping['from_country_name']; ?></span>
                        </li>
                        <li class="list-group-item">
                            <span><?php echo $mm_product_shipping_processing_time; ?>: </span>
                            <span><?php echo $seller_shipping['processing_time']; ?></span>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-6"></div>
            </div>

            <?php if(!empty($seller_shipping['methods'])) { ?>
                <div class="row">
                    <div class="col-sm-12 shipping-methods-list table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <td class="col-sm-3"><?php echo $ms_account_product_shipping_locations_to; ?></td>
                                    <td class="col-sm-3"><?php echo $ms_account_product_shipping_locations_company; ?></td>
                                    <td class="col-sm-2"><?php echo $ms_account_product_shipping_locations_delivery_time; ?></td>
                                    <td class="col-sm-2"><?php echo sprintf($ms_account_settings_shipping_weight, $this->weight->getUnit($this->config->get('config_weight_class_id'))); ?></td>
                                    <td class="col-sm-2"><?php echo $ms_account_product_shipping_locations_cost_fixed_pwu; ?></td>
                                </tr>
                            </thead>

                            <tbody>
                                <?php foreach ($seller_shipping['methods'] as $method) { ?>
                                    <tr>
                                        <td><?php echo $method['to_geo_zone_name']; ?></td>
                                        <td><?php echo $method['shipping_method_name']; ?></td>
                                        <td><?php echo isset($method['delivery_time_name']) ? $method['delivery_time_name'] : '&#8210'; ?></td>
                                        <td><?php echo sprintf($ms_product_ssm_weight_range_template, $method['weight_from'], $method['weight_to'], $method['weight_class_unit']); ?></td>
                                        <td>
                                            <span><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
                                            <?php echo $method['cost_fixed']; ?>
                                            <span><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
                                            +
                                            <span><?php echo $this->currency->getSymbolLeft($this->config->get('config_currency')); ?></span>
                                            <?php echo $method['cost_pwu']; ?>
                                            <span><?php echo $this->currency->getSymbolRight($this->config->get('config_currency')); ?></span>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <p class="ms-note"><?php echo $mm_product_shipping_locations_note; ?></p>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } else { ?>
        <h3><?php echo $mm_product_shipping_not_specified; ?></h3>
    <?php } ?>
<?php } ?>
