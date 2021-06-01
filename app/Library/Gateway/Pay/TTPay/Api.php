<?php

namespace Gateway\Pay\TTPay;

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

    private function getSign($data, $secret)
    {
        if (!is_null($secret)) {
            unset($data['sign']);
            ksort($data);
            $data['signKey'] = $secret;
            $str = json_encode($data);
            return sha1($str);
        }
        return false;
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
            $config['gateway'] = 'http://api.ttpays.cn/';
        }
        if (!isset($config['app_id'])) {
            throw new \Exception('请填写app_id');
        }
        if (!isset($config['secret'])) {
            throw new \Exception('请填写secret');
        }

        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位

        // alipay:支付宝,wxpay:微信支付,qqpay:QQ钱包
        $payway = $config['payway'];

        $params = [
            'service' => $payway,
            'app_id' => $config['app_id'],
            'out_trade_no' => $out_trade_no,
            'subject' => $subject,
            'return_url' => $this->url_return,
            'amount' => $amount
        ];

        if ($payway === 'wx.h5') {
            $params['return_url'] = SYS_URL . '/qrcode/pay/' . $out_trade_no . '/query';
        }

        $params['sign'] = $this->getSign($params, $config['secret']);

        $ret_raw = CurlRequest::post($config['gateway'] . '/api/v1/create', http_build_query($params));
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['code']) || $ret['code'] !== 0) {
            Log::error('Pay.TTPay.goPay.order, request failed: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        if ($ret['data']['status_code'] !== 0) {
            Log::error('Pay.TTPay.goPay.order, request failed #2: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        switch ($config['payway']) {
            case 'alipay.native':
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($ret['data']['result']));
                break;
            case 'wx.native':
            case 'wx.jspay':
            case 'wx.h5':
                header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($ret['data']['result']));
                break;
            case 'wx.h5_real':
                header('location: ' . $ret['data']['result']);
                break;
            default:
                throw new \Exception('支付方式错误');
        }
        exit;
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'http://api.ttpays.cn/';
        }

        if ($isNotify) {

            $can = $_REQUEST;
            $data = [
                'trade_no' => $can['trade_no'],
                'out_trade_no' => $can['out_trade_no'],
                'amount' => $can['amount'],
                'status' => $can['status']
            ];
            $data['sign'] = $this->getSign($data, $config['secret']);

            if ($data['sign'] === $can['sign']) {
                echo "success";
                $successCallback($data['out_trade_no'], (int)round($data['amount'] * 100), $data['trade_no']);
                return true;
            } else {
                echo "error sign";
                return false;
            }
        } else {

            if (empty($config['out_trade_no'])) {
                // return page
                $can = $_REQUEST;
                $data = [
                    'trade_no' => $can['trade_no'],
                    'out_trade_no' => $can['out_trade_no'],
                    'amount' => $can['amount'],
                    'status' => $can['status']
                ];
                $data['sign'] = $this->getSign($data, $config['secret']);

                if ($data['sign'] === $can['sign']) {
                    $successCallback($can['out_trade_no'], (int)round($can['amount'] * 100), $can['trade_no']);
                    return true;
                } else {
                    return false;
                }
            } else {
                // 主动查询

                $params = [
                    'app_id' => $config['app_id'],    // 商户ID
                    'out_trade_no' => $config['out_trade_no'], // 商户订单号
                ];
                $params['sign'] = $this->getSign($params, $config['secret']);

                $ret_raw = CurlRequest::post($config['gateway'] . '/api/v1/query', http_build_query($params));
                $json = @json_decode($ret_raw, true);
                if (!$json || !isset($json['code'])) {
                    Log::error('Pay.TTPay.query error#1: ' . $ret_raw);
                    return false;
                }
                if (@$json['data']['status'] === 'FINISHED') {
                    $successCallback($json['data']['out_trade_no'], (int)round($json['data']['amount'] * 100), $json['data']['trade_no']);
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