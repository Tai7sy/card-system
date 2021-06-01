<?php
/**
 *
 * merchant_id 为 商家ID
 * ipn_secret 为 IPN密钥
 * https://www.coinpayments.net/index.php?cmd=acct_settings
 * User: Windy
 * Date: 2019/5/19
 * Time: 19:25:18
 */

namespace Gateway\Pay\CoinPayments;

use Gateway\Pay\ApiInterface;
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
        $amount = sprintf('%.2f', $amount_cent / 100); //元为单位

        if (!isset($config['merchant_id'])) {
            throw new \Exception('请填写 merchant_id (商家ID)');
        }
        if (!isset($config['ipn_secret'])) {
            throw new \Exception('请填写 ipn_secret (IPN密钥)');
        }

        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="alert(\'如果您已经支付过，请勿重复支付，订单处理需要一定的时间请等待。\');document.pay.submit()">
<form name="pay" action="https://www.coinpayments.net/index.php" method="post">
    <input type="hidden" name="cmd" value="_pay_auto">
    <input type="hidden" name="merchant" value="' . $config['merchant_id'] . '">
    <input type="hidden" name="reset" value="1">
    <input type="hidden" name="currency" value="CNY">
    <input type="hidden" name="amountf" value="' . $amount . '">
    <input type="hidden" name="item_name" value="' . $subject . '">
    <input type="hidden" name="item_number" value="' . $out_trade_no . '">
    <input type="hidden" name="invoice" value="' . $out_trade_no . '">
    <input type="hidden" name="want_shipping" value="0">
    <input type="hidden" name="ipn_url" value="' . $this->url_notify . '">
    <input type="hidden" name="success_url" value="' . $this->url_return . '">
    <input type="hidden" name="cancel_url" value="' . $this->url_return . '">
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

        if (!$isNotify) {
            return false;
        }

        //These would normally be loaded from your database, the most common way is to pass the Order ID through the 'custom' POST field.

        function errorAndDie($error_msg)
        {
            Log::error('Pay.CoinPayments.verify error: ' . $error_msg);
            die('IPN Error: ' . $error_msg);
        }

        if (!isset($_POST['ipn_mode']) || $_POST['ipn_mode'] != 'hmac') {
            errorAndDie('IPN Mode is not HMAC');
        }

        if (!isset($_SERVER['HTTP_HMAC']) || empty($_SERVER['HTTP_HMAC'])) {
            errorAndDie('No HMAC signature sent.');
        }

        $request = file_get_contents('php://input');
        if ($request === FALSE || empty($request)) {
            errorAndDie('Error reading POST data');
        }

        if (!isset($_POST['merchant']) || $_POST['merchant'] != trim($config['merchant_id'])) {
            errorAndDie('No or incorrect Merchant ID passed');
        }

        $hmac = hash_hmac("sha512", $request, trim($config['ipn_secret']));
        if (!hash_equals($hmac, $_SERVER['HTTP_HMAC'])) {
            //if ($hmac != $_SERVER['HTTP_HMAC']) { <-- Use this if you are running a version of PHP below 5.6.0 without the hash_equals function
            errorAndDie('HMAC signature does not match');
        }

        // HMAC Signature verified at this point, load some variables.


        $amount1 = floatval($_POST['amount1']); // 订单金额
        $amount2 = floatval($_POST['amount2']); // 币金额
        $currency1 = $_POST['currency1']; // 提交时候的订单币种, 这里需要校验一下, 防止修改
        $currency2 = $_POST['currency2']; // 币类型 BTC / LTC ....
        $status = intval($_POST['status']);
        $status_text = $_POST['status_text'];
        $order_no = $_POST['item_number'];   // 本系统订单号
        $total_fee = (int)round($amount1 * 100);
        $txn_id = $_POST['txn_id'];

        //depending on the API of your system, you may want to check and see if the transaction ID $txn_id has already been handled before at this point

        // Check the original currency to make sure the buyer didn't change it.
        if ($currency1 !== 'CNY') {
            errorAndDie('Pay.CoinPayments.verify error, order_no:' . $order_no . ', Original currency(CNY) mismatch, current(' . $currency1 . ')!');
        }

        if ($status >= 100 || $status == 2) {
            // payment is complete or queued for nightly payout, success
            $successCallback($order_no, $total_fee, $txn_id);

        } else if ($status < 0) {
            Log::debug('Pay.CoinPayments.verify info: payment error, this is usually final but payments will sometimes be reopened if there was no exchange rate conversion or with seller consent', ['$_POST' => $_POST]);
        } else {
            Log::debug('Pay.CoinPayments.verify info: payment is pending, you can optionally add a note to the order page', ['$_POST' => $_POST]);
        }
        die('IPN OK');
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