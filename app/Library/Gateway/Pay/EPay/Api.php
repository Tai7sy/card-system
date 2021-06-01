<?php

namespace Gateway\Pay\EPay;

use App\Library\CurlRequest;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';
    private $pay_id;

    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
        $this->pay_id = $id;
    }

    private function getSign($params, $sign_type, $sign_key)
    {
        ksort($params);
        reset($params);

        $sign_str = '';
        foreach ($params as $key => $val) {
            if (!$val || $key == 'sign' || $key == 'sign_type')
                continue;
            $sign_str .= $key . '=' . $val . '&';
        }
        //去掉最后一个&字符
        $sign_str = substr($sign_str, 0, -1);

        if ($sign_type === 'MD5') {
            return md5($sign_str . $sign_key);
        } else {
            return '';
        }
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
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'http://pay.tgyzf.cn/';
        }
        if (!isset($config['pid'])) {
            throw new \Exception('请填写pid');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写key');
        }

        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位

        // alipay:支付宝,wxpay:微信支付,qqpay:QQ钱包
        $payway = strtolower($config['payway']);

        $params = [
            'pid' => $config['pid'],
            'type' => $payway,
            'out_trade_no' => $out_trade_no,
            'notify_url' => $this->url_notify,
            'return_url' => $this->url_return,
            'name' => $subject,
            'money' => $amount,
            'sitename' => SYS_NAME
        ];

        $params['sign_type'] = 'MD5';
        $params['sign'] = $this->getSign($params, $params['sign_type'], $config['key']);

        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $config['gateway'] . '/submit.php" method="post">
    <input type="hidden" name="pid" value="' . $params['pid'] . '">
    <input type="hidden" name="type" value="' . $params['type'] . '">
    <input type="hidden" name="out_trade_no" value="' . $params['out_trade_no'] . '">
    <input type="hidden" name="notify_url" value="' . $params['notify_url'] . '">
    <input type="hidden" name="return_url" value="' . $params['return_url'] . '">
    <input type="hidden" name="name" value="' . $params['name'] . '">
    <input type="hidden" name="money" value="' . $params['money'] . '">
    <input type="hidden" name="sitename" value="' . $params['sitename'] . '">
    <input type="hidden" name="sign_type" value="' . $params['sign_type'] . '">
    <input type="hidden" name="sign" value="' . $params['sign'] . '">
</form>
</body>
        ');
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'http://pay.tgyzf.cn/';
        }

        if ($isNotify) {

            $can = $_GET;
            $data = [
                'pid' => $config['pid'],
                'trade_no' => $can['trade_no'],
                'out_trade_no' => $can['out_trade_no'],
                'type' => $can['type'],
                'name' => $can['name'],
                'money' => $can['money'],
                'trade_status' => $can['trade_status']
            ];
            $data['sign_type'] = 'MD5';
            $data['sign'] = $this->getSign($data, $data['sign_type'], $config['key']);

            if ($data['sign'] === $can['sign']) {
                echo "success";
                $successCallback($data['out_trade_no'], (int)round($data['money'] * 100), $data['trade_no']);
                return true;
            } else {
                echo "error sign";
                return false;
            }
        } else {

            if (empty($config['out_trade_no'])) {
                // return page
                $can = $_GET;
                $data = [
                    'pid' => $config['pid'],
                    'trade_no' => $can['trade_no'],
                    'out_trade_no' => $can['out_trade_no'],
                    'type' => $can['type'],
                    'name' => $can['name'],
                    'money' => $can['money'],
                    'trade_status' => $can['trade_status']
                ];
                $data['sign_type'] = 'MD5';
                $data['sign'] = $this->getSign($data, $data['sign_type'], $config['key']);

                if ($data['sign'] === $can['sign']) {
                    $successCallback($data['out_trade_no'], (int)round($data['money'] * 100), $data['trade_no']);
                    return true;
                } else {
                    return false;
                }
            } else {
                // 主动查询

                $params = [
                    'act' => 'order',           // 操作类型
                    'pid' => $config['pid'],    // 商户ID
                    'key' => $config['key'],    // 商户密钥
                    'out_trade_no' => $config['out_trade_no'], // 商户订单号
                ];
                $ret_raw = CurlRequest::get($config['gateway'] . 'api.php?' . http_build_query($params));
                $data = @json_decode($ret_raw, true);
                if (!$data || !isset($data['code'])) {
                    Log::error('Pay.EPay.query error#1', ['ret_raw' => $ret_raw]);
                    return false;
                }
                if (@$data['status'] === '1') {
                    $successCallback($data['out_trade_no'], (int)round($data['money'] * 100), $data['trade_no']);
                    return true;
                }
                return false;
            }
        }
    }

    /**
     * 退款操作
     * @param array $config 支付渠道配置
     * @param string $order_no 订单号
     * @param string $pay_trade_no 支付渠道流水号
     * @param int $amount_cent 金额/分
     * @return true|string true 退款成功  string 失败原因
     */
    function refund($config, $order_no, $pay_trade_no, $amount_cent)
    {
        return '此支付渠道不支持发起退款, 请手动操作';
    }
}