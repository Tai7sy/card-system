<?php

/**
 * 黔贵金服
 * https://mch.jinfupass.com/merchant/login
 * 通道编码
 * https://open.jinfupass.com/index.php?s=/1&page_id=10
 */

namespace App\Library\Pay\JinfuPass;

use App\Library\CurlRequest;
use App\Library\Helper;
use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/** @noinspection SpellCheckingInspection */

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
    }

    /**
     * @param array $config
     * @param string $out_trade_no
     * @param string $subject
     * @param string $body
     * @param int $amount_cent
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        if (!isset($config['mch_id'])) {
            throw new \Exception('请填写商户号 mch_id');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写key');
        }

        // wx(微信扫码) wxwap(微信手机端, 自动判断浏览器/公众号)
        // qq(qq扫码) qqwap(微信手机端, 自动判断浏览器/公众号)
        // alipay(支付宝扫码)  wxwap(支付宝手机端, 自动判断浏览器/生活号)
        // jd unionpay
        $payway = $config['payway'];
        $this->url_return .= '/' . $out_trade_no;
        switch ($payway) {
            case 'wx':
                $channel = '102';      // 微信扫码（线下）
                return $this->api_scan($config, $channel, 'wechat', $out_trade_no, $subject, $body, $amount_cent);
                break;
            case 'qq':
                $channel = '103';      // 	QQ钱包扫码
                return $this->api_scan($config, $channel, 'qq', $out_trade_no, $subject, $body, $amount_cent);
                break;
            case 'alipay':
                $channel = '101';      // 支付宝扫码
                return $this->api_scan($config, $channel, 'aliqr', $out_trade_no, $subject, $body, $amount_cent);
                break;
            case 'unionpay':
                $channel = '104';      // 银联扫码
                return $this->api_scan($config, $channel, 'unionpay', $out_trade_no, $subject, $body, $amount_cent);
                break;
            case 'jd':
                $channel = '105';      // 京东扫码
                return $this->api_scan($config, $channel, 'jd', $out_trade_no, $subject, $body, $amount_cent);
                break;
            case 'wxwap':
                if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                    // 微信内部
                    $channel = '3030';  // 微信公众号（线上）
                    return $this->api_pay_in_app($config, $channel, $out_trade_no, $subject, $body, $amount_cent);
                } else {
                    $channel = '206'; // 微信WAP
                    return $this->api_wap($config, $channel, $out_trade_no, $subject, $body, $amount_cent);
                }
                break;
            case 'qqwap':
                if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MQQBrowser') !== false) {
                    // qq内部
                    $channel = '304';  // QQ钱包公众号
                    return $this->api_pay_in_app($config, $channel, $out_trade_no, $subject, $body, $amount_cent);
                } else {
                    $channel = '203'; // QQ钱包H5支付
                    return $this->api_h5($config, $channel, $out_trade_no, $subject, $body, $amount_cent);
                }
                break;
            case 'alipaywap':
                if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'AlipayClient') !== false) {
                    // 支付宝内部
                    $channel = '302';  // 支付宝服务窗
                    return $this->api_pay_in_app($config, $channel, $out_trade_no, $subject, $body, $amount_cent);
                } else {
                    $channel = '205';  // 支付宝H5
                    return $this->api_h5($config, $channel, $out_trade_no, $subject, $body, $amount_cent);
                }
                break;
            default:
                throw new \Exception('支付渠道错误');

        }
    }

    /**
     * @param $config
     * @param callable $successCallback
     * @return bool|string
     * @throws \Exception
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        if ($isNotify) {

            $mch_id = $config['mch_id'];//商户号，由平台分配
            $Key = $config['key'];//秘钥，由平台分配
            $out_trade_no = $_POST["out_trade_no"];//商户系统订单号，原样返回
            $trade_state = $_POST["trade_state"];//1：支付成功，0：未支付，2：失败，3：已退款
            $fee_type = "CNY";//默认人民币：CNY
            $pay_type = $_POST["pay_type"];//支付类型 ，比如 201 202
            $total_amount = (int)$_POST["total_amount"];//订单金额，单位为分
            $receipt_amount = $_POST["receipt_amount"];//实际付款金额，单位为分
            $sys_trade_no = $_POST["sys_trade_no"];//平台系统内的订单号
            $txn_id = $_POST["txn_id"];//微信、支付宝、QQ、银联、京东等官方订单号
            $device_info = $_POST["device_info"];//终端设备号
            $attach = $_POST["attach"];//商户附加信息
            $time_end = $_POST["time_end"];//支付完成时间，格式为yyyyMMddHHmmss
            $sign = $_POST["sign"];//MD5签名，32位小写


            $signSource = sprintf("mch_id=%s&fee_type=%s&pay_type=%s&total_amount=%s&device_info=%s&coupon_amount=%s&key=%s", $mch_id, $out_trade_no, $fee_type, $pay_type, $total_amount, $device_info, $Key); //连接字符串加密处理

            if ($sign == md5($signSource))//签名正确
            {
                //此处作逻辑处理
                $successCallback($out_trade_no, $total_amount, $sys_trade_no);

                echo('success');
                return true;
            } else {
                echo('FAIL');
                return false;

            }

        } else {
            $out_trade_no = @$config['out_trade_no'];
            if (strlen($out_trade_no) < 5) {
                // payReturn 页面
                throw new \Exception('交易单号未传入');
            }


            $sign = md5('version=1.0&mch_id=' . $config['mch_id'] . '&out_trade_no=' . $out_trade_no . '&sys_trade_no=&key=' . $config['key']);
            $post_data = [
                'version' => '1.0',
                'mch_id' => $config['mch_id'],
                'out_trade_no' => $out_trade_no,
                'sys_trade_no' => '',
                'sign' => $sign

            ];
            $ret_raw = CurlRequest::post('https://pay.jinfupass.com/gateway/query', http_build_query($post_data));
            $ret = @json_decode($ret_raw, true);
            if (!$ret || !isset($ret['result_code']) || $ret['result_code'] !== '1') {
                Log::error('Pay.JinfuPass.verify.order Error#1: ' . $ret_raw);
                throw new \Exception('获取付款信息超时, 请刷新重试');
            }

            if ($ret['trade_state'] === '1') {
                $successCallback($ret['out_trade_no'], (int)$ret['total_amount'], $ret['sys_trade_no']);
                return true;
            }

            // 此驱动, 不支持主动查询交易结果
            return false;
        }
    }


    // 扫码下单API
    private function api_scan($config, $channel, $qrcodeType, $out_trade_no, $subject, $body, $amount_cent)
    {
        $mch_id = $config['mch_id'];    //	商户号
        $key = $config['key']; // key
        $notify_url = $this->url_notify;    //	通知地址

        $signSource = sprintf("version=1.0&mch_id=$mch_id&pay_type=$channel&total_amount=$amount_cent&out_trade_no=$out_trade_no&notify_url=$notify_url&key=$key");
        $sign = md5($signSource);

        $data = array(
            "version" => '1.0',
            "mch_id" => $mch_id,
            "pay_type" => $channel,
            "fee_type" => 'CNY',
            "total_amount" => $amount_cent,
            "out_trade_no" => $out_trade_no,
            "device_info" => date("YmdHis"), //	设备号
            "notify_url" => $notify_url,
            "body" => $subject,
            "attach" => '',
            "time_start" => '', // 订单生成时间
            "time_expire" => '', // 订单失效时间
            "limit_credit_pay" => '0', // 禁用信用卡
            "hb_fq_num" => '', // 花呗分期  分期数：3、6、12
            "hb_fq_percent" => '', // 花呗分期 手续费承担方 用户：0 ； 商户： 100
            "sign" => $sign,
        );
        $post_data = $data;

        $ret_raw = CurlRequest::post('http://pay.jinfupass.com/gateway/pay', http_build_query($post_data));
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['result_code']) || $ret['result_code'] !== '1') {
            Log::error('Pay.JinfuPass.api_scan Error#1: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        if (empty($ret['code_url'])) {
            Log::error('Pay.JinfuPass.api_scan Error#2: ' . $ret_raw);
            throw new \Exception('获取付款信息失败, 请联系客服反馈');
        }

        // Log::debug('Pay.JinfuPass.api_scan response: ' . $ret_raw);

        header('location: /qrcode/pay/' . $out_trade_no . '/' . strtolower($qrcodeType) . '?url=' . urlencode($ret['code_url']));
        exit;
    }


    // 各种支付方式内
    private function api_pay_in_app($config, $channel, $out_trade_no, $subject, $body, $amount_cent)
    {
        $mch_id = $config['mch_id'];    //	商户号
        $key = $config['key']; // key
        $notify_url = $this->url_notify;    //	通知地址

        $signSource = sprintf("version=1.0&mch_id=$mch_id&pay_type=$channel&total_amount=$amount_cent&out_trade_no=$out_trade_no&notify_url=$notify_url&key=$key");
        $sign = md5($signSource);
        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/jspay2" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $mch_id . '">
    <input type="hidden" name="pay_type" value="' . $channel . '">
    <input type="hidden" name="minipg" value="0">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $amount_cent . '">
    <input type="hidden" name="out_trade_no" value="' . $out_trade_no . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $subject . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sign . '">
</form>
</body>
        '
        );
    }

    // h5支付
    private function api_h5($config, $channel, $out_trade_no, $subject, $body, $amount_cent)
    {
        $mch_id = $config['mch_id'];    //	商户号
        $key = $config['key']; // key
        $notify_url = $this->url_notify;    //	通知地址

        $signSource = sprintf("version=1.0&mch_id=$mch_id&pay_type=$channel&total_amount=$amount_cent&out_trade_no=$out_trade_no&notify_url=$notify_url&key=$key");
        $sign = md5($signSource);
        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/h5pay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $mch_id . '">
    <input type="hidden" name="pay_type" value="' . $channel . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $amount_cent . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="out_trade_no" value="' . $out_trade_no . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $subject . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sign . '">
</form>
</body>
        '
        );
    }

    // wap支付(微信)
    private function api_wap($config, $channel, $out_trade_no, $subject, $body, $amount_cent)
    {
        $mch_id = $config['mch_id'];    //	商户号
        $key = $config['key']; // key
        $notify_url = $this->url_notify;    //	通知地址

        $signSource = sprintf("version=1.0&mch_id=$mch_id&pay_type=$channel&total_amount=$amount_cent&out_trade_no=$out_trade_no&notify_url=$notify_url&key=$key");
        $sign = md5($signSource);

        $url_home = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}";
        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://pay.jinfupass.com/gateway/wappay" method="post">
    <input type="hidden" name="version" value="1.0">
    <input type="hidden" name="mch_id" value="' . $mch_id . '">
    <input type="hidden" name="pay_type" value="' . $channel . '">
    <input type="hidden" name="fee_type" value="CNY">
    <input type="hidden" name="total_amount" value="' . $amount_cent . '">
    <input type="hidden" name="device_info" value="AND_WAP">
    <input type="hidden" name="mch_app_name" value="' . SYS_NAME . '">
    <input type="hidden" name="mch_app_id" value="' . $url_home . '">
    <input type="hidden" name="out_trade_no" value="' . $out_trade_no . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
    <input type="hidden" name="return_url" value="' . $this->url_return . '">
    <input type="hidden" name="body" value="' . $subject . '">
    <input type="hidden" name="sp_client_ip" value="' . Helper::getIP() . '">
    <input type="hidden" name="sign" value="' . $sign . '">
</form>
</body>
        '
        );
    }
}