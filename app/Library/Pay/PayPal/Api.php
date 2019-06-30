<?php
/**
 *
 * business 为 商家Email
 */

namespace App\Library\Pay\PayPal;

use App\Library\CurlRequest;
use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';
    private $pay_id;

    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
        $this->pay_id = $id;
    }

    /**
     * @param array $config 支付渠道配置
     * @param string $out_trade_no 外部订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $cent_usd = self::getUsd($amount_cent);
        $amount = sprintf('%.2f', $cent_usd / 100); //元为单位

        \App\Order::where('order_no', $out_trade_no)->update(['pay_trade_no' => $amount]);

        if (!isset($config['business'])) {
            throw new \Exception('请填写 business (商家邮箱)');
        }

        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="' . $config['business'] . '">
    <input type="hidden" name="item_name" value="' . $subject . '">
    <input type="hidden" name="item_number" value="' . $out_trade_no . '">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="amount" value="' . $amount . '">
    <input type="hidden" name="tax" value="0.00">
    <input type="hidden" name="no_note" value="1">
    <input type="hidden" name="no_shipping" value="1">
    <input type="hidden" name="address_override" value="0">
    <input type="hidden" name="charset" value="utf-8">
    <input type="hidden" name="bn" value="PAY">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="return" value="' . $this->url_return . '/' . $out_trade_no . '">
    <input type="hidden" name="cancel_return" value="' . $this->url_return . '/' . $out_trade_no . '">
    <input type="hidden" name="notify_url" value="' . $this->url_notify . '">
</form>
</body>
        ');
    }

    /**
     * @param $config
     * @param callable $successCallback 成功回调 (系统单号,交易金额/分,支付渠道单号)
     * @return bool|string true 验证通过  string 失败原因
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        if (!$isNotify && @!isset($_POST['business'])) {
            return false;
        }

// STEP 1: read POST data
// Reading POSTed data directly from $_POST causes serialization issues with array data in the POST.
// Instead, read raw POST data from the input stream.
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
// read the IPN message sent from PayPal and prepend 'cmd=_notify-validate'
        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

// Step 2: POST IPN data back to PayPal to validate
        $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr');
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //返回获取的输出文本流
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); // SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // SSL
        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
// In wamp-like environments that do not come bundled with root authority certificates,
// please download 'cacert.pem' from "https://curl.haxx.se/docs/caextract.html" and set
// the directory path of the certificate as shown below:
// curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
        if (!($res = curl_exec($ch))) {
            // error_log("Got " . curl_error($ch) . " when processing IPN data");
            Log::error('Pay.PayPal.verify notify fail, Got ' . curl_error($ch) . ' when processing IPN data');
            curl_close($ch);
            exit;
        }
        curl_close($ch);


        // inspect IPN validation result and act accordingly
        if (strcmp($res, "VERIFIED") == 0) {
            if ($isNotify) echo('IPN OK');

            // The IPN is verified, process it
            $order_no = $_POST['item_number'];
            $payment_status = $_POST['payment_status'];
            $payment_amount = $_POST['mc_gross'];
            $payment_currency = $_POST['mc_currency'];
            $txn_id = $_POST['txn_id'];
            $receiver_email = $isNotify ? $_POST['receiver_email'] : $_POST['business'];

            if ($payment_status !== 'Completed') {
                Log::debug('Pay.PayPal.verify fail, order_no:' . $order_no . ', Payment status is not Completed, current(' . $payment_status . ')', ['$_POST' => $_POST]);
                return false;
            }

            if ($payment_currency !== 'USD') {
                Log::error('Pay.PayPal.verify fail, order_no:' . $order_no . ', Payment currency is not USD, current(' . $payment_currency . ')', ['$_POST' => $_POST]);
                return false;
            }

            /** @var \App\Order $order */
            $order = \App\Order::where('order_no', $order_no)->firstOrFail();
            if ($order->status === \App\Order::STATUS_PAID || $order->status === \App\Order::STATUS_SUCCESS) {
                return true;
            }


            if ($order->pay_trade_no !== $payment_amount) { // pay_trade_no 临时储存汇率
                Log::error('Pay.PayPal.verify fail, order_no:' . $order_no . ', Payment amount error (' . $order->pay_trade_no . '), current(' . $payment_amount . ')', ['$_POST' => $_POST]);
            }


            if ($receiver_email !== $config['business']) {
                Log::error('Pay.PayPal.verify fail, order_no:' . $order_no . ', payment account is not yours(' . $config['business'] . '), current(' . $receiver_email . ')', ['$_POST' => $_POST]);
                return false;
            }

            $successCallback($order_no, $order->paid, $txn_id); // 这里直接用原始的
            return true;

        } else if (strcmp($res, "INVALID") == 0) {
            if ($isNotify) echo('IPN OK');
            // IPN invalid, log for manual investigation
            Log::debug('Pay.PayPal.verify notify fail, IPN INVALID', ['$res' => $res, '$_POST' => $_POST]);
        } else {
            Log::debug('Pay.PayPal.verify notify fail, Unknown IPN error', ['$res' => $res, '$_POST' => $_POST]);
        }

        return false;
    }


    function getUsd($cny)
    {
        $data = @json_decode(CurlRequest::get('https://m.cmbchina.com/api/rate/getfxrate'), true);
        if (!isset($data['data'])) {
            throw  new \Exception('获取汇率失败');
        }

        $rate = 0.2;
        foreach ($data['data'] as $item) {
            if ($item['ZCcyNbr'] === '美元') {
                $rate = 100 / $item['ZRtcOfr'];
                break;
            }
        }

        return $cny * $rate;
    }
}

















