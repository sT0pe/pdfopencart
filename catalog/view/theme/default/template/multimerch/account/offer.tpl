<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <?php if ($attention) { ?>
  <div class="alert alert-info"><i class="fa fa-info-circle"></i> <?php echo $attention; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <?php if ($error_warning) { ?>
  <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
    <button type="button" class="close" data-dismiss="alert">&times;</button>
  </div>
  <?php } ?>
  <div class="row">

    <div id="content" class="col-sm-12"><?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>

      <form id="offer-form" name="offer-form" action="<?php echo $offer_add; ?>" method="post" onsubmit="return validateForm();" enctype="multipart/form-data">
        <input type="hidden" name="offer_id" value="<?php if(isset($offer_info)){ echo $offer_info['offer_id']; } ?>" />
        <div style="background-color: #f5f5f5; border: 1px solid #ddd; margin-bottom: 10px; padding: 20px 0;" class="row">
          <div class="pull-left col-md-6">
            <div class="form-group form-inline">
              <label for="name">Caption:</label>
              <input id="name" name="name" type="text" class="form-control"  value="<?php if(isset($offer_info['offer_name'])){ echo $offer_info['offer_name']; } ?>" />
              <?php if (isset($error_name)) { ?>
                <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline">
              <label for="date_start">Date start:</label>
              <input id="date_start" name="date_start" type="date" class="form-control"  value="<?php if(isset($offer_info['date_start'])){ echo $offer_info['date_start']; } ?>" />
              <?php if (isset($error_date_start)) { ?>
                <div class="text-danger"><?php echo $error_date_start; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline">
              <label for="date_end">Date end:</label>
              <input id="date_end" name="date_end" type="date" class="form-control"  value="<?php if(isset($offer_info['date_end'])){ echo $offer_info['date_end']; } ?>" />
              <?php if (isset($error_date_end)) { ?>
                <div class="text-danger"><?php echo $error_date_end; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline">
              <label for="delivery">Delivery:</label>
              <input id="delivery" name="delivery" type="number" step="any" value="<?php if(isset($offer_info['delivery_cost'])){ echo $offer_info['delivery_cost']; } else { echo 0; } ?>" class="form-control" />
            </div>
            <div class="form-group form-inline">
              <label for="prepayment">Prepayment:</label>
              <input id="prepayment" type="number" step="any" name="prepayment"  class="form-control" value="<?php if(isset($offer_info['prepayment'])){ echo $offer_info['prepayment']; } else { echo 0; } ?>" />
            </div>
            <div class="form-group form-inline">
              <label for="service_cost">Product service:</label>
              <input id="service_cost" name="service_cost" type="number" step="any" class="form-control" value="<?php if(isset($offer_info['service_cost'])){ echo $offer_info['service_cost']; } else { echo 0; } ?>" />
            </div>
            <div class="form-group form-inline">
              <input id="work_cost" name="work_cost" type="checkbox" <?php if(isset($offer_info['show_work_cost']) && $offer_info['show_work_cost'] == 1 ){ echo 'checked'; } ?>/>
              <label for="work_cost">Show the cost of work</label>
            </div>
          </div>
          <div class="col-md-6 form-group">
            <button type="submit" name="submit" class="btn btn-primary" value="save">Save offer</button><br/><br/>
            <a id="add-to-cart" class="btn btn-primary">Add to cart</a><br/><br/>
            <button type="submit" name="submit" class="btn btn-primary" value="new">Save as new</button><br/><br/>
            <button type="submit" name="submit" class="btn btn-primary" value="pdf">Save and create pdf</button><br/><br/>
            <button type="submit" name="submit" class="btn btn-primary" value="mail">Send by mail</button><br/><br/>
          </div>
      </div>

      <div style="background-color: #f5f5f5; border: 1px solid #ddd; margin-bottom: 10px; padding: 20px 0;" class="row">
        <div class="col-md-6" style="border-right: 1px solid #ddd;">
          <fieldset class="control-inline">
            <legend>Issued by</legend>
            <div class="form-group">
              <label class="col-sm-2 control-label">Image</label>
              <div class="col-sm-10">
                <div id="offer_by_image">
                  <div class="ms-image <?php if (empty($offer_info['offer_by_image'])) { ?>hidden<?php } ?>">
                    <input type="hidden" name="by_image" value="<?php if(isset($offer_info['offer_by_image'])) { echo $offer_info['offer_by_image']; } ?>" />
                    <img src="<?php if(isset($offer_info['offer_by_image'])) { echo $offer_info['offer_by_image']; } ?>" />
                    <span class="ms-remove"><i class="fa fa-times"></i></span>
                  </div>

                  <div class="dragndropmini <?php if (!empty($offer_info['offer_by_image'])) { ?>hidden<?php } ?>" id="by_image"><p class="mm_drophere">Drop image here or click to upload</p></div>
                  <p class="ms-note">Select your logo (displayed in your invoices)</p>
                  <div class="alert alert-danger" style="display: none;"></div>
                  <div class="ms-progress progress"></div>
                </div>
              </div>
            </div>

            <div class="form-group form-inline text-center">
              <label for="by_name">Name:</label>
              <input type="text" name="by_name" id="by_name" value="<?php if(isset($offer_info['offer_by_name'])){ echo $offer_info['offer_by_name']; } ?>" class="form-control"/>
              <?php if (isset($error_by_name)) { ?>
                <div class="text-danger"><?php echo $error_by_name; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline text-center">
              <label for="by_nip">NIP:</label>
              <input type="number" name="by_nip" id="by_nip" value="<?php if(isset($offer_info['offer_by_nip'])){ echo $offer_info['offer_by_nip']; } ?>" class="form-control"/>
              <?php if (isset($error_by_nip)) { ?>
                <div class="text-danger"><?php echo $error_by_nip; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline text-center">
              <label for="by_address">Address:</label>
              <input type="text" name="by_address" id="by_address" value="<?php if(isset($offer_info['offer_by_address'])){ echo $offer_info['offer_by_address']; } ?>" class="form-control"/>
              <?php if (isset($error_by_address)) { ?>
                <div class="text-danger"><?php echo $error_by_address; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline text-center">
              <label for="by_phone">Phone:</label>
              <input type="text" name="by_phone" id="by_phone" value="<?php if(isset($offer_info['offer_by_phone'])){ echo $offer_info['offer_by_phone']; } ?>" class="form-control"/>
              <?php if (isset($error_by_phone)) { ?>
                <div class="text-danger"><?php echo $error_by_phone; ?></div>
              <?php } ?>
            </div>
          </fieldset>
        </div>

        <div class="col-md-6">
          <fieldset class="control-inline">
            <legend>Offer for </legend>
            <div class="form-group">
              <label class="col-sm-2 control-label">Image</label>
              <div class="col-sm-10">
                <div id="offer_for_image">
                  <div class="ms-image <?php if (empty($offer_info['offer_for_image'])) { ?>hidden<?php } ?>">
                    <input type="hidden" name="for_image" value="<?php if(isset($offer_info['offer_for_image'])) { echo $offer_info['offer_for_image']; } ?>" />
                    <img src="<?php echo $settings['slr_thumb']; ?>" />
                    <span class="ms-remove"><i class="fa fa-times"></i></span>
                  </div>

                  <div class="dragndropmini <?php if (!empty($offer_info['offer_for_image'])) { ?>hidden<?php } ?>" id="for_image"><p class="mm_drophere">Drop image here or click to upload</p></div>
                  <p class="ms-note">Select your logo (displayed in your invoices)</p>
                  <div class="alert alert-danger" style="display: none;"></div>
                  <div class="ms-progress progress"></div>
                </div>
              </div>
            </div>
            <div class="form-group form-inline text-center">
              <label for="for_name">Name:</label>
              <input type="text" name="for_name" id="for_name" value="<?php if(isset($offer_info['offer_for_name'])){ echo $offer_info['offer_for_name']; } ?>" class="form-control"/>
              <?php if (isset($error_for_name)) { ?>
                <div class="text-danger"><?php echo $error_for_name; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline text-center">
              <label for="for_nip">NIP:</label>
              <input type="number" name="for_nip" id="for_nip" value="<?php if(isset($offer_info['offer_for_nip'])){ echo $offer_info['offer_for_nip']; } ?>" class="form-control"/>
              <?php if (isset($error_for_nip)) { ?>
                <div class="text-danger"><?php echo $error_for_nip; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline text-center">
              <label for="for_address">Address:</label>
              <input type="text" name="for_address" id="for_address" value="<?php if(isset($offer_info['offer_for_address'])){ echo $offer_info['offer_for_address']; } ?>" class="form-control"/>
              <?php if (isset($error_for_address)) { ?>
                <div class="text-danger"><?php echo $error_for_address; ?></div>
              <?php } ?>
            </div>
            <div class="form-group form-inline text-center">
              <label for="for_phone">Phone:</label>
              <input type="text" name="for_phone" id="for_phone" value="<?php if(isset($offer_info['offer_for_phone'])){ echo $offer_info['offer_for_phone']; } ?>" class="form-control"/>
              <?php if (isset($error_for_phone)) { ?>
                <div class="text-danger"><?php echo $error_for_phone; ?></div>
              <?php } ?>
            </div>
          </fieldset>
        </div>
      </div>

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <td></td>
                <td class="text-center">#</td>
                <td class="text-center"><?php echo $column_image; ?></td>
                <td class="text-left"><?php echo $column_name; ?></td>
                <td class="text-left"><?php echo $column_model; ?></td>
                <td class="text-right">Purchase price<br/>ex. tax</td>
                <td class="text-center">Retail price<br/> ex. tax</td>
                <td class="text-center">Discount, %</td>
                <td class="text-center">Price for client<br/> ex. tax</td>
                <td class="text-center">Tax, %</td>
                <td class="text-center">Price for client<br/> incl. tax</td>
                <td class="text-left"><?php echo $column_quantity; ?></td>
                <td class="text-right"><?php echo $column_total; ?></td>
                <td></td>
              </tr>
            </thead>
            <tbody id="offer-products">
              <?php $i=1; foreach ($products as $product) { ?>
              <tr>
                <td class="text-center"><input name="remove-row" type="checkbox"/></td>
                <td class="text-center">
                  <?php echo $i; ?>
                  <input type="hidden" name="product_id[]" value="<?php echo $product['product_id']; ?>"/>
                </td>
                <td class="text-center"><?php if ($product['thumb']) { ?>
                  <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" title="<?php echo $product['name']; ?>" class="img-thumbnail" /></a>
                  <?php } ?></td>
                <td class="text-left"><a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                  <?php if (!$product['stock']) { ?>
                  <span class="text-danger">***</span>
                  <?php } ?>
                  <?php if ($product['option']) { ?>
                  <?php foreach ($product['option'] as $option) { ?>
                  <br />
                  <small><?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                  <?php } ?>
                  <?php } ?>
                  <?php if ($product['reward']) { ?>
                  <br />
                  <small><?php echo $product['reward']; ?></small>
                  <?php } ?>
                  <?php if ($product['recurring']) { ?>
                  <br />
                  <span class="label label-info"><?php echo $text_recurring_item; ?></span> <small><?php echo $product['recurring']; ?></small>
                  <?php } ?></td>
                <td class="text-left"><?php echo $product['model']; ?></td>
                <td class="text-right"><?php echo $product['price']; ?></td>
                <td class="text-center"><input id="retail-price-<?php echo $product['product_id']; ?>" name="retail_price[<?php echo $product['product_id']; ?>]" value="<?php echo isset($product['retail_price']) ? $product['retail_price'] : sprintf('%.2f', $product['unit_price']); ?>" type="text" min="0" step="any" class="form-control" onchange="newRetailPrice(<?php echo $product['product_id']; ?>);" /></td>
                <td class="text-center"><input id="discount-<?php echo $product['product_id']; ?>" name="discount[<?php echo $product['product_id']; ?>]" value="<?php echo isset($product['discount']) ? $product['discount'] : 0; ?>" type="number" min="0" max="100" step="any" class="form-control" onchange="newRetailPrice(<?php echo $product['product_id']; ?>);" /></td>
                <td class="text-center"><input id="seller-price-<?php echo $product['product_id']; ?>" name="seller_price[<?php echo $product['product_id']; ?>]" value="<?php echo isset($product['price_ex_tax']) ? $product['price_ex_tax'] : sprintf('%.2f', $product['unit_price']); ?>" type="number" min="0" step="any" class="form-control" onchange="newSellerPrice(<?php echo $product['product_id']; ?>);" /></td>
                <td class="text-center"><span id="tax-<?php echo $product['product_id']; ?>">20</span></td>
                <td class="text-center"><input id="final-price-<?php echo $product['product_id']; ?>" name="final_price[<?php echo $product['product_id']; ?>]" type="number" step="any" class="form-control" value="<?php echo isset($product['price_inc_tax']) ? $product['price_inc_tax'] : sprintf('%.2f', $product['unit_price']); ?>" onchange="newFinalPrice(<?php echo $product['product_id']; ?>)" /></td>
                <td class="text-left">
                    <input id="quantity-<?php echo $product['product_id']; ?>" name="quantity[<?php echo $product['product_id']; ?>]" value="<?php echo $product['quantity']; ?>" type="number" step="any" min="1" onchange="newQuantity(<?php echo $product['product_id']; ?>);" size="1" class="form-control quantity" />
                </td>
                <td class="text-right"><span id="total-<?php echo $product['product_id']; ?>"><?php echo $product['total'] ?></span></td>
                <td class="text-center text-danger">
                  <a class="btn btn-danger" onclick="removeProduct(this);"><i class="fa fa-times-circle"></i></a>
                </td>
              </tr>
              <?php  $i++; } ?>
            </tbody>
          </table>
        </div>
          <a class="btn btn-default" onclick="removeProducts()">Remove selected</a>

      <br />
      <div class="row">
        <div class="col-sm-4 col-sm-offset-8">
          <table class="table table-bordered">
            <thead>
            <tr>
              <td class="text-center">Total</td>
              <td class="text-center">Netto</td>
              <td class="text-center">Brutto</td>
            </tr>
            </thead>
            <tbody>
            <tr>
              <td>Total for seller:</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Total for client:</td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <td>Your profit:</td>
              <td></td>
              <td></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="row">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">Add products</a></li>
          <li><a data-toggle="tab" href="#tab2">Tab 2</a></li>
          <li><a data-toggle="tab" href="#tab3">Tab 3</a></li>
        </ul>

        <div class="tab-content">
          <div id="home" class="tab-pane fade in active">
             <table class="table table-bordered">
               <tr>
                 <td class="col-md-10">Find products:</td>
               </tr>
               <tr>
                 <td>
                   <div id="search-product">
                     <input type="text" autocomplete="off" name="search" placeholder="Search" class="form-control input-lg" />
                   </div>
                 </td>
               </tr>
             </table>
          </div>
          <div id="tab2" class="tab-pane fade">
            <p>Some content in tab 2.</p>
          </div>
          <div id="tab3" class="tab-pane fade">
            <p>Some content in tab 3.</p>
          </div>
        </div>
      </div>

      <div style="background-color: #f5f5f5; border: 1px solid #ddd; margin-bottom: 10px; padding: 20px 10px 20px;" class="row text-right">
          <div class="form-inline">
            <button type="submit" name="submit" class="btn btn-primary" value="save">Save offer</button>
            <a id="add-to-cart" class="btn btn-primary">Add to cart</a>
            <button type="submit" name="submit" class="btn btn-primary" value="new">Save as new</button>
            <button type="submit" name="submit" class="btn btn-primary" value="pdf">Save and create pdf</button>
            <button type="submit" name="submit" class="btn btn-primary" value="mail">Send by mail</button>
          </div>
      </div>
      </form>

      <script type="text/javascript">

      $('#add-to-cart').on('click', function() {
          $.ajax({
              url: 'index.php?route=multimerch/account_offer/addToCart',
              type: 'post',
              data: $('#offer-form').serialize(),
              dataType: 'json',

              success: function(json) {
                  $('.alert').remove();

                  if (json['error']) {
                      $('.breadcrumb').after('<div class="alert alert-danger">' + json['error'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                      $('html, body').animate({ scrollTop: 0 }, 'slow');
                  }

                  if (json['success']) {
                      $('.breadcrumb').after('<div class="alert alert-success">' + json['success'] + '<button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                      $('#cart > button').html('<span id="cart-total"><i class="fa fa-shopping-cart"></i> ' + json['total'] + '</span>');

                      $('html, body').animate({ scrollTop: 0 }, 'slow');

                      $('#cart > ul').load('index.php?route=common/cart/info ul li');
                  }
              }
          });
      });

      function newQuantity(id) {
          var quantity = Number($("#quantity-" + id).val());
          var final    = Number($("#final-price-" + id).val());

          $("#total-" + id).html((quantity * final).toFixed(2));
      }

      function newRetailPrice(id) {
        var retail   = Number($("#retail-price-" + id).val());
        var discount = Number($("#discount-" + id).val());
        var tax      = Number($("#tax-" + id).html());

        var seller = retail - retail * discount/100;
        var final  = seller + seller * tax / 100;

        $("#seller-price-" + id).val(seller.toFixed(2));
        $("#final-price-" + id).val(final.toFixed(2));
        newQuantity(id);
      }

      function newSellerPrice(id) {
          var seller   = Number($("#seller-price-" + id).val());
          var discount = Number($("#discount-" + id).val());
          var tax      = Number($("#tax-" + id).html());

          var retail   = seller * 100 / ( 100 - discount ) ;
          var final  = seller + seller * tax / 100;

          $("#retail-price-" + id).val(retail.toFixed(2));
          $("#final-price-" + id).val(final.toFixed(2));
          newQuantity(id);
      }

      function newFinalPrice(id) {
          var final    = Number($("#final-price-" + id).val());
          var discount = Number($("#discount-" + id).val());
          var tax      = Number($("#tax-" + id).html());

          var seller = final * 100 / ( 100 + tax );
          var retail   = seller * 100 / ( 100 - discount ) ;

          $("#seller-price-" + id).val(seller.toFixed(2));
          $("#retail-price-" + id).val(retail.toFixed(2));
          newQuantity(id);
      }

      function removeProduct(e) {
          e.parentNode.parentNode.parentNode.removeChild(e.parentNode.parentNode);
      }

      function removeProducts() {
          $( "input[name=remove-row]:checked" ).each( function () {
              this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);
          });
      }


      </script>

      <?php echo $content_bottom; ?></div>
   </div>
</div>

<?php echo $footer; ?>
