<div class="container">
<hr/>
<h3>New Offer <?php echo $offer_info['date_start']; ?></h3>
<div class="row">
  <table style="margin-bottom: 50px">
    <tr>
      <td>Date start:</td>
      <td><?php echo $offer_info['date_start']; ?></td>
    </tr>
    <tr>
      <td>Date end:</td>
      <td><?php echo $offer_info['date_end']; ?></td>
    </tr>
    <tr>
      <td>Delivery cost:</td>
      <td><?php echo $offer_info['delivery_cost']; ?></td>
    </tr>
    <tr>
      <td>Prepayment:</td>
      <td><?php echo $offer_info['prepayment']; ?></td>
    </tr>
  </table>
</div>

<div class="row">
  <table class="col-md-6">
    <tr>
      <td>Offer for</td>
      <td><img src="<?php echo $offer_info['offer_for_image']; ?>"/></td>
    </tr>
    <tr>
      <td><?php echo $offer_info['offer_for_name']; ?></td>
    </tr>
    <tr>
      <td>NIP: <?php echo $offer_info['offer_for_nip']; ?></td>
    </tr>
    <tr>
      <td><?php echo $offer_info['offer_for_address']; ?></td>
    </tr>
    <tr>
      <td>tel. <?php echo $offer_info['offer_for_phone']; ?></td>
    </tr>
  </table>
  <table class="col-md-6">
    <tr>
      <td>Offer by</td>
      <td><img src="<?php echo $offer_info['offer_by_image']; ?>"/></td>
    </tr>
    <tr>
      <td><?php echo $offer_info['offer_by_name']; ?></td>
    </tr>
    <tr>
      <td>NIP: <?php echo $offer_info['offer_by_nip']; ?></td>
    </tr>
    <tr>
      <td><?php echo $offer_info['offer_by_address']; ?></td>
    </tr>
    <tr>
      <td>tel. <?php echo $offer_info['offer_by_phone']; ?></td>
    </tr>
  </table>
</div>
<hr/>

  <h3>Products</h3>
  <table>
    <thead>
      <tr>
        <td class="text-center">#</td>
        <td class="text-center">Image</td>
        <td class="text-left">Name</td>
        <td class="text-left">Model</td>
        <td class="text-center">Price netto</td>
        <td class="text-center">Tax%</td>
        <td class="text-center">Price brutto</td>
        <td class="text-left">Quantity</td>
        <td class="text-right">Total</td>
      </tr>
    </thead>
    <tbody>
    <?php $i=1; foreach($result as $product) { ?>
      <tr>
        <td class="text-center"><?php echo $i; ?></td>
        <td class="text-center"><img src="<?php echo $product['image']?>" /></td>
        <td class="text-left"><?php echo $product['name']?></td>
        <td class="text-left"><?php echo $product['model']?></td>
        <td class="text-center"><?php echo $product['price_netto']?></td>
        <td class="text-center">23</td>
        <td class="text-center"><?php echo $product['price_brutto']?></td>
        <td class="text-left"><?php echo $product['quantity']?></td>
        <td class="text-right"><?php echo $product['total']?></td>
      </tr>
    <?php $i++; } ?>
    </tbody>
  </table>

</div>

