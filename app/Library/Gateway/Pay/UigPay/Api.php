<?php
/**
 * http://www.uigpay.com/login
 * 2019-07-12 11:54:49
 *
 * 驱动 UigPay 方式(随便填)
 */

namespace Gateway\Pay\UigPay;

use App\Library\CurlRequest;
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
        if (empty($config['id'])) {
            throw new \Exception('请填写商户编号 [id]');
        }
        if (empty($config['key'])) {
            throw new \Exception('请填写商户密钥 [key]');
        }

        $params = [
            'version' => '1.0',
            'customerid' => $config['id'],
            'sdorderno' => $out_trade_no,
            'total_fee' => sprintf('%.2f', $amount_cent / 100),
            'notifyurl' => $this->url_notify,
            'returnurl' => $this->url_return,
            'remark' => $subject
        ];

        $params['sign'] = md5("version={$params['version']}&customerid={$params['customerid']}&total_fee={$params['total_fee']}&sdorderno={$params['sdorderno']}&notifyurl={$params['notifyurl']}&returnurl={$params['returnurl']}&{$config['key']}");
        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="http://www.guupay.com/checkout" method="post">
    <input type="hidden" name="version" value="' . $params['version'] . '">
    <input type="hidden" name="customerid" value="' . $params['customerid'] . '">
    <input type="hidden" name="sdorderno" value="' . $params['sdorderno'] . '">
    <input type="hidden" name="total_fee" value="' . $params['total_fee'] . '">
    <input type="hidden" name="notifyurl" value="' . $params['notifyurl'] . '">
    <input type="hidden" name="returnurl" value="' . $params['returnurl'] . '">
    <input type="hidden" name="remark" value="' . $params['remark'] . '">
    <input type="hidden" name="sign" value="' . $params['sign'] . '">
</form>
</body>
        ');
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        if ($isNotify) {
            $sign = md5("customerid={$_POST['customerid']}&status={$_POST['status']}&sdpayno={$_POST['sdpayno']}&sdorderno={$_POST['sdorderno']}&total_fee={$_POST['total_fee']}&paytype={$_POST['paytype']}&{$config['key']}");
            if ($sign !== $_POST['sign']) {
                echo 'sign_error';
                return false;
            }
            echo 'success';

            if ( $_POST['status'] === '1') {
                $order_no = $_POST['sdorderno'];  // 本系统订单号
                $total_fee = (int)round($_POST['total_fee'] * 100);
                $pay_no = $_POST['sdpayno']; // API渠道订单号
                $successCallback($order_no, $total_fee, $pay_no);
                return true;
            }

            return false;
        }


        // 可能是主动查询
        if (!empty($config['out_trade_no'])) {
            $data = [
                'customerid' => $config['id'],
                'sdorderno' => $config['out_trade_no'],
                'reqtime' => date('YmdHis'),
            ];
            $data['sign'] = md5("customerid={$data['customerid']}&sdorderno={$data['sdorderno']}&reqtime={$data['reqtime']}&{$config['key']}");
            $ret_raw = CurlRequest::post('http://www.guupay.com/apiorderquery' , http_build_query($data));
            $ret = @json_decode($ret_raw, true);
            if (!$ret || !isset($ret['status'])) {
                Log::error('Pay.UigPay.verify, request failed', ['response' => $ret_raw]);
                return false;
            }
            if($ret['status'] === 1){
                $order_no = $ret['sdorderno'];  // 本系统订单号
                $total_fee = (int)round($ret['total_fee'] * 100);
                $pay_no = $ret['sdpayno']; // API渠道订单号
                $successCallback($order_no, $total_fee, $pay_no);
                return true;
            }
            return false;
        }

        $sign = md5("customerid={$_GET['customerid']}&status={$_GET['status']}&sdpayno={$_GET['sdpayno']}&sdorderno={$_GET['sdorderno']}&total_fee={$_GET['total_fee']}&paytype={$_GET['paytype']}&{$config['key']}");
        if ($sign !== $_GET['sign']) {
            echo 'sign_error';
            return false;
        }
        echo 'success';

        if ( $_GET['status'] === '1') {
            $order_no = $_GET['sdorderno'];  // 本系统订单号
            $total_fee = (int)round($_GET['total_fee'] * 100);
            $pay_no = $_GET['sdpayno']; // API渠道订单号
            $successCallback($order_no, $total_fee, $pay_no);
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