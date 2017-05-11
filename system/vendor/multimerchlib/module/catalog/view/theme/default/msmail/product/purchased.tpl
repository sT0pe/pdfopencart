<?php
$products = '';
foreach ($this->order_products as $p) {
    if ($p['quantity'] > 1) {
        $products .= "{$p['quantity']} x ";
    }
    $products .= "{$p['name']}\t" . $this->getMultiMerchModule()->formatMoney($p['seller_net_amt']) . "<br>";

    foreach ($p['order_options'] as $option) {
		$value = $option['value'];
		$option['value'] = utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value;
		$products .= "<br>";
        $products .= "- {$option['name']} : {$option['value']}";
    }

    $products .= "<br>";
}
?>
<?php if ($this->getMultiMerchModule()->getConfigOC('msconf_hide_customer_email')): ?>
    <p><?php echo nl2br(sprintf($this->translate('ms_mail_product_purchased_no_email'), $this->sender, $this->order_info['firstname'] . ' ' . $this->order_info['lastname'], $products, $this->total)) ?></p>
<?php else: ?>
    <p><?php echo nl2br(sprintf($this->translate('ms_mail_product_purchased'), $this->sender, $this->order_info['firstname'] . ' ' . $this->order_info['lastname'], $this->order_info['email'], $products, $this->total)) ?></p>
<?php endif; ?>

<?php if ($this->order_info['comment']): ?>
    <p><?php echo nl2br(sprintf($this->translate('ms_mail_product_purchased_comment'), $this->order_info['comment'])); ?></p>
<?php endif; ?>
