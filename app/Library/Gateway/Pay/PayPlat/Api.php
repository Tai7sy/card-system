<?php

/**
 * PayPlat支付
 * https://www.showdoc.cc/payplat?page_id=2948068161991484
 * 2020年3月20日
 */

namespace Gateway\Pay\PayPlat;

use App\Library\CurlRequest;
use Gateway\Pay\ApiInterface;
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
        if (!isset($config['inst_no'])) {
            throw new \Exception('请填写机构号 [inst_no]');
        }
        if (!isset($config['mch_no'])) {
            throw new \Exception('请填写商户号 [mch_no]');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写key');
        }
        if (!isset($config['gateway'])) {
            $gateway = 'http://wps.open.payplat.cn/openpay';
        } else {
            $gateway = $config['gateway'];
        }

        if (isset($config['type']) && $config['type'] === 'JSAPI') {
            if (!isset($_GET['openid'])) {
                $pay_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . '/pay/' . $out_trade_no;

                if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                    // 微信内部 直接跳转获取
                    // 获取openid
                    $params = [
                        'inst_no' => $config['inst_no'],  // 机构号 ，分配
                        'mch_no' => $config['mch_no'],  // 商户号
                        'redirect_uri' => $pay_url
                    ];
                    $params['sign'] = $this->getSign($params, $config['key']);
                    header('Location: ' . $gateway . '/jsapi/getauth2?' . http_build_query($params));
                    exit;
                } else {
                    // 微信外部, 让用户扫码, $pay_url = 当前网页
                    header('Location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($pay_url));
                    exit;
                }
            }
        }


        /**
         * 300 支付宝，
         * 400 微信
         * 500 口碑
         */
        $payway = $config['payway'];
        switch ($payway) {
            case '300':
            case '500':
                $bankType = 'aliqr'; // 支付宝扫码
                break;
            case '400':
                $bankType = 'wechat';
                break;
            default:
                throw new \Exception('支付渠道错误');
        }

        $params = [
            'inst_no' => $config['inst_no'],  // 机构号 ，分配
            'mch_no' => $config['mch_no'],  // 商户号
            'pay_type' => $payway,
            'pay_trace_no' => $out_trade_no,  // 商户系统内订单号
            'pay_time' => date('YmdHis'), // 请求支付时间，yyyyMMddHHmmss格式
            'total_amount' => $amount_cent, // 支付金额，单位：分
            'order_body' => $subject, // 订单描述
            'notify_url' => $this->url_notify,
        ];

        $path = '/v3/prepay';
        if (isset($config['type']) && $config['type'] === 'JSAPI') {
            $params['open_id'] = $_GET['openid'];
            $path = '/v3/jspay';
        }

        $params['sign'] = $this->getSign($params, $config['key']);
        $ret_raw = CurlRequest::post($gateway . $path, json_encode($params), ['Content-Type' => 'application/json']);

        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['return_code'])) {
            Log::error('Pay.PayPlat.goPay error#1: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请联系客服反馈');
        }

        if ($ret['return_code'] !== 'SUCCESS') {
            Log::error('Pay.PayPlat.goPay error#2: ' . $ret_raw);
            throw new \Exception('获取付款信息失败, 请返回重新下单');
        }

        if (isset($config['type']) && $config['type'] === 'JSAPI') {
            // 微信JSAPI
            // 已经保证在微信内部了
            $params = [
                'appId' => $ret['appId'],                // 公众号名称，由商户传入
                'timeStamp' => $ret['timeStamp'],        // 时间戳，自1970年以来的秒数
                'nonceStr' => $ret['nonceStr'],          // 随机串
                'package' => $ret['packages'],
                'signType' => $ret['signType'],          // 微信签名方式：
                'paySign' => $ret['paySign']             // 微信签名
            ];
            header('Location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode(json_encode($params)));
        } else {
            // 普通方式, 支付宝 or 微信扫码
            header('Location: /qrcode/pay/' . $out_trade_no . '/' . strtolower($bankType) . '?url=' . urlencode($ret['qrcode']));
        }
        exit;
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
        if (!isset($config['gateway'])) {
            $gateway = 'http://wps.open.payplat.cn/openpay';
        } else {
            $gateway = $config['gateway'];
        }

        if ($isNotify) {

            $post_raw = file_get_contents('php://input');
            $params = @json_decode($post_raw, true) ?? [];

            if (empty($params['sign']) ||
                $params['sign'] !== $this->getSign($params, $config['key']) ||
                $params['result_code'] !== 'PAY_SUCCESS'
            ) {
                Log::error('Pay.PayPlat.verify, sign error $post:' . $post_raw);
                echo 'error';
                return false;
            }

            $successCallback($params['pay_trace_no'], (int)$params['total_amount'], $params['trade_no']);

            echo '{
    "return_code": "SUCCESS",
    "return_msg": "成功"
}';
            return true;
        } else {
            $out_trade_no = @$config['out_trade_no'];
            if (strlen($out_trade_no) < 5) {
                // 此驱动不支持return_url返回通过url参数校验, 必须传入订单号
                throw new \Exception('交易单号未传入');
            }

            $params = [
                'inst_no' => $config['inst_no'],  // 机构号 ，分配
                'mch_no' => $config['mch_no'],  // 商户号
                'query_trace_no' => date('YmdHis'),  // 查询请求流水号 ??? 为什么要这个??
                'pay_trace_no' => $out_trade_no, // 支付时传入的单号
            ];
            $params['sign'] = $this->getSign($params, $config['key']);

            $ret_raw = CurlRequest::post($gateway . '/v3/queryorder', json_encode($params), ['Content-Type' => 'application/json']);
            $ret = @json_decode($ret_raw, true);
            if (!$ret || !isset($ret['return_code'])) {
                Log::error('Pay.PayPlat.refund Error#1: ' . $ret_raw);
                return false;
            }

            if ($ret['return_code'] !== 'SUCCESS' || $ret['trade_state'] !== 'SUCCESS') {
                return false;
            }

            $successCallback($ret['pay_trace_no'], (int)$ret['total_amount'], $ret['trade_no']);
            return true;
        }
    }

    private function getSign($params, $key)
    {
        ksort($params);
        $tmp = array();
        foreach ($params as $k => $v) {
            // 参数为空不参与签名
            if ($k !== 'sign' && !is_array($v)) {
                array_push($tmp, "$k=$v");
            }
        }
        $params = implode('&', $tmp);
        $sign_data = $params . '&key=' . $key;
        return strtolower(md5($sign_data));
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
        if (!isset($config['gateway'])) {
            $gateway = 'http://wps.open.payplat.cn/openpay';
        } else {
            $gateway = $config['gateway'];
        }
        $params = [
            'inst_no' => $config['inst_no'],  // 机构号 ，分配
            'mch_no' => $config['mch_no'],  // 商户号
            'refund_trace_no' => $order_no,  // 系统方退款订单号, 唯一
            'refund_fee' => $amount_cent,  // 退款金额，单位：分
            'pay_time' => date('YmdHis'), // 请求退款时间
            'pay_trace_no' => $order_no, // 支付时传入的单号
        ];
        $params['sign'] = $this->getSign($params, $config['key']);
        $ret_raw = CurlRequest::post($gateway . '/v3/refundorder', json_encode($params), ['Content-Type' => 'application/json']);

        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['return_code'])) {
            Log::error('Pay.PayPlat.refund error#1: ' . $ret_raw);
            return '获取退款信息超时, 请重试:' . $ret_raw;
        }

        if ($ret['return_code'] !== 'SUCCESS' || $ret['result_code'] !== 'PAY_SUCCESS') {
            Log::error('Pay.PayPlat.refund error#2: ' . $ret_raw);
            return '获取退款信息失败, 请重试: ' . $ret['return_msg'];
        }
        return true;
    }
}