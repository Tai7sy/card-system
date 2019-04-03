<?php
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>支付宝即时到账交易接口接口</title>
</head>
<?php  require_once 'alipay.config.php'; require_once 'lib/alipay_submit.class.php'; $sp30c318 = $_POST['WIDout_trade_no']; $spc3fe8b = $_POST['WIDsubject']; $sp51db0b = $_POST['WIDtotal_fee']; $sp66faf9 = $_POST['WIDbody']; $spf88b18 = array('service' => $sp6e4d76['service'], 'partner' => $sp6e4d76['partner'], 'seller_id' => $sp6e4d76['seller_id'], 'payment_type' => $sp6e4d76['payment_type'], 'notify_url' => $sp6e4d76['notify_url'], 'return_url' => $sp6e4d76['return_url'], 'anti_phishing_key' => $sp6e4d76['anti_phishing_key'], 'exter_invoke_ip' => $sp6e4d76['exter_invoke_ip'], 'out_trade_no' => $sp30c318, 'subject' => $spc3fe8b, 'total_fee' => $sp51db0b, 'body' => $sp66faf9, '_input_charset' => trim(strtolower($sp6e4d76['input_charset']))); $spac156f = new AlipaySubmit($sp6e4d76); $spd3fa86 = $spac156f->buildRequestForm($spf88b18, 'get', '确认'); echo $spd3fa86; ?>
</body>
</html><?php 