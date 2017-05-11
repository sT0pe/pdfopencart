<?php echo $header; ?>
<div class="container">

    <?php if (isset($success) && $success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?></div>
    <?php } ?>

    <div class="alert alert-danger" id="error-holder" style="display: none;"></div>

    <div class="row"><?php echo $column_left; ?>
        <?php if ($column_left && $column_right) { ?>
        <?php $class = 'col-sm-6'; ?>
        <?php } elseif ($column_left || $column_right) { ?>
        <?php $class = 'col-sm-9'; ?>
        <?php } else { ?>
        <?php $class = 'col-sm-12'; ?>
        <?php } ?>
        <div id="content" class="<?php echo $class; ?> ms-account-dashboard"><?php echo $content_top; ?>
            <div class="mm_dashboard">
                <h1 class="pull-left"><i class="fa fa-cog"></i><?php echo $ms_account_attribute_manage; ?></h1>

                <div class="pull-right">
                    <ul class="nav nav-tabs ms-attributes-topbar">
                        <li class="active"><a href="#tab-attribute" data-toggle="tab"><?php echo $ms_account_attributes; ?></a></li>
                        <li><a href="#tab-attribute-group" data-toggle="tab"><?php echo $ms_account_attribute_groups; ?></a></li>
                    </ul>
                </div>

                <div class="tab-content">
                    <div id="tab-attribute" class="tab-pane active">
                        <div class="cl"></div>
                        <a href="<?php echo $this->url->link('seller/account-attribute/createAttribute', '', 'SSL'); ?>" class="btn btn-primary"><span><?php echo $ms_account_attribute_new; ?></span></a>

                        <div class="table-responsive">
                            <table class="mm_dashboard_table table table-borderless table-hover" id="list-attributes">
                                <thead>
                                    <tr>
                                        <td class="mm_size_medium"><?php echo $ms_account_attribute; ?></td>
                                        <td class="mm_size_medium"><?php echo $ms_account_attribute_group; ?></td>
                                        <td class="mm_size_small"><?php echo $ms_status; ?></td>
                                        <td class="mm_size_small"><?php echo $ms_sort_order; ?></td>
                                        <td class="mm_size_medium"><?php echo $ms_action; ?></td>
                                    </tr>

                                    <tr class="filter">
                                        <td><input type="text"/></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>

                    <div id="tab-attribute-group" class="tab-pane">
                        <div class="cl"></div>
                        <a href="<?php echo $this->url->link('seller/account-attribute/createAttributeGroup', '', 'SSL'); ?>" class="btn btn-primary"><span><?php echo $ms_account_attribute_group_new; ?></span></a>

                        <div class="table-responsive">
                            <table class="mm_dashboard_table table table-borderless table-hover" id="list-attribute-groups">
                                <thead>
                                    <tr>
                                        <td class="mm_size_medium"><?php echo $ms_account_attribute_group; ?></td>
                                        <td class="mm_size_small"><?php echo $ms_status; ?></td>
                                        <td class="mm_size_small"><?php echo $ms_sort_order; ?></td>
                                        <td class="mm_size_medium"><?php echo $ms_action; ?></td>
                                    </tr>

                                    <tr class="filter">
                                        <td><input type="text"/></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <?php echo $content_bottom; ?>
        </div>
        <?php echo $column_right; ?>
    </div>
</div>
<?php echo $footer; ?>