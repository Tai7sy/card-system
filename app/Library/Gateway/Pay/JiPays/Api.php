<?php
/**
 * http://pay.jipays.com/
 * 2019年5月3日
 */

namespace Gateway\Pay\JiPays;

use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/** @noinspection SpellChecD:\MyProjects\card\card\app\Library\PaykingInspection */

include_once 'common.php';

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

        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位
        $payway = $config['payway'];
        /**
         * 支付编码
         * 微信扫码      8001
         * 微信公众号    8002
         * 支付宝扫码    8004
         * 支付宝公众号  8006
         */

        switch ($payway) {
            case '8001':
            case '8002':
                $bankType = 'wechat';
                break;
            case '8004':
            case '8006':
                $bankType = 'aliqr'; // 支付宝扫码
                break;
            default:
                throw new \Exception('支付渠道错误');

        }


        // 构造要请求的参数数组

        $return_url = SYS_URL . '/qrcode/pay/' . $out_trade_no . '/query';
        $params = array(
            'mch_id' => $config['id'],                //网站分配的商户号商户号(商户id)					(必填)
            'sign_type' => 'MD5',                //签名算法类型,MD5或RSA2或SHA256					(必填)
            'charset' => 'utf-8',                //编码格式,固定为:utf-8							(必填)
            'version' => '1.0',                //接口版本,固定为:1.0								(必填)
            'timestamp' => date('Y-m-d H:i:s'),    //发送请求的时间,格式"yyyy-MM-dd HH:mm:ss"		(必填)
            'notify_url' => $this->url_notify,            //异步通知地址,http/https开头字符串				(必填)
            'payment_code' => $payway,        //支付渠道编码									(必填)
            'out_trade_no' => $out_trade_no,        //商户订单号,64个字符以内							(必填)
            'total_fee' => $amount,            //订单金额,单位为元,精确到小数点后两位				(必填)
            'body' => $body,                //订单描述										(选填)
        );

        // 拼接待签名的参数
        $string = create_link_string($params);
        $params['sign'] = md5($string . '&key=' . $config['key']);

        // AJAX协议头
        $header = array(
            'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
            'X-Requested-With:XMLHttpRequest',
        );

        // 发起请求
        $gateway = 'http://pay.jipays.com/gateway';
        $url = rtrim($gateway, '/');
        $ret_raw = curl_http(rtrim($url, '/'), $params, 'post', $header);

        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['state'])) {
            Log::error('Pay.JiPays.goPay.order Error#1: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }


        if ($ret['state'] == '0') {
            Log::error('Pay.JiPays.goPay.order Error#2: ' . $ret_raw);
            throw new \Exception($ret['msg']); // 失败信息
        }

        if (is_mobile() && is_weixin()) {
            header('Location:' . $ret['data']['jump_url']);
            exit;
        }

        $url = @strlen($ret['data']['qrcode_url']) ? $ret['data']['qrcode_url'] : $ret['data']['jump_url'];
        if (strlen($url)) {
            header('location: /qrcode/pay/' . $out_trade_no . '/' . strtolower($bankType) . '?url=' . urlencode($url));
        } else {
            Log::error('Pay.JiPays.goPay.order Error#3: ' . $ret_raw);
            throw new \Exception('获取付款信息失败, 请联系客服反馈');
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

        if ($isNotify) {

            //构造验签数组
            $params = array(
                'mch_id' => $config['id'],                //商户号(商户id)
                'sign_type' => 'MD5',            //签名算法类型
                'charset' => 'utf-8',                //编码格式
                'version' => '1.0',                //接口版本
                'out_trade_no' => $_POST['out_trade_no'],        //商户订单号
                'timestamp' => $_POST['timestamp'],            //发送请求的时间
                'payment_code' => $_POST['payment_code'],        //支付渠道编码
                'body' => $_POST['body'],                //订单描述
                'attach' => $_POST['attach'],                //业务扩展参数
                'total_fee' => $_POST['total_fee'],            //付款金额
                'trade_no' => $_POST['trade_no'],            //网关订单号
                'channel_trade_no' => $_POST['channel_trade_no'],    //银行订单号,仅在订单为支付成功或部分付款状态时,才会有反馈出来
                'trade_status' => $_POST['trade_status'],        //订单付款状态
                'payment_time' => $_POST['payment_time'],        //订单付款时间
                'sign' => $_POST['sign'],                //接口签名
            );

            if (md5(create_link_string($params) . '&key=' . $config['key']) !== $params['sign']) {
                Log::error('Pay.JiPays.verify, sign error $post:' . json_encode($_POST));
                echo 'fail';
                return false;
            }

            if ($_POST['trade_status'] === 'TRADE_FINISHED') {
                $order_no = $_POST['out_trade_no']; //商户订单号
                $pay_trade_no = $_POST['trade_no']; //支付流水号
                $successCallback($order_no, (int)round($_POST['total_fee'] * 100), $pay_trade_no);
            }
            echo 'success';
            return true;
        } else {
            $out_trade_no = @$config['out_trade_no'];
            if (strlen($out_trade_no) < 5) {
                throw new \Exception('交易单号未传入');
            }
            /*
             * 构造要请求的参数数组
             */
            $params = array(
                'mch_id' => $config['id'],                //网站分配的商户号商户号(商户id)				(必填)
                'sign_type' => 'MD5',                //签名算法类型,MD5或RSA2或SHA256				(必填)
                'charset' => 'utf-8',                //编码格式,固定为:utf-8						(必填)
                'version' => '1.0',                //接口版本,固定为:1.0							(必填)
                'timestamp' => date('Y-m-d H:i:s'),    //发送请求的时间,格式"yyyy-MM-dd HH:mm:ss"	(必填)
                'out_trade_no' => $out_trade_no,        //商户订单号,64个字符以内						(必填)
            );;
            $params['sign'] = md5(create_link_string($params) . '&key=' . $config['key']);


            // AJAX协议头
            $header = array(
                'Content-Type:application/x-www-form-urlencoded;charset=utf-8',
                'X-Requested-With:XMLHttpRequest',
            );

            // 发起请求
            $gateway = 'http://pay.jipays.com/gateway';
            $url = rtrim($gateway, '/') . '/trade_query';
            $ret_raw = curl_http(rtrim($url, '/'), $params, 'post', $header);

            $ret = @json_decode($ret_raw, true);
            if (!$ret || !isset($ret['state'])) {
                Log::error('Pay.JiPays.verify Error#1: ' . $ret_raw);
                throw new \Exception('查询超时, 请刷新重试');
            }
            if ($ret['state'] == '0') {
                Log::error('Pay.JiPays.verify.verify Error#2: ' . $ret_raw);
                throw new \Exception($ret['msg']); // 失败信息
            }

            if($ret['data']['trade_status'] === 'TRADE_FINISHED'){
                $order_no = $ret['data']['out_trade_no']; //商户订单号
                $pay_trade_no = $ret['data']['trade_no']; //支付流水号
                $successCallback($order_no, (int)round($ret['data']['total_fee'] * 100), $pay_trade_no);
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
        return '此支付渠道不支持发起退款, 请手动操作';
    }
}