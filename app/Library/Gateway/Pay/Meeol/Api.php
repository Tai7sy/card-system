<?php

namespace Gateway\Pay\Meeol;

use App\Library\CurlRequest;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    /**
     * 需要传入支付方式ID (因为一个支付方式可能添加了多次)
     * ApiInterface constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
    }

    /**
     * @param array $config 支付渠道配置
     * @param string $out_trade_no 外部订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $total_fee = sprintf('%.2f', $amount_cent / 100); // 元为单位

        if (!isset($config['appId'])) {
            throw new \Exception('请设置appId');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请设置key');
        }
        $payway = $config['payway'];
        $req = [
            'amount' => $total_fee,
            'appId' => $config['appId'],
            'orderId' => $out_trade_no,
            'random' => md5(random_bytes(16)),
            'tradeType' => $payway, // 微信W01，支付宝A01
        ];
        $req['sign'] = strtoupper(md5('amount=' . $req['amount'] . '&appId=' . $config['appId'] . '&key=' . $config['key'] . '&orderId=' . $req['orderId'] . '&random=' . $req['random'] . '&tradeType=' . $req['tradeType']));
        $ret_raw = CurlRequest::post('http://api.meeol.cn/rest/mall/payment/order', json_encode($req));
        $ret = json_decode($ret_raw, true);
        if (!isset($ret['status']) || $ret['status'] !== '0') {
            Log::error('Pay.Meeol.goPay.order Error: ' . $ret_raw);
            throw new \Exception('支付请求失败, 请刷新重试');
        }
        if (substr($payway, 0, 1) === 'W')
            header('Location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($ret['qrcode']));
        elseif (substr($payway, 0, 1) === 'A')
            header('Location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($ret['qrcode']));
        exit;
    }

    /**
     * @param $config
     * @param callable $successCallback 成功回调 (系统单号,交易金额/分,支付渠道单号)
     * @return bool|string true 验证通过  string 失败原因
     * @throws \Exception
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {
            $post = json_decode(file_get_contents('php://input'), true);
            $sign = strtoupper(md5('amount=' . $post['amount'] . '&appid=' . $post['appid'] . '&key=' . $config['key'] . '&orderId=' . $post['orderId'] . '&tradeTime=' . $post['tradeTime'] . '&tradeType=' . $post['tradeType']));

            if ($sign === $post['sign']) {
                $total_fee = (int)round($post['amount'] * 100);
                $successCallback($post['orderId'], $total_fee, $post['passTradeNo']);
                echo 'success';
                return true;
            } else {
                Log::error('Pay.Meeol.verify notify sign error, post: ' . file_get_contents('php://input'));
                echo 'error';
            }
        } else {
            if (!empty($config['out_trade_no'])) {
                $req = [
                    'appId' => $config['appId'],
                    'orderId' => $config['out_trade_no'],
                    'random' => md5(random_bytes(16)),
                ];
                $req['sign'] = strtoupper(md5('appId=' . $config['appId'] . '&key=' . $config['key'] . '&orderId=' . $req['orderId'] . '&random=' . $req['random']));
                $req = json_encode($req);
                $ret_raw = CurlRequest::post('http://api.meeol.cn/rest/mall/payment/query', $req);
                $ret = json_decode($ret_raw, true);
                if (!isset($ret['status'])) {
                    Log::error('Pay.Meeol.verify Error: ' . $ret_raw);
                }
                if ($ret['status'] === '0') {
                    $total_fee = (int)round($ret['amount'] * 100);
                    $successCallback($ret['orderId'], $total_fee, $ret['passTradeNo']);
                    return true;
                }
                Log::debug('Pay.Meeol.verify debug, req:' . $req . 'ret:' . $ret_raw);
                return false;
            } else {
                throw new \Exception('请传递订单编号');
            }
        }
        return false;
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