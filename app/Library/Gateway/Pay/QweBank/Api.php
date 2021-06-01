<?php

namespace Gateway\Pay\QweBank;

use App\Library\CurlRequest;
use App\Library\Helper;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

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

    function getAccessToken($config)
    {
        $nonce = str_random(5);
        $timestamp = time() . '00';
        $sign = strtoupper(md5('merchantNo=' . $config['mchid'] . '&nonce=' . $nonce . '&timestamp=' . $timestamp . '&key=' . $config['key']));
        //print_r('商户号'.$config['mchid'].'密钥'.$config['key'].'随机字符'.$nonce.'时间戳'.$timestamp.'签名'.$sign);
        $params = json_encode([
            'merchantNo' => $config['mchid'],   //商户号
            'key' => $config['key'],            //密钥
            'nonce' => $nonce,                  //随机字符（与获取签名时的保持一致）
            'timestamp' => $timestamp,          //时间戳（与获取签名时的保持一致）
            'sign' => $sign                     //签名（大写，详情见1.5）
        ], JSON_FORCE_OBJECT);

        $ret_raw = CurlRequest::post('http://api.qwebank.top/open/v1/getAccessToken/merchant', $params, [
            'Content-Type' => 'application/json'
        ]);
        $ret = @json_decode($ret_raw, true);
        if (!is_array($ret) || !isset($ret['value']) || !isset($ret['value']['accessToken'])) {
            Log::error('Pay.QweBank.getAccessToken Error: ' . $ret_raw);
            throw new \Exception('获取支付渠道失败，请联系客服反馈');
        }
        return $ret['value']['accessToken'];
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
        if (!isset($config['mchid'])) {
            throw new \Exception('请填写 mchid');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写 key');
        }
        $payway = $config['payway']; // alipayWapPay, alipayScan, wechatScan, wechatWapPay, qqScan, jdPay, bankPay, unionpayScan

        $params = [
            'accessToken' => $this->getAccessToken($config),
            'param' => [
                'outTradeNo' => $out_trade_no,
                'money' => $amount_cent,
                'type' => 'T0',
                'body' => $subject,
                'detail' => $subject,
                'notifyUrl' => $this->url_notify,
                'successUrl' => $this->url_return,
                'productId' => '12313',
                'timestamp' => strval(time()),
                'sign' => '1'
            ]
        ];

        if ($payway === 'wechatWapPay') {
            $params['param']['merchantIp'] = Helper::getIP();
        }
        if ($payway === 'bankPay') {
            if (!isset($config['bankName'])) {
                throw new \Exception('请填写 bankName');
            }
            $params['param']['bankName'] = $config['bankName'];
        }
        $ret_raw = CurlRequest::post('http://api.qwebank.top/open/v1/order/' . $payway,
            json_encode($params, JSON_FORCE_OBJECT), [
                'Content-Type' => 'application/json'
            ]);
        $ret = @json_decode($ret_raw, true);
        if (!is_array($ret) || !isset($ret['success']) || !isset($ret['value'])) {
            Log::error('Pay.QweBank.goPay Error: ' . $ret_raw);
            throw new \Exception('提交订单到支付渠道失败，请联系客服反馈');
        }

        header('Location: ' . $ret['value']);
        exit;
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        $result = false;

        if ($isNotify) {
            $str = 'merchantNo=' . $_REQUEST['merchantNo'] .
                '&no=' . $_REQUEST['no'] .
                '&nonce=' . $_REQUEST['nonce'] .
                '&timestamp=' . $_REQUEST['timestamp'] .
                '&key=' . $config['key'];
            if (md5($str) !== $_REQUEST['sign']) {
                $result = true;
            }
            echo $result ? 'SUCCESS' : 'fail';
        } else {
            $out_trade_no = $_REQUEST['outTradeNo'];
            $params = [
                'accessToken' => $this->getAccessToken($config),
                'param' => $out_trade_no
            ];
            $ret_raw = CurlRequest::post('http://api.qwebank.top/open/v1/order/getByCustomerNo',
                json_encode($params, JSON_FORCE_OBJECT), [
                    'Content-Type' => 'application/json'
                ]);
            $ret = @json_decode($ret_raw, true);
            if (!is_array($ret) || !isset($ret['success']) || !isset($ret['value'])) {
                Log::error('Pay.QweBank.verify.getByCustomerNo Error: ' . $ret_raw);
                return false;
            }
            $result = $ret['value']['done'] === true;
        }

        if ($result) {
            $out_trade_no = $_REQUEST['outTradeNo'];  // 本系统订单号
            $total_fee = $_REQUEST['money'];
            $pay_order_id = $_REQUEST['no']; // API渠道订单号
            $successCallback($out_trade_no, $total_fee, $pay_order_id);
            return true;
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