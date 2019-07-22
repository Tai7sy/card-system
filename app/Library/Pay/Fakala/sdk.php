<?php
class fakala { public $gateway; public $uid; public $key; public function __construct($spbccc31, $sp3c46ab, $spf1241f) { $this->gateway = $spbccc31; $this->uid = $sp3c46ab; $this->key = $spf1241f; } function getSignStr($spc0e525) { ksort($spc0e525); $sp92fb01 = ''; foreach ($spc0e525 as $sp17f3a7 => $sp75c248) { if ('sign' !== $sp17f3a7) { $sp92fb01 .= $sp17f3a7 . '=' . $sp75c248 . '&'; } } return $sp92fb01; } function getSign($spc0e525, $spf1241f, &$spbcd452 = false) { $sp92fb01 = self::getSignStr($spc0e525); $sp964415 = md5($sp92fb01 . 'key=' . $spf1241f); if ($spbcd452 !== false) { $spbcd452 = $sp92fb01 . 'sign=' . $sp964415; } return $sp964415; } function goPay($spf9ca0c, $spd4e90d, $sp2e47fc, $spb03602, $spc686cf, $spf83ede, $spd4aa96, $sp400d9a) { $spc0e525 = array('version' => '20190501', 'uid' => (int) $this->uid, 'subject' => $spd4e90d, 'out_trade_no' => $sp2e47fc, 'total_fee' => (int) $spc686cf, 'cost' => (int) $spb03602, 'payway' => $spf9ca0c, 'return_url' => $spd4aa96, 'notify_url' => $sp400d9a, 'attach' => $spf83ede); $spc0e525['sign'] = $this->getSign($spc0e525, $this->key); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $this->gateway . '/api/order" method="post">
    <input type="hidden" name="version" value="' . $spc0e525['version'] . '">
    <input type="hidden" name="uid" value="' . $spc0e525['uid'] . '">
    <input type="hidden" name="subject" value="' . $spc0e525['subject'] . '">
    <input type="hidden" name="out_trade_no" value="' . $spc0e525['out_trade_no'] . '">
    <input type="hidden" name="total_fee" value="' . $spc0e525['total_fee'] . '">
    <input type="hidden" name="cost" value="' . $spc0e525['cost'] . '">
    <input type="hidden" name="payway" value="' . $spc0e525['payway'] . '">
    <input type="hidden" name="return_url" value="' . $spc0e525['return_url'] . '">
    <input type="hidden" name="notify_url" value="' . $spc0e525['notify_url'] . '">
    <input type="hidden" name="attach" value="' . $spc0e525['attach'] . '">
    <input type="hidden" name="sign" value="' . $spc0e525['sign'] . '">
</form>
</body>
        '); } function notify_verify() { $spc0e525 = $_POST; if ($spc0e525['sign'] === $this->getSign($spc0e525, $this->key)) { echo 'success'; return true; } else { echo 'fail'; return false; } } function return_verify() { $spc0e525 = $_GET; if ($spc0e525['sign'] === $this->getSign($spc0e525, $this->key)) { return true; } else { return false; } } function get_order($sp2e47fc) { $spb72f32 = $this->curl_post($this->gateway . '/api/order/query', 'uid=' . $this->uid . '&out_trade_no=' . $sp2e47fc); $spb72f32 = @json_decode($spb72f32, true); if (is_array($spb72f32) && is_array($spb72f32['data']) && isset($spb72f32['data']['order'])) { return $spb72f32['data']['order']; } return array(); } private function curl_post($spd2457c, $sp6fd648) { $sp4f6936 = curl_init($spd2457c); curl_setopt($sp4f6936, CURLOPT_HEADER, 0); curl_setopt($sp4f6936, CURLOPT_RETURNTRANSFER, 1); curl_setopt($sp4f6936, CURLOPT_SSL_VERIFYPEER, true); curl_setopt($sp4f6936, CURLOPT_POST, true); curl_setopt($sp4f6936, CURLOPT_POSTFIELDS, $sp6fd648); $spdc5091 = curl_exec($sp4f6936); curl_close($sp4f6936); return $spdc5091; } }