<?php

namespace Gateway\Pay\MoTonePay;

use Gateway\Pay\ApiInterface;

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
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $total_fee = sprintf('%.2f', $amount_cent / 100); // 元为单位

        $version = '1.0';
        $paytype = $config['payway'];
        // qzfzfbh5 qzfzfb dlbwxsm dlbwxh5 qqrcode qqwallet
        $get_code = '0';
        $sign = md5('version=' . $version . '&customerid=' . $config['id'] . '&total_fee=' . $total_fee .
            '&sdorderno=' . $out_trade_no . '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $config['key']);

        ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf8">
            <title>正在转到付款页</title>
        </head>
        <body onLoad="document.pay.submit()">
        <form name="pay" action="http://www.motonepay.com/apisubmit" method="post">
            <input type="hidden" name="version" value="<?php echo $version ?>">
            <input type="hidden" name="customerid" value="<?php echo $config['id'] ?>">
            <input type="hidden" name="sdorderno" value="<?php echo $out_trade_no ?>">
            <input type="hidden" name="total_fee" value="<?php echo $total_fee ?>">
            <input type="hidden" name="paytype" value="<?php echo $paytype ?>">
            <input type="hidden" name="notifyurl" value="<?php echo $this->url_notify ?>">
            <input type="hidden" name="returnurl" value="<?php echo $this->url_return ?>">
            <input type="hidden" name="sign" value="<?php echo $sign ?>">
            <input type="hidden" name="get_code" value="<?php echo $get_code ?>">
        </form>
        </body>
        </html>
        <?php
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
            $status = $_POST['status'];
            $customerid = $_POST['customerid'];
            $sdorderno = $_POST['sdorderno']; // 商户订单号
            $total_fee = $_POST['total_fee']; // 订单金额 元
            $paytype = $_POST['paytype'];
            $sdpayno = $_POST['sdpayno']; // 平台订单号
            $sign = $_POST['sign'];

            $mysign = md5('customerid=' . $customerid . '&status=' . $status . '&sdpayno=' . $sdpayno . '&sdorderno=' . $sdorderno . '&total_fee=' . $total_fee . '&paytype=' . $paytype . '&' . $config['key']);

            if ($sign == $mysign) {
                if ($status == '1') {
                    $total_fee = (int)round($total_fee * 100);
                    $successCallback($sdorderno, $total_fee, $sdpayno);
                    echo 'success';
                    return true;
                } else {
                    echo 'success'; // 表示收到通知
                }
            } else {
                echo 'sign_err';
            }
        } else {
            if (!empty($config['out_trade_no'])) {
                // 查询页面点击支付 先查询一下, 不支持
                return false;
            }
            $status = $_GET['status'];
            $customerid = $_GET['customerid'];
            $sdorderno = $_GET['sdorderno'];
            $total_fee = $_GET['total_fee'];
            $paytype = $_GET['paytype'];
            $sdpayno = $_GET['sdpayno'];
            $sign = $_GET['sign'];

            $mysign = md5('customerid=' . $customerid . '&status=' . $status . '&sdpayno=' . $sdpayno . '&sdorderno=' . $sdorderno . '&total_fee=' . $total_fee . '&paytype=' . $paytype . '&' . $config['key']);

            if ($sign == $mysign) {
                if ($status == '1') {
                    $total_fee = (int)round($total_fee * 100);
                    $successCallback($sdorderno, $total_fee, $sdpayno);
                    return true;
                } else {
                    throw new \Exception('付款失败');
                }
            } else {
                throw new \Exception('sign error');
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