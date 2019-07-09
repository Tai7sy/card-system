<?php
namespace App\Library\Pay\JinfuPass; use App\Library\CurlRequest; use App\Library\Helper; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($spe00284) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $spe00284; $this->url_return = SYS_URL . '/pay/return/' . $spe00284; } function goPay($spc27de0, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { if (!isset($spc27de0['mch_id'])) { throw new \Exception('请填写商户号 mch_id'); } if (!isset($spc27de0['key'])) { throw new \Exception('请填写key'); } $spd53b1c = $spc27de0['payway']; switch ($spd53b1c) { case 'wx': $sp347986 = '102'; return $this->api_scan($spc27de0, $sp347986, 'wechat', $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); break; case 'qq': $sp347986 = '103'; return $this->api_scan($spc27de0, $sp347986, 'qq', $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); break; case 'alipay': $sp347986 = '101'; return $this->api_scan($spc27de0, $sp347986, 'aliqr', $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); break; case 'unionpay': $sp347986 = '104'; return $this->api_scan($spc27de0, $sp347986, 'unionpay', $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); break; case 'jd': $sp347986 = '105'; return $this->api_scan($spc27de0, $sp347986, 'jd', $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); break; case 'wxwap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) { $sp347986 = '3030'; return $this->api_pay_in_app($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); } else { $sp347986 = '206'; return $this->api_wap($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); } break; case 'qqwap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) { $sp347986 = '304'; return $this->api_pay_in_app($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); } else { $sp347986 = '203'; return $this->api_h5($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); } break; case 'alipaywap': if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) { $sp347986 = '302'; return $this->api_pay_in_app($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); } else { $sp347986 = '205'; return $this->api_h5($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3); } break; default: throw new \Exception('支付渠道错误'); } } function verify($spc27de0, $sp4294a3) { $spb2acff = isset($spc27de0['isNotify']) && $spc27de0['isNotify']; if ($spb2acff) { $sp2ecab4 = $spc27de0['mch_id']; $sp803c46 = $spc27de0['key']; $spba04f6 = $_POST['out_trade_no']; $sp1fbc94 = $_POST['trade_state']; $spd7876e = 'CNY'; $sp2b9922 = $_POST['pay_type']; $sp072e60 = (int) $_POST['total_amount']; $sp05dbe1 = $_POST['receipt_amount']; $sp246b40 = $_POST['sys_trade_no']; $spaa9795 = $_POST['txn_id']; $spd07659 = $_POST['device_info']; $spbcd190 = $_POST['attach']; $sp3c63da = $_POST['time_end']; $sp75e4cc = $_POST['sign']; $sp485a3f = sprintf('mch_id=%s&fee_type=%s&pay_type=%s&total_amount=%s&device_info=%s&coupon_amount=%s&key=%s', $sp2ecab4, $spba04f6, $spd7876e, $sp2b9922, $sp072e60, $spd07659, $sp803c46); if ($sp75e4cc == md5($sp485a3f)) { $sp4294a3($spba04f6, $sp072e60, $sp246b40); echo 'success'; return true; } else { echo 'FAIL'; return false; } } else { $spba04f6 = @$spc27de0['out_trade_no']; if (strlen($spba04f6) < 5) { throw new \Exception('交易单号未传入'); } $sp75e4cc = md5('version=1.0&mch_id=' . $spc27de0['mch_id'] . '&out_trade_no=' . $spba04f6 . '&sys_trade_no=&key=' . $spc27de0['key']); $sp4aa83c = array('version' => '1.0', 'mch_id' => $spc27de0['mch_id'], 'out_trade_no' => $spba04f6, 'sys_trade_no' => '', 'sign' => $sp75e4cc); $sp4187a7 = CurlRequest::post('https://pay.jinfupass.com/gateway/query', http_build_query($sp4aa83c)); $sp29a775 = @json_decode($sp4187a7, true); if (!$sp29a775 || !isset($sp29a775['result_code']) || $sp29a775['result_code'] !== '1') { Log::error('Pay.JinfuPass.verify.order Error#1: ' . $sp4187a7); throw new \Exception('获取付款信息超时, 请刷新重试'); } if ($sp29a775['trade_state'] === '1') { $sp4294a3($sp29a775['out_trade_no'], (int) $sp29a775['total_amount'], $sp29a775['sys_trade_no']); return true; } return false; } } private function api_scan($spc27de0, $sp347986, $sp69de7e, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { $sp2ecab4 = $spc27de0['mch_id']; $sp7b7024 = $spc27de0['key']; $sp6b0215 = $this->url_notify; $sp485a3f = sprintf("version=1.0&mch_id={$sp2ecab4}&pay_type={$sp347986}&total_amount={$sp6956b3}&out_trade_no={$spba04f6}&notify_url={$sp6b0215}&key={$sp7b7024}"); $sp75e4cc = md5($sp485a3f); $spcb019a = array('version' => '1.0', 'mch_id' => $sp2ecab4, 'pay_type' => $sp347986, 'fee_type' => 'CNY', 'total_amount' => $sp6956b3, 'out_trade_no' => $spba04f6, 'device_info' => date('YmdHis'), 'notify_url' => $sp6b0215, 'body' => $sp9f49de, 'attach' => '', 'time_start' => '', 'time_expire' => '', 'limit_credit_pay' => '0', 'hb_fq_num' => '', 'hb_fq_percent' => '', 'sign' => $sp75e4cc); $sp4aa83c = $spcb019a; $sp4187a7 = CurlRequest::post('http://pay.jinfupass.com/gateway/pay', http_build_query($sp4aa83c)); $sp29a775 = @json_decode($sp4187a7, true); if (!$sp29a775 || !isset($sp29a775['result_code']) || $sp29a775['result_code'] !== '1') { Log::error('Pay.JinfuPass.api_scan Error#1: ' . $sp4187a7); throw new \Exception('获取付款信息超时, 请刷新重试'); } if (!isset($sp29a775['code_url'])) { Log::error('Pay.JinfuPass.api_scan Error#2: ' . $sp4187a7); throw new \Exception('获取付款信息失败, 请联系客服反馈'); } header('location: /qrcode/pay/' . $spba04f6 . '/' . strtolower($sp69de7e) . '?url=' . urlencode($sp29a775['code_url'])); die; } private function api_pay_in_app($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { $sp2ecab4 = $spc27de0['mch_id']; $sp7b7024 = $spc27de0['key']; $sp6b0215 = $this->url_notify; $sp485a3f = sprintf("version=1.0&mch_id={$sp2ecab4}&pay_type={$sp347986}&total_amount={$sp6956b3}&out_trade_no={$spba04f6}&notify_url={$sp6b0215}&key={$sp7b7024}"); $sp75e4cc = md5($sp485a3f); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/jspay2" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $sp2ecab4 . '">
    <input type="hidden" name="pay_type" value="' . $sp347986 . '">
    <input type="hidden" name="minipg" value="0">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp6956b3 . '">
    <input type="hidden" name="out_trade_no" value="' . $spba04f6 . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $sp9f49de . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sp75e4cc . '">
</form>
</body>
        '); } private function api_h5($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { $sp2ecab4 = $spc27de0['mch_id']; $sp7b7024 = $spc27de0['key']; $sp6b0215 = $this->url_notify; $sp485a3f = sprintf("version=1.0&mch_id={$sp2ecab4}&pay_type={$sp347986}&total_amount={$sp6956b3}&out_trade_no={$spba04f6}&notify_url={$sp6b0215}&key={$sp7b7024}"); $sp75e4cc = md5($sp485a3f); die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/h5pay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $sp2ecab4 . '">
    <input type="hidden" name="pay_type" value="' . $sp347986 . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp6956b3 . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="out_trade_no" value="' . $spba04f6 . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $sp9f49de . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sp75e4cc . '">
</form>
</body>
        '); } private function api_wap($spc27de0, $sp347986, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { $sp2ecab4 = $spc27de0['mch_id']; $sp7b7024 = $spc27de0['key']; $sp6b0215 = $this->url_notify; $sp485a3f = sprintf("version=1.0&mch_id={$sp2ecab4}&pay_type={$sp347986}&total_amount={$sp6956b3}&out_trade_no={$spba04f6}&notify_url={$sp6b0215}&key={$sp7b7024}"); $sp75e4cc = md5($sp485a3f); $sp3bdc58 = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}"; die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/wappay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $sp2ecab4 . '">
    <input type="hidden" name="pay_type" value="' . $sp347986 . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $sp6956b3 . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="mch_app_name" value="' . SYS_NAME . '">
    <input type="hidden" name="mch_app_id" value="' . $sp3bdc58 . '">
    <input type="hidden" name="out_trade_no" value="' . $spba04f6 . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $sp9f49de . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sp75e4cc . '">
</form>
</body>
        '); } }