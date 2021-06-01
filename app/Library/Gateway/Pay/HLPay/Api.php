<?php

/**
 * 恒隆支付
 * 2019年3月3日
 */

namespace Gateway\Pay\HLPay;

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
            $gateway = 'http://henglpay.com';
        } else {
            $gateway = $config['gateway'];
        }
        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位
        $payway = $config['payway'];
        switch ($payway) {
            case '901':
            case '902':
                $bankType = 'wechat';
                break;
            case '903':
            case '904':
                $bankType = 'aliqr'; // 支付宝扫码
                break;
            default:
                throw new \Exception('支付渠道错误');

        }
        /**
         * 901    微信公众号
         * 902    微信扫码支付
         * 903    支付宝扫码支付
         * 904    支付宝手机
         */

        $return_url = SYS_URL . '/qrcode/pay/' . $out_trade_no . '/query';
        $params = [
            'pay_memberid' => $config['id'],  // 商户id,由恒隆支付分配
            'pay_orderid' => $out_trade_no,  // 网站订单号
            'pay_applydate' => date('Y-m-d H:i:s'), // 时间格式
            'pay_bankcode' => $payway,
            'pay_notifyurl' => $this->url_notify,
            'pay_callbackurl' => $return_url, //这里 是微信 or 支付宝 支付完毕跳转的地址, 轮训等待成功
            'pay_amount' => $amount, // 单位元（人民币）
            'pay_productname' => $subject, // 用户自定义商品名称
        ];

        $post_data = $this->getPostData($params, $config['key']);
        $ret_raw = $this->curl_post($gateway . '/Pay_Index.html', $post_data);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['status'])) {
            Log::error('Pay.HLPay.goPay.order Error#1: ' . $ret_raw);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        if ($ret['status'] !== '200' || !isset($ret['data']['QRCodeUrl'])) {
            Log::error('Pay.HLPay.goPay.order Error#2: ' . $ret_raw);
            throw new \Exception('获取付款信息失败, 请联系客服反馈');
        }


        if (@$ret['data']['type'] === 'qrcode') {
            header('location: /qrcode/pay/' . $out_trade_no . '/' . strtolower($bankType) . '?url=' . urlencode($ret['data']['QRCodeUrl']));
        } elseif ($ret['type'] === 'page') {
            echo $ret['data']['QRCodeUrl'];
        } elseif ($ret['type'] === 'jsapi') {
            var_dump('未启用此方式: ');
            var_dump($ret['data']['QRCodeUrl']);
        } else {
            Log::error('Pay.HLPay.goPay.order Error#3: ' . $ret_raw);
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
            $sign_column = ['memberid', 'orderid', 'transaction_id', 'amount', 'datetime', 'returncode'];
            $params = [];
            foreach ($sign_column as $column)
                $params[$column] = $_POST[$column];

            if ($this->getSign($params, $config['key']) !== $_POST['sign']) {
                Log::error('Pay.HLPay.verify, sign error $post:' . json_encode($_POST));
                echo 'sign error';
                return false;
            }

            $order_no = $_POST['orderid']; //上行过程中商户系统传入的商户系统订单
            $pay_trade_no = $_POST['transaction_id']; //支付流水号
            $successCallback($order_no, (int)round($_POST['amount'] * 100), $pay_trade_no);

            echo 'ok';
            return true;
        } else {
            /*$out_trade_no = @$config['out_trade_no'];
            if (strlen($out_trade_no) < 5) {
                throw new \Exception('交易单号未传入');
            }*/
            // 此驱动, 不支持主动查询交易结果
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
        return $params . '&pay_md5sign=' . strtoupper(md5($sign_data));
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
            $gateway = 'http://henglpay.com';
        } else {
            $gateway = $config['gateway'];
        }
        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位
        $params = [
            'pay_memberid' => $config['id'],  // 商户id,由恒隆支付分配
            'pay_orderid' => $order_no,  // 网站订单号
            'pay_applydate' => date('Y-m-d H:i:s'), // 时间格式
            'pay_amount' => $amount, // 单位元（人民币）
        ];

        $post_data = $this->getPostData($params, $config['key']);
        $ret_raw = $this->curl_post($gateway . '/Pay_Refund.html', $post_data);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['status'])) {
            Log::error('Pay.HLPay.refund Error#1: ' . $ret_raw);
            return '获取退款信息超时, 请重试';
        }

        if ($ret['status'] !== '200') {
            Log::error('Pay.HLPay.refund Error#2: ' . $ret_raw);
            return '获取退款信息失败, 请重试';
        }
        return true;
    }
}