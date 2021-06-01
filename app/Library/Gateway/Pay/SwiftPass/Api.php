<?php

/**
 * 威富通, 支持的方式 ALIPAY/WECHAT/QQ/UNIONPAY
 * https://open.swiftpass.cn/
 */

namespace Gateway\Pay\SwiftPass;

use App\Library\Helper;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

require('Utils.class.php');
require('class/RequestHandler.class.php');
require('class/ClientResponseHandler.class.php');
require('class/PayHttpClient.class.php');

/**
 * Class Api
 * @package Gateway\Pay\SwiftPass
 */
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

    private function check_config($config)
    {
        if (!isset($config['url'])) {
            throw new \Exception('请填写支付请求地址[url]');
        }
        if (!isset($config['mchId'])) {
            throw new \Exception('请填写商户号[mchId]');
        }
        if (!isset($config['sign_type'])) {
            throw new \Exception('请填写签名方式[sign_type], 可选 RSA_1_256 ');
        }

        if ($config['sign_type'] === 'MD5') { // 这里不知道有没有
            if (!isset($config['key'])) {
                throw new \Exception('请填写商户密钥[key]');
            }
        }
        if ($config['sign_type'] === 'RSA_1_1' || $config['sign_type'] === 'RSA_1_256') {
            if (!isset($config['public_rsa_key'])) {
                throw new \Exception('RSA验签平台公钥[public_rsa_key]');
            }
            if (!isset($config['private_rsa_key'])) {
                throw new \Exception('RSA验签平台公钥[private_rsa_key]');
            }
        }
    }

    /** @var \ClientResponseHandler $resHandler */
    private $resHandler = null;
    /** @var \RequestHandler $reqHandler */
    private $reqHandler = null;
    /** @var \PayHttpClient $pay */
    private $pay = null;

    private function request($config)
    {
        $this->check_config($config);

        $req = new \RequestHandler();
        $req->setGateUrl($config['url']);
        $req->setSignType($config['sign_type']);
        if ($config['sign_type'] == 'MD5') {
            $req->setKey($config['key']);

        } else if ($config['sign_type'] == 'RSA_1_1' || $config['sign_type'] == 'RSA_1_256') {
            $req->setRSAKey($config['private_rsa_key']);
        }
        return $req;
    }

    private function response($config)
    {
        $this->check_config($config);

        $res = new \ClientResponseHandler();
        if ($config['sign_type'] == 'MD5') {
            $res->setKey($config['key']);
        } else if ($config['sign_type'] == 'RSA_1_1' || $config['sign_type'] == 'RSA_1_256') {
            $res->setRSAKey($config['public_rsa_key']);
        }
        return $res;
    }


    /**
     * @param array $config 支付渠道配置
     * @param string $out_trade_no 外部订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     * @throws \Alipay\Exception\AlipayErrorResponseException
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {

        $this->check_config($config);

        $req = $this->request($config);
        $req->setReqParams([
            // 统一扫码(unified.trade.native), 支付宝扫码(pay.alipay.native), 银联钱包扫码(pay.unionpay.native)
            'service' => 'unified.trade.native',
            'version' => '2.0',
            'sign_type' => $config['sign_type'],
            'mch_id' => $config['mchId'],
            'nonce_str' => mt_rand(), // 随机字符串，必填项，不长于 32 位

            'out_trade_no' => $out_trade_no,
            'body' => $subject,
            'attach' => 'none',
            'total_fee' => $amount_cent,
            'mch_create_ip' => Helper::getIP(),
            'notify_url' => $this->url_notify, // 通知地址，必填项，接收平台通知的URL，需给绝对路径，255字符内格式如:http://wap.tenpay.com/tenpay.asp
        ]);

        $req->createSign(); // 创建签名

        $data = \Utils::toXml($req->getAllParameters());

        $client = new \PayHttpClient();
        $client->setReqContent($req->getGateURL(), $data);

        if (!$client->call()) {
            Log::error('Pay.SwiftPass.goPay Error', [
                'ResponseCode' => $client->getResponseCode(),
                'ErrorInfo' => $client->getErrInfo()
            ]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        $res = $this->response($config);
        $res->setContent($client->getResContent());
        $res->setKey($req->getKey());
        if (!$res->isTenpaySign()) {
            Log::error('Pay.SwiftPass.goPay Error#2', [
                'status' => $res->getParameter('status'),
                'message' => $res->getParameter('message')
            ]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        //当返回状态与业务结果都为0时才返回支付二维码，其它结果请查看接口文档
        if (!$res->getParameter('status') == 0 && $res->getParameter('result_code') == 0) {
            Log::error('Pay.SwiftPass.goPay Error#2', [
                'err_code' => $res->getParameter('err_code'),
                'err_msg' => $res->getParameter('err_msg')
            ]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        switch (strtoupper($config['payway'])) {
            case 'ALIPAY':
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($res->getParameter('code_url')));
                break;
            case 'WECHAT':
                header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($res->getParameter('code_url')));
                break;
            case 'QQ':
                header('location: /qrcode/pay/' . $out_trade_no . '/qq?url=' . urlencode($res->getParameter('code_url')));
                break;
            case 'UNIONPAY':
                header('location: /qrcode/pay/' . $out_trade_no . '/unionpay?url=' . urlencode($res->getParameter('code_url')));
                break;
            default:
                throw new \Exception('支付方式错误');
        }

        exit;
    }

    /**
     * @param $config
     * @param callable $successCallback 成功回调 (系统单号,交易金额/分,支付渠道单号)
     * @return bool|string true 验证通过  string 失败原因
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {
            // post

            $xml = file_get_contents('php://input');
            $res = $this->response($config);
            $res->setContent($xml);
            if ($res->isTenpaySign()) {
                if ($res->getParameter('status') == 0 && $res->getParameter('result_code') == 0) {
                    echo 'success';

                    $result = $res->getAllParameters();
                    $trade_no = $result['transaction_id']; //威富通交易号
                    $total_fee = (int)$result['total_fee'];
                    $successCallback($result['out_trade_no'], $total_fee, $trade_no);
                    return true;

                } else {
                    echo 'failure1';
                    return false;
                }
            } else {
                echo 'failure2';
                return false;
            }
        }

        if (!empty($config['out_trade_no'])) {
            // payReturn(带订单号) or 扫码主动查询 or 查询页面点击支付 先查询一下
            $req = $this->request($config);
            $req->setReqParams([
                'service' => 'unified.trade.query',
                'version' => '2.0',
                'sign_type' => $config['sign_type'],
                'mch_id' => $config['mchId'],
                'nonce_str' => mt_rand(), // 随机字符串，必填项，不长于 32 位
                'out_trade_no' => $config['out_trade_no'] // 商户订单号
            ]);
            $req->createSign();//创建签名
            $data = \Utils::toXml($req->getAllParameters());
            $client = new \PayHttpClient();
            $client->setReqContent($req->getGateURL(), $data);
            if (!$client->call()) {
                Log::error('Pay.SwiftPass.goPay Error', [
                    'ResponseCode' => $client->getResponseCode(),
                    'ErrorInfo' => $client->getErrInfo()
                ]);
                throw new \Exception('获取查询信息超时, 请刷新重试');
            }
            $res = $this->response($config);
            $res->setContent($client->getResContent());
            $res->setKey($req->getKey());
            if (!$res->isTenpaySign()) {
                Log::error('Pay.SwiftPass.goPay Error#2', [
                    'status' => $res->getParameter('status'),
                    'message' => $res->getParameter('message')
                ]);
                throw new \Exception('获取查询信息超时, 请刷新重试');
            }

            $result = $res->getAllParameters();
            if ($result['trade_state'] === 'SUCCESS') {
                $trade_no = $result['transaction_id']; //威富通交易号
                $total_fee = (int)$result['total_fee'];
                $successCallback($result['out_trade_no'], $total_fee, $trade_no);
                return true;
            }
        } else {
            //  支付完返回的地址
            // http://127.0.0.4/pay/return/4?xx
            // 这个支付没有同步返回
            return false;
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
     * @throws \Throwable
     */
    function refund($config, $order_no, $pay_trade_no, $amount_cent)
    {

        $req = $this->request($config);
        $req->setReqParams([
            'service' => 'unified.trade.refund',
            'version' => '2.0',
            'sign_type' => $config['sign_type'],
            'mch_id' => $config['mchId'],
            'nonce_str' => mt_rand(), // 随机字符串，必填项，不长于 32 位

            'op_user_id' => $config['mchId'], // 必填项，操作员帐号,默认为商户号
            'out_trade_no' => $config['out_trade_no'] // 商户订单号
        ]);
        $req->createSign();//创建签名
        $data = \Utils::toXml($req->getAllParameters());
        $client = new \PayHttpClient();
        $client->setReqContent($req->getGateURL(), $data);
        if (!$client->call()) {
            Log::error('Pay.SwiftPass.goPay Error', [
                'ResponseCode' => $client->getResponseCode(),
                'ErrorInfo' => $client->getErrInfo()
            ]);
            throw new \Exception('获取退款信息超时, 请刷新重试');
        }
        $res = $this->response($config);
        $res->setContent($client->getResContent());
        $res->setKey($req->getKey());
        if (!$res->isTenpaySign()) {
            Log::error('Pay.SwiftPass.goPay Error#2', [
                'status' => $res->getParameter('status'),
                'message' => $res->getParameter('message')
            ]);
            throw new \Exception('获取退款信息超时, 请刷新重试');
        }

        if ($res->getParameter('status') == 0 && $res->getParameter('result_code') == 0) {
            // 退款成功
            return true;
        }
        return '退款失败，请查看日志';
    }
}