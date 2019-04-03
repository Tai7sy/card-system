<?php
class fakala { public $gateway; public $uid; public $key; public function __construct($spc68546, $spcbbf66, $spc37b44) { $this->gateway = $spc68546; $this->uid = $spcbbf66; $this->key = $spc37b44; } function getSignStr($sp342eea) { ksort($sp342eea); $sp0ef980 = ''; foreach ($sp342eea as $sp783862 => $spce84f3) { if ('sign' !== $sp783862) { $sp0ef980 .= $sp783862 . '=' . ($spce84f3 ? $spce84f3 : '') . '&'; } } return $sp0ef980; } function getSign($sp342eea, $spc37b44, &$spf26c54 = false) { $sp0ef980 = self::getSignStr($sp342eea); $spa0f4ca = md5($sp0ef980 . 'key=' . $spc37b44); if ($spf26c54 !== false) { $spf26c54 = $sp0ef980 . 'sign=' . $spa0f4ca; } return $spa0f4ca; } function goPay($sp0a27da, $sp30c318, $sp366d9f, $sp51db0b, $sp3240fa, $spddc6c4, $sp296301) { $sp342eea = array('uid' => (int) $this->uid, 'out_trade_no' => $sp30c318, 'total_fee' => (int) $sp51db0b, 'cost' => (int) $sp366d9f, 'payway' => $sp0a27da, 'return_url' => $spddc6c4, 'notify_url' => $sp296301, 'attach' => $sp3240fa); $sp342eea['sign'] = $this->getSign($sp342eea, $this->key); die('
<!doctype html>
<html>
<head>
    <title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $this->gateway . '/api/order" method="post">
    <input type="hidden" name="uid" value="' . $sp342eea['uid'] . '">
    <input type="hidden" name="out_trade_no" value="' . $sp342eea['out_trade_no'] . '">
    <input type="hidden" name="total_fee" value="' . $sp342eea['total_fee'] . '">
    <input type="hidden" name="cost" value="' . $sp342eea['cost'] . '">
    <input type="hidden" name="payway" value="' . $sp342eea['payway'] . '">
    <input type="hidden" name="return_url" value="' . $sp342eea['return_url'] . '">
    <input type="hidden" name="notify_url" value="' . $sp342eea['notify_url'] . '">
    <input type="hidden" name="attach" value="' . $sp342eea['attach'] . '">
    <input type="hidden" name="sign" value="' . $sp342eea['sign'] . '">
</form>
</body>
        '); } function notify_verify() { $sp342eea = $_POST; if ($sp342eea['sign'] === $this->getSign($sp342eea, $this->key)) { echo 'success'; return true; } else { echo 'fail'; return false; } } function return_verify() { $sp342eea = $_GET; if ($sp342eea['sign'] === $this->getSign($sp342eea, $this->key)) { return true; } else { return false; } } }