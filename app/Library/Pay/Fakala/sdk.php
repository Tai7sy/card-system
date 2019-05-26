<?php
class fakala { public $gateway; public $uid; public $key; public function __construct($sp2984e4, $sp5d3f49, $sp1e4b49) { $this->gateway = $sp2984e4; $this->uid = $sp5d3f49; $this->key = $sp1e4b49; } function getSignStr($sp7c7a93) { ksort($sp7c7a93); $sp7bd4b2 = ''; foreach ($sp7c7a93 as $spc57c89 => $sp2a00a3) { if ('sign' !== $spc57c89) { $sp7bd4b2 .= $spc57c89 . '=' . $sp2a00a3 . '&'; } } return $sp7bd4b2; } function getSign($sp7c7a93, $sp1e4b49, &$sp9cc19c = false) { $sp7bd4b2 = self::getSignStr($sp7c7a93); $sp1e1df1 = md5($sp7bd4b2 . 'key=' . $sp1e4b49); if ($sp9cc19c !== false) { $sp9cc19c = $sp7bd4b2 . 'sign=' . $sp1e1df1; } return $sp1e1df1; } function goPay($sp2688c6, $sp930bb6, $sp4510be, $sp152c6f, $sp6f1ff6, $sp2ff0a2, $spbaa59b, $spfad78b) { $sp7c7a93 = array('version' => '20190501', 'uid' => (int) $this->uid, 'subject' => $sp930bb6, 'out_trade_no' => $sp4510be, 'total_fee' => (int) $sp6f1ff6, 'cost' => (int) $sp152c6f, 'payway' => $sp2688c6, 'return_url' => $spbaa59b, 'notify_url' => $spfad78b, 'attach' => $sp2ff0a2); $sp7c7a93['sign'] = $this->getSign($sp7c7a93, $this->key); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $this->gateway . '/api/order" method="post">
    <input type="hidden" name="version" value="' . $sp7c7a93['version'] . '">
    <input type="hidden" name="uid" value="' . $sp7c7a93['uid'] . '">
    <input type="hidden" name="subject" value="' . $sp7c7a93['subject'] . '">
    <input type="hidden" name="out_trade_no" value="' . $sp7c7a93['out_trade_no'] . '">
    <input type="hidden" name="total_fee" value="' . $sp7c7a93['total_fee'] . '">
    <input type="hidden" name="cost" value="' . $sp7c7a93['cost'] . '">
    <input type="hidden" name="payway" value="' . $sp7c7a93['payway'] . '">
    <input type="hidden" name="return_url" value="' . $sp7c7a93['return_url'] . '">
    <input type="hidden" name="notify_url" value="' . $sp7c7a93['notify_url'] . '">
    <input type="hidden" name="attach" value="' . $sp7c7a93['attach'] . '">
    <input type="hidden" name="sign" value="' . $sp7c7a93['sign'] . '">
</form>
</body>
        '); } function notify_verify() { $sp7c7a93 = $_POST; if ($sp7c7a93['sign'] === $this->getSign($sp7c7a93, $this->key)) { echo 'success'; return true; } else { echo 'fail'; return false; } } function return_verify() { $sp7c7a93 = $_GET; if ($sp7c7a93['sign'] === $this->getSign($sp7c7a93, $this->key)) { return true; } else { return false; } } function get_order($sp4510be) { $sp820aff = $this->curl_post($this->gateway . '/api/order/query', 'uid=' . $this->uid . '&out_trade_no=' . $sp4510be); $sp820aff = @json_decode($sp820aff, true); if (is_array($sp820aff) && is_array($sp820aff['data']) && isset($sp820aff['data']['order'])) { return $sp820aff['data']['order']; } return array(); } private function curl_post($sp59c732, $sp69c4ce) { $spe00444 = curl_init($sp59c732); curl_setopt($spe00444, CURLOPT_HEADER, 0); curl_setopt($spe00444, CURLOPT_RETURNTRANSFER, 1); curl_setopt($spe00444, CURLOPT_SSL_VERIFYPEER, true); curl_setopt($spe00444, CURLOPT_POST, true); curl_setopt($spe00444, CURLOPT_POSTFIELDS, $sp69c4ce); $sp31c557 = curl_exec($spe00444); curl_close($spe00444); return $sp31c557; } }