<?php
class fakala { public $gateway; public $uid; public $key; public function __construct($sp7fd7bb, $sp53f8aa, $sp1ed429) { $this->gateway = $sp7fd7bb; $this->uid = $sp53f8aa; $this->key = $sp1ed429; } function getSignStr($spa26894) { ksort($spa26894); $spea250e = ''; foreach ($spa26894 as $spce2336 => $sp39a929) { if ('sign' !== $spce2336) { $spea250e .= $spce2336 . '=' . $sp39a929 . '&'; } } return $spea250e; } function getSign($spa26894, $sp1ed429, &$spec9e2f = false) { $spea250e = self::getSignStr($spa26894); $spa109d2 = md5($spea250e . 'key=' . $sp1ed429); if ($spec9e2f !== false) { $spec9e2f = $spea250e . 'sign=' . $spa109d2; } return $spa109d2; } function goPay($spdc9a36, $sp45f07e, $spa3e681, $sp585cd5, $sp9624ba, $sp5b1094, $sp2c8827, $sp8f72d3) { $spa26894 = array('version' => '20190501', 'uid' => (int) $this->uid, 'subject' => $sp45f07e, 'out_trade_no' => $spa3e681, 'total_fee' => (int) $sp9624ba, 'cost' => (int) $sp585cd5, 'payway' => $spdc9a36, 'return_url' => $sp2c8827, 'notify_url' => $sp8f72d3, 'attach' => $sp5b1094); $spa26894['sign'] = $this->getSign($spa26894, $this->key); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $this->gateway . '/api/order" method="post">
    <input type="hidden" name="version" value="' . $spa26894['version'] . '">
    <input type="hidden" name="uid" value="' . $spa26894['uid'] . '">
    <input type="hidden" name="subject" value="' . $spa26894['subject'] . '">
    <input type="hidden" name="out_trade_no" value="' . $spa26894['out_trade_no'] . '">
    <input type="hidden" name="total_fee" value="' . $spa26894['total_fee'] . '">
    <input type="hidden" name="cost" value="' . $spa26894['cost'] . '">
    <input type="hidden" name="payway" value="' . $spa26894['payway'] . '">
    <input type="hidden" name="return_url" value="' . $spa26894['return_url'] . '">
    <input type="hidden" name="notify_url" value="' . $spa26894['notify_url'] . '">
    <input type="hidden" name="attach" value="' . $spa26894['attach'] . '">
    <input type="hidden" name="sign" value="' . $spa26894['sign'] . '">
</form>
</body>
        '); } function notify_verify() { $spa26894 = $_POST; if ($spa26894['sign'] === $this->getSign($spa26894, $this->key)) { echo 'success'; return true; } else { echo 'fail'; return false; } } function return_verify() { $spa26894 = $_GET; if ($spa26894['sign'] === $this->getSign($spa26894, $this->key)) { return true; } else { return false; } } function get_order($spa3e681) { $spbbda25 = $this->curl_post($this->gateway . '/api/order/query', 'uid=' . $this->uid . '&out_trade_no=' . $spa3e681); $spbbda25 = @json_decode($spbbda25, true); if (is_array($spbbda25) && is_array($spbbda25['data']) && isset($spbbda25['data']['order'])) { return $spbbda25['data']['order']; } return array(); } private function curl_post($sp3db1b2, $sp5aa598) { $spf94a8f = curl_init($sp3db1b2); curl_setopt($spf94a8f, CURLOPT_HEADER, 0); curl_setopt($spf94a8f, CURLOPT_RETURNTRANSFER, 1); curl_setopt($spf94a8f, CURLOPT_SSL_VERIFYPEER, true); curl_setopt($spf94a8f, CURLOPT_POST, true); curl_setopt($spf94a8f, CURLOPT_POSTFIELDS, $sp5aa598); $sp3ac4e3 = curl_exec($spf94a8f); curl_close($spf94a8f); return $sp3ac4e3; } }