<?php
namespace App\Library\Pay\CoinPayments; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; private $pay_id; public function __construct($sp53f8aa) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp53f8aa; $this->url_return = SYS_URL . '/pay/return/' . $sp53f8aa; $this->pay_id = $sp53f8aa; } function goPay($spbe80b7, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp429fcc = sprintf('%.2f', $sp5213ee / 100); if (!isset($spbe80b7['merchant_id'])) { throw new \Exception('请填写 merchant_id (商家ID)'); } if (!isset($spbe80b7['ipn_secret'])) { throw new \Exception('请填写 ipn_secret (IPN密钥)'); } die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="alert(\'如果您已经支付过，请勿重复支付，订单处理需要一定的时间请等待。\');document.pay.submit()">
<form name="pay" action="https://www.coinpayments.net/index.php" method="post">
    <input type="hidden" name="cmd" value="_pay_auto">
    <input type="hidden" name="merchant" value="' . $spbe80b7['merchant_id'] . '">
    <input type="hidden" name="reset" value="1">
    <input type="hidden" name="currency" value="CNY">
    <input type="hidden" name="amountf" value="' . $sp429fcc . '">
    <input type="hidden" name="item_name" value="' . $sp45f07e . '">
    <input type="hidden" name="item_number" value="' . $spa3e681 . '">
    <input type="hidden" name="invoice" value="' . $spa3e681 . '">
    <input type="hidden" name="want_shipping" value="0">
    <input type="hidden" name="ipn_url" value="' . $this->url_notify . '">
    <input type="hidden" name="success_url" value="' . $this->url_return . '">
    <input type="hidden" name="cancel_url" value="' . $this->url_return . '">
</form>
</body>
        '); } function verify($spbe80b7, $sp04f0f8) { $sp3bce01 = isset($spbe80b7['isNotify']) && $spbe80b7['isNotify']; if (!$sp3bce01) { return false; } function errorAndDie($spfcb0d7) { Log::error('Pay.CoinPayments.verify error: ' . $spfcb0d7); die('IPN Error: ' . $spfcb0d7); } if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') { errorAndDie('IPN Mode is not HMAC'); } if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) { errorAndDie('No HMAC signature sent.'); } $spfeab54 = file_get_contents('php://input'); if ($spfeab54 === FALSE || empty($spfeab54)) { errorAndDie('Error reading POST data'); } if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($spbe80b7['merchant_id'])) { errorAndDie('No or incorrect Merchant ID passed'); } $sp6b709f = hash_hmac('sha512', $spfeab54, trim($spbe80b7['ipn_secret'])); if (!hash_equals($sp6b709f, $_SERVER['HTTP_HMAC'])) { errorAndDie('HMAC signature does not match'); } $spe8ca11 = floatval($_POST['amount1']); $sp51774d = floatval($_POST['amount2']); $spae00c7 = $_POST['currency1']; $sp5079c6 = $_POST['currency2']; $sp7a9982 = intval($_POST['status']); $sp3d2dc7 = $_POST['status_text']; $sp7c88f3 = $_POST['item_number']; $sp9624ba = (int) round($spe8ca11 * 100); $spe02e4c = $_POST['txn_id']; if ($spae00c7 !== 'CNY') { errorAndDie('Pay.CoinPayments.verify error, order_no:' . $sp7c88f3 . ', Original currency(CNY) mismatch, current(' . $spae00c7 . ')!'); } if ($sp7a9982 >= 100 || $sp7a9982 == 2) { $sp04f0f8($sp7c88f3, $sp9624ba, $spe02e4c); } else { if ($sp7a9982 < 0) { Log::debug('Pay.CoinPayments.verify info: payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent', array('$_POST' => $_POST)); } else { Log::debug('Pay.CoinPayments.verify info: payment is pending, you can optionally add a note to the order page', array('$_POST' => $_POST)); } } die('IPN OK'); } }