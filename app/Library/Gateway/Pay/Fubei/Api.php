<?php
namespace Gateway\Pay\Fubei;

use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

class Api implements ApiInterface
{
    private $url_notify = '';
    private $url_return = '';
    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
    }
    /**
     * @param array $config 配置信息
     * @param string $out_trade_no 发卡系统订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 支付金额, 单位:分
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $payway = strtolower($config['payway']);
        switch ($config['payway']) {
            case 'wechat':
                $type = 1;
                $paysrc = "wechat";
                break;
            case 'alipay':
                $type = 2;
                $paysrc = "aliqr";
                break;
            default:
                throw new \Exception('支付方式填写错误, alipay/weixin');
        }
        $data = ["app_id" => $config['app_id'], "method" => "openapi.payment.order.scan", "format" => "json", "sign_method" => "md5", "nonce" => "ilay1380"];
        $content = ["type" => $type, "merchant_order_sn" => $out_trade_no, "body" => '订单' . $subject, "total_fee" => $amount_cent * 0.01, "store_id" => $config['store_id']];
        $key = $config['secret'];
        $data['biz_content'] = json_encode($content);
        $result = $this->execute($data, $key);
        $arr = json_decode($result, true);
        if ($arr['result_code'] == 200) {
            header('location: /qrcode/pay/' . $out_trade_no . '/' . $paysrc . '?url=' . $arr['data']['qr_code']);
            echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>正在跳转到支付渠道...</title></head><body><h1 style="text-align: center">正在跳转到支付渠道...</h1>';
            die;
        } else {
            throw new \Exception($arr['result_message'].",请重新下单");
        }
    }
    /**
     * @param $config
     * @param callable $successCallback
     * @return bool|string
     * @throws \Exception
     */
    function verify($config, $successCallback)
    {
        // 这里传递了发卡系统内交易单号, 有两种可能来到此步骤
        // 1. 用户提交订单后未支付, 重新发起支付, 支付前需要校验是否已经支付
        // 2. 此支付方式支持二维码扫码等方式, 二维码页面轮训请求是否支付
        if (!empty($config['out_trade_no'])) {
            // 通过主动查询方式查询交易是否成功
            $data = ["app_id" => $config['app_id'], "method" => "openapi.payment.order.query", "format" => "json", "sign_method" => "md5", "nonce" => "ilay1380"];
            $key = $config['secret'];
            $out_trade_no = $config['out_trade_no'];
            $content = ["merchant_order_sn" => $out_trade_no];
            $data['biz_content'] = json_encode($content);
            $result = json_decode($this->execute($data, $key), true);
            if ($result['result_code'] != 400) {
                $status = $result['data'];
                if ($status['trade_state'] == "SUCCESS") {

                    // 发卡系统内交易单号
                    $order_no = $status['merchant_order_sn'];

                    // 实际支付金额, 单位, 分
                    $total_fee = (int)round($status['total_fee'] * 100);

                    // 支付系统内订单号/流水号
                    $pay_trade_no = $status['trade_no'];

                    $successCallback($order_no, $total_fee, $pay_trade_no);
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    const GATEWAY = "https://shq-api.51fubei.com/gateway";
    public static function execute($content, $key)
    {
        $content['sign'] = static::generateSign($content, $key);
        $result = static::mycurl(static::GATEWAY, $content);
        return $result;
    }
    private static function mycurl($url, $params = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        if (!empty($params)) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $header = array("content-type: application/json");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        $reponse = curl_exec($ch);
        if (curl_errno($ch)) {
            throw new \Exception(curl_error($ch), 0);
        } else {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if (200 !== $httpStatusCode) {
                throw new \Exception($reponse, $httpStatusCode);
            }
        }
        curl_close($ch);
        return $reponse;
    }
    private static function generateSign($content, $key)
    {
        return strtoupper(static::sign(static::getSignContent($content) . $key));
    }
    private static function getSignContent($content)
    {
        ksort($content);
        $signString = "";
        foreach ($content as $key => $val) {
            if (!empty($val)) {
                $signString .= $key . "=" . $val . "&";
            }
        }
        $signString = rtrim($signString, "&");
        return $signString;
    }
    private static function sign($data)
    {
        return md5($data);
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
