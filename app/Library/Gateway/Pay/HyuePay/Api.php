<?php


namespace Gateway\Pay\HyuePay;

use App\Library\Helper;
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
        if (!isset($config['merchantId'])) {
            throw new \Exception('请填写商户号 [merchantId]');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写 [key]');
        }
        if (!isset($config['gateway'])) {
            $gateway = 'http://www.hyuepay.com';
        } else {
            $gateway = $config['gateway'];
        }

        $payway = $config['payway'];
        switch ($payway) {
            case '1001':
            case '1201':
            case '1301':
                $bankType = 'wechat';
                break;
            case '1002':
            case '1202':
            case '1302':
                $bankType = 'aliqr'; // 支付宝扫码
                break;
            default:
                throw new \Exception('支付渠道错误');

        }
        /**
        * 微信扫码：1001
        * 微信条码：1201
        * 支付宝扫码:1002
        * 支付宝条码:1202
        * 微信公众号:1301
        * 支付宝公众号:1302
         */

        $return_url = SYS_URL . '/qrcode/pay/' . $out_trade_no . '/query';
        $params = [
            'orderId' => $out_trade_no,  // 商户流水号 (网站订单号)
            'merchantId' => $config['merchantId'],  // 商户id,由支付分配
            'version' => '0.0.1',    // 版本号：0.0.1（固定）
            'orderAmt' => $amount_cent, // 订单金额 单位：分
            'bizCode' => $payway,   // 业务代码
            'bgUrl' => $this->url_notify,   // 支付结果回调url
            'returnUrl' => $return_url,
            'terminalIp' => Helper::getIP(), // 客户端ip
            'productName' => $subject, // 商品名称
            'productDes' => $body, // 商品描述
        ];

        $post_data = $this->getPostData($params, $config['key']);
        $ret_raw = $this->curl_post($gateway . '/order/pay.do', $post_data);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['rspCode'])) {
            Log::error('Pay.HyuePay.goPay.order Error#1: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        if ($ret['rspCode'] !== '00') {
            Log::error('Pay.HyuePay.goPay.order Error#2: ' . $ret_raw);
            throw new \Exception(@$ret['rspMsg'] ?? '获取付款信息失败, 请联系客服反馈');
        }

        header('location: /qrcode/pay/' . $out_trade_no . '/' . strtolower($bankType) . '?url=' . urlencode($ret['payUrl']));
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
            $gateway = 'http://www.hyuepay.com';
        } else {
            $gateway = $config['gateway'];
        }

        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        if ($isNotify) {

            $data = @json_decode(file_get_contents('php://input'), true);

            $sign_column = ['orderAmt', 'upOrderId', 'status', 'orderId', 'merchantId'];
            $params = [];
            foreach ($sign_column as $column)
                $params[$column] = $data[$column];

            if ($this->getSign($params, $config['key']) !== $data['sign']) {
                Log::error('Pay.HyuePay.verify, sign error $post:' . file_get_contents('php://input'));
                echo 'sign error';
                return false;
            }

            $order_no = $data['orderId']; //上行过程中商户系统传入的商户系统订单
            $pay_trade_no = $data['upOrderId']; //支付流水号
            $successCallback($order_no, intval($data['orderAmt']), $pay_trade_no);

            echo 'ok';
            return true;
        } else {
            if (empty($config['out_trade_no'])) {
                return false;
            } else {
                // 主动查询

                $params = [
                    'orderId' => $config['out_trade_no'],
                    'merchantId' => $config['merchantId'],    // 商户ID
                    'bizCode' => '4001',    // 业务代码 固定值：4001
                    'version' => '0.0.1',   // 版本号：0.0.1（固定）
                ];
                $post_data = $this->getPostData($params, $config['key']);
                $ret_raw = $this->curl_post($gateway . '/query/order.do', $post_data);
                $ret = @json_decode($ret_raw, true);
                if (!$ret || !isset($ret['rspCode']) || $ret['rspCode'] !== '00') {
                    Log::error('Pay.HyuePay.query error#1', ['ret_raw' => $ret_raw]);
                    return false;
                }
                if ($ret['status'] === '00') {
                    $trans_no = 'NULL'; // 主动查询接口未提供
                    $successCallback($ret['orderId'], intval($ret['orderAmt']), $trans_no);
                    return true;
                }
                return false;
            }
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
        return $params . '&sign=' . strtoupper(md5($sign_data));
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


    private function curl_post($url, $post_data = '')
    {
        $headers['Accept'] = '*/*';
        $headers['Referer'] = $url;
        $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        $sendHeaders = array();
        foreach ($headers as $headerName => $headerVal) {
            $sendHeaders[] = $headerName . ': ' . $headerVal;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回获取的输出文本流
        curl_setopt($ch, CURLOPT_HEADER, 1);         // 将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headerSize);
        curl_close($ch);

        return $body;
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
            $gateway = 'http://www.hyuepay.com';
        } else {
            $gateway = $config['gateway'];
        }

        $params = [
            'orderId' => $order_no,  // 网站订单号
            'merchantId' => $config['merchantId'],  // 商户id,由支付分配
            'bizCode' => '2900', // 固定值：2900
            'version' => '0.0.1',   // 版本号：0.0.1（固定）
            'refundAmt' => $amount_cent, // 单位（分）
            'bgUrl' => 'NULL',
            'origOrderId' => $pay_trade_no, // 原支付订单请求流水号
        ];

        $post_data = $this->getPostData($params, $config['key']);
        $ret_raw = $this->curl_post($gateway . '/refund/pay.do', $post_data);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['rspCode'])) {
            Log::error('Pay.HyuePay.refund Error#1: ' . $ret_raw);
            return '获取退款信息超时, 请检查日志';
        }

        if ($ret['rspCode'] !== '00') {
            Log::error('Pay.HyuePay.refund Error#2: ' . $ret_raw);
            return '获取退款信息失败, 请检查日志';
        }
        return true;
    }
}