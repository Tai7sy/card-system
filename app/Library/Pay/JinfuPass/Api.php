<?php
namespace App\Library\Pay\JinfuPass; use App\Library\CurlRequest; use App\Library\Helper; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($sp3c46ab) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp3c46ab; $this->url_return = SYS_URL . '/pay/return/' . $sp3c46ab; } function goPay($sp9d4382, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { if (!isset($sp9d4382['mch_id'])) { throw new \Exception('请填写商户号 mch_id'); } if (!isset($sp9d4382['key'])) { throw new \Exception('请填写key'); } $spf9ca0c = $sp9d4382['payway']; $this->url_return .= '/' . $sp2e47fc; switch ($spf9ca0c) { case 'wx': $spd26c0a = '102'; return $this->api_scan($sp9d4382, $spd26c0a, 'wechat', $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); break; case 'qq': $spd26c0a = '103'; return $this->api_scan($sp9d4382, $spd26c0a, 'qq', $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); break; case 'alipay': $spd26c0a = '101'; return $this->api_scan($sp9d4382, $spd26c0a, 'aliqr', $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); break; case 'unionpay': $spd26c0a = '104'; return $this->api_scan($sp9d4382, $spd26c0a, 'unionpay', $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); break; case 'jd': $spd26c0a = '105'; return $this->api_scan($sp9d4382, $spd26c0a, 'jd', $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); break; case 'wxwap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { $spd26c0a = '3030'; return $this->api_pay_in_app($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); } else { $spd26c0a = '206'; return $this->api_wap($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); } break; case 'qqwap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { $spd26c0a = '304'; return $this->api_pay_in_app($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); } else { $spd26c0a = '203'; return $this->api_h5($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); } break; case 'alipaywap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) { $spd26c0a = '302'; return $this->api_pay_in_app($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); } else { $spd26c0a = '205'; return $this->api_h5($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7); } break; default: throw new \Exception('支付渠道错误'); } } function verify($sp9d4382, $sp9a4d97) { $sp7b2182 = isset($sp9d4382['isNotify']) && $sp9d4382['isNotify']; if ($sp7b2182) { $spaac434 = $sp9d4382['mch_id']; $sp08ccfa = $sp9d4382['key']; $sp2e47fc = $_POST['out_trade_no']; $spbc414e = $_POST['trade_state']; $spa809c7 = 'CNY'; $sp73afba = $_POST['pay_type']; $spfcbd75 = (int) $_POST['total_amount']; $sp3dd39c = $_POST['receipt_amount']; $spaa1aea = $_POST['sys_trade_no']; $sp41f42a = $_POST['txn_id']; $spc3064a = $_POST['device_info']; $spf83ede = $_POST['attach']; $sp25ec17 = $_POST['time_end']; $sp964415 = $_POST['sign']; $spa9bc6a = sprintf('mch_id=%s&fee_type=%s&pay_type=%s&total_amount=%s&device_info=%s&coupon_amount=%s&key=%s', $spaac434, $sp2e47fc, $spa809c7, $sp73afba, $spfcbd75, $spc3064a, $sp08ccfa); if ($sp964415 == md5($spa9bc6a)) { $sp9a4d97($sp2e47fc, $spfcbd75, $spaa1aea); echo 'success'; return true; } else { echo 'FAIL'; return false; } } else { $sp2e47fc = @$sp9d4382['out_trade_no']; if (strlen($sp2e47fc) < 5) { throw new \Exception('交易单号未传入'); } $sp964415 = md5('version=1.0&mch_id=' . $sp9d4382['mch_id'] . '&out_trade_no=' . $sp2e47fc . '&sys_trade_no=&key=' . $sp9d4382['key']); $spe25017 = array('version' => '1.0', 'mch_id' => $sp9d4382['mch_id'], 'out_trade_no' => $sp2e47fc, 'sys_trade_no' => '', 'sign' => $sp964415); $sp42422c = CurlRequest::post('https://pay.jinfupass.com/gateway/query', http_build_query($spe25017)); $sp9b52fe = @json_decode($sp42422c, true); if (!$sp9b52fe || !isset($sp9b52fe['result_code']) || $sp9b52fe['result_code'] !== '1') { Log::error('Pay.JinfuPass.verify.order Error#1: ' . $sp42422c); throw new \Exception('获取付款信息超时, 请刷新重试'); } if ($sp9b52fe['trade_state'] === '1') { $sp9a4d97($sp9b52fe['out_trade_no'], (int) $sp9b52fe['total_amount'], $sp9b52fe['sys_trade_no']); return true; } return false; } } private function api_scan($sp9d4382, $spd26c0a, $spabdeda, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spaac434 = $sp9d4382['mch_id']; $spf1241f = $sp9d4382['key']; $sp400d9a = $this->url_notify; $spa9bc6a = sprintf("version=1.0&mch_id={$spaac434}&pay_type={$spd26c0a}&total_amount={$sp076ec7}&out_trade_no={$sp2e47fc}&notify_url={$sp400d9a}&key={$spf1241f}"); $sp964415 = md5($spa9bc6a); $sp6fd648 = array('version' => '1.0', 'mch_id' => $spaac434, 'pay_type' => $spd26c0a, 'fee_type' => 'CNY', 'total_amount' => $sp076ec7, 'out_trade_no' => $sp2e47fc, 'device_info' => date('YmdHis'), 'notify_url' => $sp400d9a, 'body' => $spd4e90d, 'attach' => '', 'time_start' => '', 'time_expire' => '', 'limit_credit_pay' => '0', 'hb_fq_num' => '', 'hb_fq_percent' => '', 'sign' => $sp964415); $spe25017 = $sp6fd648; $sp42422c = CurlRequest::post('http://pay.jinfupass.com/gateway/pay', http_build_query($spe25017)); $sp9b52fe = @json_decode($sp42422c, true); if (!$sp9b52fe || !isset($sp9b52fe['result_code']) || $sp9b52fe['result_code'] !== '1') { Log::error('Pay.JinfuPass.api_scan Error#1: ' . $sp42422c); throw new \Exception('获取付款信息超时, 请刷新重试'); } if (empty($sp9b52fe['code_url'])) { Log::error('Pay.JinfuPass.api_scan Error#2: ' . $sp42422c); throw new \Exception('获取付款信息失败, 请联系客服反馈'); } header('location: /qrcode/pay/' . $sp2e47fc . '/' . strtolower($spabdeda) . '?url=' . urlencode($sp9b52fe['code_url'])); die; } private function api_pay_in_app($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spaac434 = $sp9d4382['mch_id']; $spf1241f = $sp9d4382['key']; $sp400d9a = $this->url_notify; $spa9bc6a = sprintf("version=1.0&mch_id={$spaac434}&pay_type={$spd26c0a}&total_amount={$sp076ec7}&out_trade_no={$sp2e47fc}&notify_url={$sp400d9a}&key={$spf1241f}"); $sp964415 = md5($spa9bc6a); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/jspay2" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $spaac434 . '">
    <input type="hidden" name="pay_type" value="' . $spd26c0a . '">
    <input type="hidden" name="minipg" value="0">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp076ec7 . '">
    <input type="hidden" name="out_trade_no" value="' . $sp2e47fc . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $spd4e90d . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sp964415 . '">
</form>
</body>
        '); } private function api_h5($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spaac434 = $sp9d4382['mch_id']; $spf1241f = $sp9d4382['key']; $sp400d9a = $this->url_notify; $spa9bc6a = sprintf("version=1.0&mch_id={$spaac434}&pay_type={$spd26c0a}&total_amount={$sp076ec7}&out_trade_no={$sp2e47fc}&notify_url={$sp400d9a}&key={$spf1241f}"); $sp964415 = md5($spa9bc6a); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/h5pay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $spaac434 . '">
    <input type="hidden" name="pay_type" value="' . $spd26c0a . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp076ec7 . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="out_trade_no" value="' . $sp2e47fc . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $spd4e90d . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sp964415 . '">
</form>
</body>
        '); } private function api_wap($sp9d4382, $spd26c0a, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spaac434 = $sp9d4382['mch_id']; $spf1241f = $sp9d4382['key']; $sp400d9a = $this->url_notify; $spa9bc6a = sprintf("version=1.0&mch_id={$spaac434}&pay_type={$spd26c0a}&total_amount={$sp076ec7}&out_trade_no={$sp2e47fc}&notify_url={$sp400d9a}&key={$spf1241f}"); $sp964415 = md5($spa9bc6a); $spa9e20d = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}"; die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/wappay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $spaac434 . '">
    <input type="hidden" name="pay_type" value="' . $spd26c0a . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp076ec7 . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="mch_app_name" value="' . SYS_NAME . '">
    <input type="hidden" name="mch_app_id" value="' . $spa9e20d . '">
    <input type="hidden" name="out_trade_no" value="' . $sp2e47fc . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $spd4e90d . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sp964415 . '">
</form>
</body>
        '); } }