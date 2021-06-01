<?php
/**
 * MugglePay.com
 * 2019年07月02日10:16:59
 *
 * 驱动 MugglePay 方式 COIN / ALIPAY / WECHAT
 */

namespace Gateway\Pay\MugglePay;

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
     * @param array $config 支付渠道配置
     * @param string $out_trade_no 本系统的订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        if (!isset($config['app_secret'])) {
            throw new \Exception('请填写[app_secret] (后台应用密钥)');
        }


        $this->url_return .= '/' . $out_trade_no;

        $params = [
            'merchant_order_id' => $out_trade_no,
            'price_amount' => sprintf('%.2f', $amount_cent / 100),
            'price_currency' => 'CNY',
            'pay_currency' => $config['payway'] !== 'COIN' ? $config['payway'] : '',
            'title' => $subject,
            'description' => $body,
            'callback_url' => $this->url_notify,
            'cancel_url' => $this->url_return,
            'success_url' => $this->url_return,
            'token' => md5($config['app_secret'] . $out_trade_no . config('app.key'))
        ];
        $ret_raw = CurlRequest::post('https://api.mugglepay.com/v1/orders', json_encode($params), [
            'Content-Type' => 'application/json',
            'token' => $config['app_secret']
        ]);
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['status']) || $ret['status'] !== 201) {
            Log::error('Pay.MugglePay.goPay.order, request failed', ['response' => $ret_raw]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        \App\Order::whereOrderNo($out_trade_no)->update(['pay_trade_no' => $ret['order']['order_id']]);
        header('Location: ' . $ret['payment_url']);
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

            $inputString = file_get_contents('php://input', 'r');
            $inputStripped = str_replace(array("\r", "\n", "\t", "\v"), '', $inputString);
            $_POST = json_decode($inputStripped, true); //convert JSON into array

            // check request format
            if (!isset($_POST['merchant_order_id']) || !isset($_POST['token'])) {
                Log::error('Pay.MugglePay.verify, request invalid', ['$_POST' => $_POST]);
                echo json_encode(['status' => 400]);
                return false;
            }

            // check token
            $out_trade_no = $_POST['merchant_order_id'];
            if ($_POST['token'] !== md5($config['app_secret'] . $out_trade_no . config('app.key'))) {
                Log::error('Pay.MugglePay.verify, token illegal', ['$_POST' => $_POST]);
                echo json_encode(['status' => 403]);
                return false;
            }

            // check payment currency
            if ($_POST['pay_currency'] !== 'CNY') {
                Log::error('Pay.MugglePay.verify, currency illegal', ['$_POST' => $_POST]);
                echo json_encode(['status' => 415]);
                return false;
            }

            // check payment status
            if ($_POST['status'] === 'PAID') {
                $pay_trade_no = $_POST['order_id']; // MugglePay order ID
                $paid = (int)round($_POST['pay_amount'] * 100);
                $successCallback($out_trade_no, $paid, $pay_trade_no);
                echo json_encode(['status' => 200]);
                return true;
            } else {
                Log::error('Pay.MugglePay.verify, status illegal', ['$_POST' => $_POST]);
            }

            echo json_encode(['status' => 406]);
            return false;
        } else {
            $out_trade_no = @$config['out_trade_no'];
            if (strlen($out_trade_no) < 5) {
                // 这里可能是payReturn支付返回页面的第一种情况, 没有传递 out_trade_no
                // 这里, $_GET 里面可能有订单参数用于校验订单是否成功(参考支付宝的AliAop逻辑)

                // 本支付驱动, payReturn 页面没有用于验证交易的参数, 不采用此种方式
                throw new \Exception('交易单号未传入');
            }

            // 用于payReturn支付返回页面第二种情况(传递了out_trade_no), 或者重新发起支付之前检查一下, 或者二维码支付页面主动请求
            // 没有能用来验证交易结果的参数, 因此主动查询交易结果


            $pay_trade_no = \App\Order::whereOrderNo($out_trade_no)->firstOrFail()->pay_trade_no; // 支付渠道流水号

            $ret_raw = CurlRequest::get('https://api.mugglepay.com/v1/orders/' . $pay_trade_no, [
                'token' => $config['app_secret']
            ]);
            $ret = @json_decode($ret_raw, true);
            if (!$ret || !isset($ret['status'])) {
                Log::error('Pay.MugglePay.verify, request failed', ['response' => $ret_raw]);
                return false;
            }


            // check payment currency
            if ($ret['order']['pay_currency'] === 'CNY') {
                // check payment status
                if ($ret['order']['status'] === 'PAID') {
                    $pay_trade_no = $ret['order']['order_id']; // MugglePay order ID
                    $paid = (int)round($ret['order']['pay_amount'] * 100); // The price set by the merchant; Example: 9.99
                    $successCallback($out_trade_no, $paid, $pay_trade_no);
                    return true;
                } else {
                    Log::error('Pay.MugglePay.verify, status illegal', ['response' => $ret_raw]);
                }
            } else {
                Log::error('Pay.MugglePay.verify, currency illegal', ['response' => $ret_raw]);
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