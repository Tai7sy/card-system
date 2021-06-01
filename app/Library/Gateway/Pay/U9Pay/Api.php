<?php

/**
 * 悠久支付
 * 2020年3月14日
 */

namespace Gateway\Pay\U9Pay;

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
            $gateway = 'http://1.u9clouds.com';
        } else {
            $gateway = $config['gateway'];
        }
        $amount = sprintf('%.2f', $amount_cent / 100); // 元为单位
        $payway = $config['payway'];

        $params = [
            'pay_memberid' => $config['id'],  // 商户id,由恒隆支付分配
            'pay_orderid' => $out_trade_no,  // 网站订单号
            'pay_applydate' => date('Y-m-d H:i:s'), // 时间格式
            'pay_bankcode' => $payway,
            'pay_notifyurl' => $this->url_notify,
            'pay_callbackurl' => $this->url_return . '/' . $out_trade_no, //这里 是微信 or 支付宝 支付完毕跳转的地址, 轮训等待成功
            'pay_amount' => $amount, // 单位元（人民币）
        ];

        $params['pay_md5sign'] = $this->getSign($params, $config['key']);
        $params['pay_productname'] = $subject; // 用户自定义商品名称, 不参与签名

        ?>
        <!doctype html>
        <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>正在转到付款页</title>
    </head>
    <body onload="document.pay.submit()">
    <form name="pay" action="<?php echo $gateway; ?>/Pay_Index.html" method="post">
        <?php
        foreach ($params as $key => $val) {
            echo '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
        ?>
    </form>
    </body>
        <?php
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

            // notify page
            $sign_column = ['memberid', 'orderid', 'transaction_id', 'amount', 'datetime', 'returncode'];
            $params = [];
            foreach ($sign_column as $column)
                $params[$column] = $_REQUEST[$column];

            if ($this->getSign($params, $config['key']) !== $_REQUEST['sign']) {
                Log::error('Pay.U9Pay.verify.notify, sign error $post:' . json_encode($_REQUEST));
                echo 'sign error';
                return false;
            }

            $order_no = $_REQUEST['orderid']; //上行过程中商户系统传入的商户系统订单
            $pay_trade_no = $_REQUEST['transaction_id']; //支付流水号
            $successCallback($order_no, (int)round($_REQUEST['amount'] * 100), $pay_trade_no);

            echo 'ok';
            return true;
        } else {
            if (!empty($config['out_trade_no'])) {
                // 主动查询接口, 此接口不支持....
                // 此驱动, 不支持主动查询交易结果, 直接返回失败(未支付)
                return false;
            }

            // return page
            $sign_column = ['memberid', 'orderid', 'transaction_id', 'amount', 'datetime', 'returncode'];
            $params = [];
            foreach ($sign_column as $column)
                $params[$column] = $_REQUEST[$column];

            if ($this->getSign($params, $config['key']) !== $_REQUEST['sign']) {
                Log::error('Pay.U9Pay.verify.return, sign error $_REQUEST:' . json_encode($_REQUEST));
                echo 'sign error';
                return false;
            }

            $order_no = $_REQUEST['orderid']; //上行过程中商户系统传入的商户系统订单
            $pay_trade_no = $_REQUEST['transaction_id']; //支付流水号
            $successCallback($order_no, (int)round($_REQUEST['amount'] * 100), $pay_trade_no);

            return true;
        }
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