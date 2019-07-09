<?php
namespace App\Library\Pay\CoinPayments; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; private $pay_id; public function __construct($spe00284) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $spe00284; $this->url_return = SYS_URL . '/pay/return/' . $spe00284; $this->pay_id = $spe00284; } function goPay($spc27de0, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { $sp9b2bb6 = sprintf('%.2f', $sp6956b3 / 100); if (!isset($spc27de0['merchant_id'])) { throw new \Exception('请填写 merchant_id (商家ID)'); } if (!isset($spc27de0['ipn_secret'])) { throw new \Exception('请填写 ipn_secret (IPN密钥)'); } die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="alert(\'如果您已经支付过，请勿重复支付，订单处理需要一定的时间请等待。\');document.pay.submit()">
<form name="pay" action="https://www.coinpayments.net/index.php" method="post">
    <input type="hidden" name="cmd" value="_pay_auto">
    <input type="hidden" name="merchant" value="' . $spc27de0['merchant_id'] . '">
    <input type="hidden" name="reset" value="1">
    <input type="hidden" name="currency" value="CNY">
    <input type="hidden" name="amountf" value="' . $sp9b2bb6 . '">
    <input type="hidden" name="item_name" value="' . $sp9f49de . '">
    <input type="hidden" name="item_number" value="' . $spba04f6 . '">
    <input type="hidden" name="invoice" value="' . $spba04f6 . '">
    <input type="hidden" name="want_shipping" value="0">
    <input type="hidden" name="ipn_url" value="' . $this->url_notify . '">
    <input type="hidden" name="success_url" value="' . $this->url_return . '">
    <input type="hidden" name="cancel_url" value="' . $this->url_return . '">
</form>
</body>
        '); } function verify($spc27de0, $sp4294a3) { $spb2acff = isset($spc27de0['isNotify']) && $spc27de0['isNotify']; if (!$spb2acff) { return false; } function errorAndDie($spfd84bb) { Log::error('Pay.CoinPayments.verify error: ' . $spfd84bb); die('IPN Error: ' . $spfd84bb); } if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') { errorAndDie('IPN Mode is not HMAC'); } if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) { errorAndDie('No HMAC signature sent.'); } $spd5cc4d = file_get_contents('php://input'); if ($spd5cc4d === FALSE || empty($spd5cc4d)) { errorAndDie('Error reading POST data'); } if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($spc27de0['merchant_id'])) { errorAndDie('No or incorrect Merchant ID passed'); } $sp2d2369 = hash_hmac('sha512', $spd5cc4d, trim($spc27de0['ipn_secret'])); if (!hash_equals($sp2d2369, $_SERVER['HTTP_HMAC'])) { errorAndDie('HMAC signature does not match'); } $sp29d79c = floatval($_POST['amount1']); $spc13fdd = floatval($_POST['amount2']); $spbe80af = $_POST['currency1']; $sp1e772c = $_POST['currency2']; $spc3ee59 = intval($_POST['status']); $sp8953e9 = $_POST['status_text']; $spd56469 = $_POST['item_number']; $sp36f78e = (int) round($sp29d79c * 100); $spaa9795 = $_POST['txn_id']; if ($spbe80af !== 'CNY') { errorAndDie('Pay.CoinPayments.verify error, order_no:' . $spd56469 . ', Original currency(CNY) mismatch, current(' . $spbe80af . ')!'); } if ($spc3ee59 >= 100 || $spc3ee59 == 2) { $sp4294a3($spd56469, $sp36f78e, $spaa9795); } else { if ($spc3ee59 < 0) { Log::debug('Pay.CoinPayments.verify info: payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent', array('$_POST' => $_POST)); } else { Log::debug('Pay.CoinPayments.verify info: payment is pending, you can optionally add a note to the order page', array('$_POST' => $_POST)); } } die('IPN OK'); } }