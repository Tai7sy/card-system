<?php

/**
 *
 * 未知接口
 * http://www.docway.net/project/3Va1V40iIY/3Va1VjEtcQ
 *
 *
 * 支付方式:
 * 1支付宝 2微信
 *
 */

namespace Gateway\Pay\Unk1Pay;

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

        if (!isset($config['appid'])) {
            throw new \Exception('请设置appid');
        }
        if (!isset($config['appsecret'])) {
            throw new \Exception('请设置appsecret');
        }
        if (!isset($config['gateway'])) {
            throw new \Exception('请设置gateway');
        }

        $data = array(
            'amount' => $amount_cent,
            'payway' => $config['payway'], // 1支付宝 2微信
            'appid' => $config['appid'],
            'sign' => md5($config['appid'] . md5($amount_cent) . md5($config['appsecret'])),
            'extension' => $out_trade_no
        );

        $ret_raw = CurlRequest::post($config['gateway'] . '/api3/needQrcode', http_build_query($data));
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['code']) || $ret['code'] !== 200) {
            Log::error('Pay.Unk1Pay.goPay.order, request failed', ['response' => $ret_raw]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }


        Log::debug('Pay.Unk1Pay.goPay.order', ['response' => $ret]);
        switch (strtoupper($config['payway'])) {
            case 1:
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($ret['data']['qrcode']));
                break;
            case 2:
                header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($ret['data']['qrcode']));
                break;
            default:
                throw new \Exception('支付方式错误');
        }
        exit(0);
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
            if (!isset($_POST['sign'])) {
                return false;
            }

            Log::debug('Pay.Unk1Pay.verify', ['$_POST' => $_POST]);

            if ($_POST['sign'] === md5($config['appid'] . md5($_POST['orderid']) . $config['appsecret'])) {
                $successCallback($_POST['extension'], (int)$_POST['amount'], $_POST['orderid']);
                echo 'success';
                return true;
            }

            echo 'fail';
            return false;
        } else {
            // 主动查询交易结果
            if (!empty($config['out_trade_no'])) {
                $order_no = @$config['out_trade_no'];  //商户订单号
                return false; // 不支持
            }
            // 暂无
            return false;
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