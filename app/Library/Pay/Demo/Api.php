<?php

namespace App\Library\Pay\Demo;

use App\Library\Pay\ApiInterface;

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
        // wechat qq alipay
        // 跳转支付页面
        header('Location: http://example.com/order?out_trade_no=' . $out_trade_no . '&subject=' . $subject . '&total_fee=' . $amount_cent);
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

            if (some_verify()) { // 一些校验, 如签名校验等
                \Log::error('这里可以记录一些出错信息, 内容保存在 /storage/logs 内');
                echo 'error';
                return false;
            } else {
                echo 'success';
            }

            $order_no = $_REQUEST[''];  // 发卡系统内交易单号
            $total_fee = $_REQUEST['']; // 实际支付金额, 单位, 分
            $pay_trade_no = $_REQUEST['']; // 支付系统内订单号/流水号
            $successCallback($order_no, $total_fee, $pay_trade_no);
            return true;

        } else {
            $order_no = @$config['out_trade_no']; // 发卡系统内交易单号
            if (strlen($order_no) < 5) {
                throw new \Exception('交易单号未传入');
            }

            if (some_verify($order_no)) { // 一些校验, 如签名校验等
                $order_no = $_REQUEST[''];  // 发卡系统内交易单号
                $total_fee = $_REQUEST['']; // 实际支付金额, 单位, 分
                $pay_trade_no = $_REQUEST['']; // 支付系统内订单号/流水号
                $successCallback($order_no, $total_fee, $pay_trade_no);
                return true;
            } else {
                \Log::error('这里可以记录一些出错信息, 内容保存在 /storage/logs 内');
                return false;
            }
        }
    }
}