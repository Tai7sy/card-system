<?php
namespace App\Library\Pay\CoinPayments; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; private $pay_id; public function __construct($sp3c46ab) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp3c46ab; $this->url_return = SYS_URL . '/pay/return/' . $sp3c46ab; $this->pay_id = $sp3c46ab; } function goPay($sp9d4382, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spf59c91 = sprintf('%.2f', $sp076ec7 / 100); if (!isset($sp9d4382['merchant_id'])) { throw new \Exception('请填写 merchant_id (商家ID)'); } if (!isset($sp9d4382['ipn_secret'])) { throw new \Exception('请填写 ipn_secret (IPN密钥)'); } die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="alert(\'如果您已经支付过，请勿重复支付，订单处理需要一定的时间请等待。\');document.pay.submit()">
<form name="pay" action="https://www.coinpayments.net/index.php" method="post">
    <input type="hidden" name="cmd" value="_pay_auto">
    <input type="hidden" name="merchant" value="' . $sp9d4382['merchant_id'] . '">
    <input type="hidden" name="reset" value="1">
    <input type="hidden" name="currency" value="CNY">
    <input type="hidden" name="amountf" value="' . $spf59c91 . '">
    <input type="hidden" name="item_name" value="' . $spd4e90d . '">
    <input type="hidden" name="item_number" value="' . $sp2e47fc . '">
    <input type="hidden" name="invoice" value="' . $sp2e47fc . '">
    <input type="hidden" name="want_shipping" value="0">
    <input type="hidden" name="ipn_url" value="' . $this->url_notify . '">
    <input type="hidden" name="success_url" value="' . $this->url_return . '">
    <input type="hidden" name="cancel_url" value="' . $this->url_return . '">
</form>
</body>
        '); } function verify($sp9d4382, $sp9a4d97) { $sp7b2182 = isset($sp9d4382['isNotify']) && $sp9d4382['isNotify']; if (!$sp7b2182) { return false; } function errorAndDie($spd23304) { Log::error('Pay.CoinPayments.verify error: ' . $spd23304); die('IPN Error: ' . $spd23304); } if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') { errorAndDie('IPN Mode is not HMAC'); } if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) { errorAndDie('No HMAC signature sent.'); } $spf066f3 = file_get_contents('php://input'); if ($spf066f3 === FALSE || empty($spf066f3)) { errorAndDie('Error reading POST data'); } if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($sp9d4382['merchant_id'])) { errorAndDie('No or incorrect Merchant ID passed'); } $sp85f068 = hash_hmac('sha512', $spf066f3, trim($sp9d4382['ipn_secret'])); if (!hash_equals($sp85f068, $_SERVER['HTTP_HMAC'])) { errorAndDie('HMAC signature does not match'); } $spcd6699 = floatval($_POST['amount1']); $spcf83ba = floatval($_POST['amount2']); $sp4d094c = $_POST['currency1']; $sp75c7e3 = $_POST['currency2']; $sp27b58d = intval($_POST['status']); $spfb06b7 = $_POST['status_text']; $sp845b45 = $_POST['item_number']; $spc686cf = (int) round($spcd6699 * 100); $sp41f42a = $_POST['txn_id']; if ($sp4d094c !== 'CNY') { errorAndDie('Pay.CoinPayments.verify error, order_no:' . $sp845b45 . ', Original currency(CNY) mismatch, current(' . $sp4d094c . ')!'); } if ($sp27b58d >= 100 || $sp27b58d == 2) { $sp9a4d97($sp845b45, $spc686cf, $sp41f42a); } else { if ($sp27b58d < 0) { Log::debug('Pay.CoinPayments.verify info: payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent', array('$_POST' => $_POST)); } else { Log::debug('Pay.CoinPayments.verify info: payment is pending, you can optionally add a note to the order page', array('$_POST' => $_POST)); } } die('IPN OK'); } }