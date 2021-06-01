<?php

/**
 * 数游支付
 * 2020-04-25 14:21:22
 */

namespace Gateway\Pay\ShuYouPay;

use App\Library\CurlRequest;
use App\Library\Helper;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/**
 * Class Api
 * @package Gateway\Pay\ShuYouPay
 */
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
        $total_fee = strval($amount_cent / 100); // 元为单位


        if (!isset($config['id'])) {
            throw new \Exception('请设置[id]');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请设置[key]');
        }
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'http://api.zhangyishou.com';
        }

        $data = array(
            'MerchantId' => (int)$config['id'],
            'DownstreamOrderNo' => $out_trade_no,
            'OrderTime' => date('Y-m-d H:i:s'),
            'PayChannelId' => (int)$config['payway'], // 支付宝6 微信4
            'AsynPath' => $this->url_notify,
            'ReturnUrl' => $this->url_return . '/' . $out_trade_no,
            'OrderMoney' => $total_fee,
            'MD5Sign' => '',
            'Mproductdesc' => $subject,
            'IPPath' => Helper::getIP() ?? '127.0.0.1',
        );

        foreach ($data as $key => $value) {
            if ($key !== 'MD5Sign' && $key !== 'Mproductdesc' && $key !== 'ReturnUrl') {
                $data['MD5Sign'] .= $value;
            }
        }
        $data['MD5Sign'] = md5($data['MD5Sign'] . $config['key']);


        $ret_raw = CurlRequest::post($config['gateway'] . '/api/Order/' . (intval($config['payway']) === 6 ? 'AddOrder' : 'SYWxPayYL'), json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), [
            'Content-Type' => 'application/json'
        ]);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['Code']) || $ret['Code'] !== '1009') {
            Log::error('Pay.ShuYouPay.goPay.order, request failed', ['response' => $ret_raw]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        switch (intval($config['payway'])) {
            case 6:
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($ret['Info']));
                break;
            case 4:
                header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($ret['Info']));
                break;
            default:
                throw new \Exception('支付方式错误');
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
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'http://api.zhangyishou.com';
        }

        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {

            $data = @json_decode(file_get_contents('php://input'), true) ?? [];
            if (!empty($data['Signature']) && $data['Signature'] === md5($config['id'] . $data['DownstreamOrderNo'] . $config['key'])) {
                $order_no = $data['DownstreamOrderNo']; // 本系统内订单号
                $total_fee = (int)round((float)$data['OrderMoney'] * 100);
                $pay_trade_no = $data['OrderNo']; //支付流水号
                $successCallback($order_no, $total_fee, $pay_trade_no);

                echo 'OK';
                return true;
            }

            echo 'FAILED';
            return false;
        } else {

            // 用于payReturn支付返回页面第二种情况(传递了out_trade_no), 或者重新发起支付之前检查一下, 或者二维码支付页面主动请求
            // 主动查询交易结果
            if (!empty($config['out_trade_no'])) {

                // 进行一些查询逻辑
                $data = [
                    'MerchantId' => (int)$config['id'],
                    'OrderId' => $config['out_trade_no'], //商户订单号
                    'MD5Sign' => md5($config['id'] . $config['out_trade_no'] . $config['key'])
                ];
                $ret_raw = CurlRequest::post($config['gateway'] . '/api/QueryOrder/QueryOrder', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), [
                    'Content-Type' => 'application/json'
                ]);
                $ret = @json_decode($ret_raw, true);
                if (!$ret || !isset($ret['Code'])) {
                    Log::error('Pay.ShuYouPay.verify, request failed', ['response' => $ret_raw]);
                    throw new \Exception('验证支付信息超时, 请刷新重试');
                }

                if ($ret['Code'] === '5000' && $ret['Info']['OrderState'] === 1) {
                    $order_no = $ret['Info']['OrderId']; // 本系统内订单号
                    $total_fee = (int)round((float)$ret['Info']['OrderMoney'] * 100);
                    $pay_trade_no = $ret['Info']['TransactionId']; //支付流水号
                    $successCallback($order_no, $total_fee, $pay_trade_no);
                    return true;
                }
                return false;
            }


            // 这里可能是payReturn支付返回页面的第一种情况, 支付成功后直接返回, config里面没有out_trade_no
            // 这里的URL, $_GET 里面可能有订单参数用于校验订单是否成功(参考支付宝的AliAop逻辑)
            if (0) { // 进行一些校验逻辑, 如果检查通过
                // 扫码, 没有此种情况
                return true;
            }
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
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'http://api.zhangyishou.com';
        }
        $total_fee = strval($amount_cent / 100); // 元为单位

        // 进行一些查询逻辑
        $data = [
            'MerchantId' => (int)$config['id'],
            'OrderNo' => $pay_trade_no, // 我方订单号必传
            'RefundAmount' => $total_fee,
            'MD5Sign' => md5($config['id'] . $pay_trade_no . $config['key'])
        ];
        $ret_raw = CurlRequest::post($config['gateway'] . '/api/OrderRefund/Refund', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), [
            'Content-Type' => 'application/json'
        ]);
        $ret = @json_decode($ret_raw, true);
        if (@$ret['Code'] === '1000') {
            return true;
        }

        return $ret_raw;
    }
}