<?php
class fakala { public $gateway; public $uid; public $key; public function __construct($sp669180, $spe00284, $sp7b7024) { $this->gateway = $sp669180; $this->uid = $spe00284; $this->key = $sp7b7024; } function getSignStr($sp3e77f9) { ksort($sp3e77f9); $sp973e68 = ''; foreach ($sp3e77f9 as $spe3274c => $spd7786b) { if ('sign' !== $spe3274c) { $sp973e68 .= $spe3274c . '=' . $spd7786b . '&'; } } return $sp973e68; } function getSign($sp3e77f9, $sp7b7024, &$sp97657f = false) { $sp973e68 = self::getSignStr($sp3e77f9); $sp75e4cc = md5($sp973e68 . 'key=' . $sp7b7024); if ($sp97657f !== false) { $sp97657f = $sp973e68 . 'sign=' . $sp75e4cc; } return $sp75e4cc; } function goPay($spd53b1c, $sp9f49de, $spba04f6, $sp92a485, $sp36f78e, $spbcd190, $spe6f749, $sp6b0215) { $sp3e77f9 = array('version' => '20190501', 'uid' => (int) $this->uid, 'subject' => $sp9f49de, 'out_trade_no' => $spba04f6, 'total_fee' => (int) $sp36f78e, 'cost' => (int) $sp92a485, 'payway' => $spd53b1c, 'return_url' => $spe6f749, 'notify_url' => $sp6b0215, 'attach' => $spbcd190); $sp3e77f9['sign'] = $this->getSign($sp3e77f9, $this->key); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $this->gateway . '/api/order" method="post">
    <input type="hidden" name="version" value="' . $sp3e77f9['version'] . '">
    <input type="hidden" name="uid" value="' . $sp3e77f9['uid'] . '">
    <input type="hidden" name="subject" value="' . $sp3e77f9['subject'] . '">
    <input type="hidden" name="out_trade_no" value="' . $sp3e77f9['out_trade_no'] . '">
    <input type="hidden" name="total_fee" value="' . $sp3e77f9['total_fee'] . '">
    <input type="hidden" name="cost" value="' . $sp3e77f9['cost'] . '">
    <input type="hidden" name="payway" value="' . $sp3e77f9['payway'] . '">
    <input type="hidden" name="return_url" value="' . $sp3e77f9['return_url'] . '">
    <input type="hidden" name="notify_url" value="' . $sp3e77f9['notify_url'] . '">
    <input type="hidden" name="attach" value="' . $sp3e77f9['attach'] . '">
    <input type="hidden" name="sign" value="' . $sp3e77f9['sign'] . '">
</form>
</body>
        '); } function notify_verify() { $sp3e77f9 = $_POST; if ($sp3e77f9['sign'] === $this->getSign($sp3e77f9, $this->key)) { echo 'success'; return true; } else { echo 'fail'; return false; } } function return_verify() { $sp3e77f9 = $_GET; if ($sp3e77f9['sign'] === $this->getSign($sp3e77f9, $this->key)) { return true; } else { return false; } } function get_order($spba04f6) { $spb34b01 = $this->curl_post($this->gateway . '/api/order/query', 'uid=' . $this->uid . '&out_trade_no=' . $spba04f6); $spb34b01 = @json_decode($spb34b01, true); if (is_array($spb34b01) && is_array($spb34b01['data']) && isset($spb34b01['data']['order'])) { return $spb34b01['data']['order']; } return array(); } private function curl_post($sp3ae187, $spcb019a) { $spfe4f44 = curl_init($sp3ae187); curl_setopt($spfe4f44, CURLOPT_HEADER, 0); curl_setopt($spfe4f44, CURLOPT_RETURNTRANSFER, 1); curl_setopt($spfe4f44, CURLOPT_SSL_VERIFYPEER, true); curl_setopt($spfe4f44, CURLOPT_POST, true); curl_setopt($spfe4f44, CURLOPT_POSTFIELDS, $spcb019a); $spad32e6 = curl_exec($spfe4f44); curl_close($spfe4f44); return $spad32e6; } }