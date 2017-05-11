<?php
$products = '';
foreach ($this->order_products as $p) {
    if ($p['quantity'] > 1) $products .= "{$p['quantity']} x ";
    $products .= $p['name'] . '<br>';
}

?>
<p>
    <?php echo nl2br(sprintf($this->translate('ms_mail_order_updated'), $this->sender, $this->seller_nickname, $this->order_id, $products, $this->order_status, $this->order_comment)) ?>
</p>
