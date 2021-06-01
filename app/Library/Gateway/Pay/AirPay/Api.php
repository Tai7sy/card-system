<?php

/**
 * AirPay聚合支付
 * https://docs.airpayx.com/web/#/3?page_id=20
 *
 * payway
 *  104 = 支付宝
 *  102 = 微信
 */

namespace Gateway\Pay\AirPay;

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
        if (!isset($config['id'])) {
            throw new \Exception('请填写id');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写key');
        }
        if (!isset($config['gateway'])) {
            $gateway = 'https://gateway.airpayx.com';
        } else {
            $gateway = $config['gateway'];
        }
        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位
        $payway = $config['payway'];
        switch ($payway) {
            case '102':
                $bankType = 'wechat';
                break;
            case '104':
                $bankType = 'aliqr'; // 支付宝扫码
                break;
            default:
                throw new \Exception('支付渠道错误');

        }

        $return_url = SYS_URL . '/qrcode/pay/' . $out_trade_no . '/query';
        $params = [
            'appid' => $config['id'],  // 商户id,由恒隆支付分配
            'out_trade_no' => $out_trade_no,  // 网站订单号
            'total_amount' => $amount, // 单位元（人民币）
            'timestamp' => date('Y-m-d H:i:s'), // 时间格式
            'bankcode' => $payway,
            'notifyurl' => $this->url_notify,
            'callbackurl' => $return_url, //这里 是微信 or 支付宝 支付完毕跳转的地址, 轮训等待成功
            'attach' => $out_trade_no,
            'goods_name' => $subject, // 用户自定义商品名称
        ];

        $post_data = $this->getPostData($params, $config['key']) . '&return_type=json';
        $ret_raw = CurlRequest::post($gateway . '/Pay/index', $post_data);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['code'])) {
            Log::error('Pay.AirPay.goPay.order Error#1: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        if ($ret['code'] !== '1000' || !isset($ret['data']['payurl'])) {
            Log::error('Pay.AirPay.goPay.order Error#2: ' . $ret_raw);
            throw new \Exception('获取付款信息失败, 请联系客服反馈');
        }


        header('location: /qrcode/pay/' . $out_trade_no . '/' . strtolower($bankType) . '?url=' . urlencode($ret['data']['payurl']));

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
            $gateway = 'https://gateway.airpayx.com';
        } else {
            $gateway = $config['gateway'];
        }

        if ($isNotify) {
            $sign_column = ['appid', 'out_trade_no', 'total_amount', 'trade_no', 'timestamp', 'attach', 'code', 'msg'];
            $params = [];
            foreach ($sign_column as $column)
                $params[$column] = $_REQUEST[$column];

            if ($this->getSign($params, $config['key']) !== $_REQUEST['sign']) {
                Log::error('Pay.AirPay.verify, sign error $post:' . json_encode($_REQUEST));
                echo 'sign error';
                return false;
            }

            $order_no = $_REQUEST['out_trade_no']; //上行过程中商户系统传入的商户系统订单
            $pay_trade_no = $_REQUEST['trade_no']; //支付流水号
            $successCallback($order_no, (int)round($_REQUEST['total_amount'] * 100), $pay_trade_no);

            echo 'success';
            return true;
        } else {

            // 主动查询

            $params = [
                'appid' => $config['id'],    // 商户ID
                'out_trade_no' => $config['out_trade_no'], // 商户订单号
            ];

            $post_data = $this->getPostData($params, $config['key']);
            $ret_raw = CurlRequest::post($gateway . '/Pay/query', $post_data);
            $ret = @json_decode($ret_raw, true);
            if (!$ret || !isset($ret['code'])) {
                Log::error('Pay.AirPay.goPay.order Error#1: ' . $ret_raw);
                throw new \Exception('查询订单信息超时');
            }

            if (@$ret['trade_state'] === 'SUCCESS') {
                $successCallback($ret['out_trade_no'], (int)round($ret['total_amount'] * 100), $ret['trade_no']);
                return true;
            }

            return false;
        }
    }


    private function getPostData($params, $key)
    {
        ksort($params);
        $tmp = array();
        foreach ($params as $k => $v) {
            // 参数为空不参与签名
            if ($v !== '' && !is_array($v)) {
                array_push($tmp, "$k=$v");
            }
        }
        $params = implode('&', $tmp);
        $sign_data = $params . '&key=' . $key;
        return $params . '&sign=' . strtoupper(md5($sign_data)) . '&sign_type=MD5';
    }

    private function getSign($params, $key)
    {
        ksort($params);
        $tmp = array();
        foreach ($params as $k => $v) {
            // 参数为空不参与签名
            if ($v !== '' && !is_array($v)) {
                array_push($tmp, "$k=$v");
            }
        }
        $params = implode('&', $tmp);
        $sign_data = $params . '&key=' . $key;
        return strtoupper(md5($sign_data));
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
            $gateway = 'https://gateway.airpayx.com';
        } else {
            $gateway = $config['gateway'];
        }
        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位
        $params = [
            'appid' => $config['id'],  // 商户id,由恒隆支付分配
            'out_trade_no' => $order_no,  // 网站订单号
        ];

        $post_data = $this->getPostData($params, $config['key']);
        $ret_raw = CurlRequest::post($gateway . '/Pay/refund', $post_data);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['code'])) {
            Log::error('Pay.AirPay.refund Error#1: ' . $ret_raw);
            return '获取退款信息超时, 请重试';
        }

        if ($ret['code'] !== '1000') {
            Log::error('Pay.AirPay.refund Error#2: ' . $ret_raw);
            return '获取退款信息失败, 请重试';
        }
        return true;
    }
}