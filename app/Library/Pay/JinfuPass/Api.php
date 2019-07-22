<?php
namespace App\Library\Pay\JinfuPass; use App\Library\CurlRequest; use App\Library\Helper; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($sp53f8aa) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp53f8aa; $this->url_return = SYS_URL . '/pay/return/' . $sp53f8aa; } function goPay($spbe80b7, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { if (!isset($spbe80b7['mch_id'])) { throw new \Exception('请填写商户号 mch_id'); } if (!isset($spbe80b7['key'])) { throw new \Exception('请填写key'); } $spdc9a36 = $spbe80b7['payway']; $this->url_return .= '/' . $spa3e681; switch ($spdc9a36) { case 'wx': $sp4634ba = '102'; return $this->api_scan($spbe80b7, $sp4634ba, 'wechat', $spa3e681, $sp45f07e, $sp873488, $sp5213ee); break; case 'qq': $sp4634ba = '103'; return $this->api_scan($spbe80b7, $sp4634ba, 'qq', $spa3e681, $sp45f07e, $sp873488, $sp5213ee); break; case 'alipay': $sp4634ba = '101'; return $this->api_scan($spbe80b7, $sp4634ba, 'aliqr', $spa3e681, $sp45f07e, $sp873488, $sp5213ee); break; case 'unionpay': $sp4634ba = '104'; return $this->api_scan($spbe80b7, $sp4634ba, 'unionpay', $spa3e681, $sp45f07e, $sp873488, $sp5213ee); break; case 'jd': $sp4634ba = '105'; return $this->api_scan($spbe80b7, $sp4634ba, 'jd', $spa3e681, $sp45f07e, $sp873488, $sp5213ee); break; case 'wxwap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { $sp4634ba = '3030'; return $this->api_pay_in_app($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee); } else { $sp4634ba = '206'; return $this->api_wap($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee); } break; case 'qqwap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { $sp4634ba = '304'; return $this->api_pay_in_app($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee); } else { $sp4634ba = '203'; return $this->api_h5($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee); } break; case 'alipaywap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) { $sp4634ba = '302'; return $this->api_pay_in_app($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee); } else { $sp4634ba = '205'; return $this->api_h5($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee); } break; default: throw new \Exception('支付渠道错误'); } } function verify($spbe80b7, $sp04f0f8) { $sp3bce01 = isset($spbe80b7['isNotify']) && $spbe80b7['isNotify']; if ($sp3bce01) { $sp773cac = $spbe80b7['mch_id']; $spe19aed = $spbe80b7['key']; $spa3e681 = $_POST['out_trade_no']; $sp00f559 = $_POST['trade_state']; $spf2a22c = 'CNY'; $sp9b380a = $_POST['pay_type']; $sp378160 = (int) $_POST['total_amount']; $sp26f1a8 = $_POST['receipt_amount']; $spacb468 = $_POST['sys_trade_no']; $spe02e4c = $_POST['txn_id']; $sp3927f2 = $_POST['device_info']; $sp5b1094 = $_POST['attach']; $sp1ea6f4 = $_POST['time_end']; $spa109d2 = $_POST['sign']; $spe78a33 = sprintf('mch_id=%s&fee_type=%s&pay_type=%s&total_amount=%s&device_info=%s&coupon_amount=%s&key=%s', $sp773cac, $spa3e681, $spf2a22c, $sp9b380a, $sp378160, $sp3927f2, $spe19aed); if ($spa109d2 == md5($spe78a33)) { $sp04f0f8($spa3e681, $sp378160, $spacb468); echo 'success'; return true; } else { echo 'FAIL'; return false; } } else { $spa3e681 = @$spbe80b7['out_trade_no']; if (strlen($spa3e681) < 5) { throw new \Exception('交易单号未传入'); } $spa109d2 = md5('version=1.0&mch_id=' . $spbe80b7['mch_id'] . '&out_trade_no=' . $spa3e681 . '&sys_trade_no=&key=' . $spbe80b7['key']); $sp0e98cb = array('version' => '1.0', 'mch_id' => $spbe80b7['mch_id'], 'out_trade_no' => $spa3e681, 'sys_trade_no' => '', 'sign' => $spa109d2); $sp00a165 = CurlRequest::post('https://pay.jinfupass.com/gateway/query', http_build_query($sp0e98cb)); $spb9589c = @json_decode($sp00a165, true); if (!$spb9589c || !isset($spb9589c['result_code']) || $spb9589c['result_code'] !== '1') { Log::error('Pay.JinfuPass.verify.order Error#1: ' . $sp00a165); throw new \Exception('获取付款信息超时, 请刷新重试'); } if ($spb9589c['trade_state'] === '1') { $sp04f0f8($spb9589c['out_trade_no'], (int) $spb9589c['total_amount'], $spb9589c['sys_trade_no']); return true; } return false; } } private function api_scan($spbe80b7, $sp4634ba, $sp28fede, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp773cac = $spbe80b7['mch_id']; $sp1ed429 = $spbe80b7['key']; $sp8f72d3 = $this->url_notify; $spe78a33 = sprintf("version=1.0&mch_id={$sp773cac}&pay_type={$sp4634ba}&total_amount={$sp5213ee}&out_trade_no={$spa3e681}&notify_url={$sp8f72d3}&key={$sp1ed429}"); $spa109d2 = md5($spe78a33); $sp5aa598 = array('version' => '1.0', 'mch_id' => $sp773cac, 'pay_type' => $sp4634ba, 'fee_type' => 'CNY', 'total_amount' => $sp5213ee, 'out_trade_no' => $spa3e681, 'device_info' => date('YmdHis'), 'notify_url' => $sp8f72d3, 'body' => $sp45f07e, 'attach' => '', 'time_start' => '', 'time_expire' => '', 'limit_credit_pay' => '0', 'hb_fq_num' => '', 'hb_fq_percent' => '', 'sign' => $spa109d2); $sp0e98cb = $sp5aa598; $sp00a165 = CurlRequest::post('http://pay.jinfupass.com/gateway/pay', http_build_query($sp0e98cb)); $spb9589c = @json_decode($sp00a165, true); if (!$spb9589c || !isset($spb9589c['result_code']) || $spb9589c['result_code'] !== '1') { Log::error('Pay.JinfuPass.api_scan Error#1: ' . $sp00a165); throw new \Exception('获取付款信息超时, 请刷新重试'); } if (empty($spb9589c['code_url'])) { Log::error('Pay.JinfuPass.api_scan Error#2: ' . $sp00a165); throw new \Exception('获取付款信息失败, 请联系客服反馈'); } header('location: /qrcode/pay/' . $spa3e681 . '/' . strtolower($sp28fede) . '?url=' . urlencode($spb9589c['code_url'])); die; } private function api_pay_in_app($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp773cac = $spbe80b7['mch_id']; $sp1ed429 = $spbe80b7['key']; $sp8f72d3 = $this->url_notify; $spe78a33 = sprintf("version=1.0&mch_id={$sp773cac}&pay_type={$sp4634ba}&total_amount={$sp5213ee}&out_trade_no={$spa3e681}&notify_url={$sp8f72d3}&key={$sp1ed429}"); $spa109d2 = md5($spe78a33); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/jspay2" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $sp773cac . '">
    <input type="hidden" name="pay_type" value="' . $sp4634ba . '">
    <input type="hidden" name="minipg" value="0">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp5213ee . '">
    <input type="hidden" name="out_trade_no" value="' . $spa3e681 . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $sp45f07e . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $spa109d2 . '">
</form>
</body>
        '); } private function api_h5($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp773cac = $spbe80b7['mch_id']; $sp1ed429 = $spbe80b7['key']; $sp8f72d3 = $this->url_notify; $spe78a33 = sprintf("version=1.0&mch_id={$sp773cac}&pay_type={$sp4634ba}&total_amount={$sp5213ee}&out_trade_no={$spa3e681}&notify_url={$sp8f72d3}&key={$sp1ed429}"); $spa109d2 = md5($spe78a33); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/h5pay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $sp773cac . '">
    <input type="hidden" name="pay_type" value="' . $sp4634ba . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp5213ee . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="out_trade_no" value="' . $spa3e681 . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $sp45f07e . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $spa109d2 . '">
</form>
</body>
        '); } private function api_wap($spbe80b7, $sp4634ba, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp773cac = $spbe80b7['mch_id']; $sp1ed429 = $spbe80b7['key']; $sp8f72d3 = $this->url_notify; $spe78a33 = sprintf("version=1.0&mch_id={$sp773cac}&pay_type={$sp4634ba}&total_amount={$sp5213ee}&out_trade_no={$spa3e681}&notify_url={$sp8f72d3}&key={$sp1ed429}"); $spa109d2 = md5($spe78a33); $sp501a23 = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}"; die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/wappay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $sp773cac . '">
    <input type="hidden" name="pay_type" value="' . $sp4634ba . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp5213ee . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="mch_app_name" value="' . SYS_NAME . '">
    <input type="hidden" name="mch_app_id" value="' . $sp501a23 . '">
    <input type="hidden" name="out_trade_no" value="' . $spa3e681 . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $sp45f07e . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $spa109d2 . '">
</form>
</body>
        '); } }