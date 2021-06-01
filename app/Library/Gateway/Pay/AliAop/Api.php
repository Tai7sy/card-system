<?php

namespace Gateway\Pay\AliAop;

use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/**
 * 这个驱动比较特殊, 核心系统内置了 AlipaySDK(wi1dcard/alipay-sdk), 此驱动仅作为封装使用
 * Class Api
 * @package Gateway\Pay\AliAop
 */
class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';
    private $aop = null;

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


    private function aop($config)
    {
        if ($this->aop === null) {
            $keyPair = \Alipay\Key\AlipayKeyPair::create(
                "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($config['merchant_private_key'], 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----",
                "-----BEGIN PUBLIC KEY-----\n" . wordwrap($config['alipay_public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----"
            );
            $this->aop = new \Alipay\AopClient($config['app_id'], $keyPair);
        }
        return $this->aop;
    }


    /**
     * @param array $config 支付渠道配置
     * @param string $out_trade_no 外部订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     * @throws \Alipay\Exception\AlipayErrorResponseException
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount = sprintf('%.2f', $amount_cent / 100); //支付宝元为单位

        if ($config['payway'] === 'f2f') {
            $request = \Alipay\AlipayRequestFactory::create('alipay.trade.precreate', [
                'notify_url' => $this->url_notify,
                'biz_content' => [
                    'out_trade_no' => $out_trade_no, // 商户网站唯一订单号 这里一定要保证唯一 支付宝那里没有校验
                    'total_amount' => $amount, // 订单总金额，单位为元，精确到小数点后两位，取值范围 [0.01,100000000]
                    'subject' => $subject, // 商品的标题 / 交易标题 / 订单标题 / 订单关键字等
                ],
            ]);
            $result = $this->aop($config)->execute($request)->getData();
            header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($result['qr_code']));
        } elseif ($config['payway'] === 'pc') {
            $request = \Alipay\AlipayRequestFactory::create('alipay.trade.page.pay', [
                'return_url' => $this->url_return,
                'notify_url' => $this->url_notify,
                'biz_content' => [
                    'out_trade_no' => $out_trade_no, // 商户网站唯一订单号 这里一定要保证唯一 支付宝那里没有校验
                    'product_code' => 'FAST_INSTANT_TRADE_PAY',
                    'total_amount' => $amount, // 订单总金额，单位为元，精确到小数点后两位，取值范围 [0.01,100000000]
                    'subject' => $subject, // 商品的标题 / 交易标题 / 订单标题 / 订单关键字等
                ],
            ]);
            $result = $this->aop($config)->pageExecuteUrl($request);
            header('location: ' . $result);
        } elseif ($config['payway'] === 'mobile') {
            $request = \Alipay\AlipayRequestFactory::create('alipay.trade.wap.pay', [
                'return_url' => $this->url_return,
                'notify_url' => $this->url_notify,
                'biz_content' => [
                    'out_trade_no' => $out_trade_no, // 商户网站唯一订单号 这里一定要保证唯一 支付宝那里没有校验
                    'product_code' => 'QUICK_WAP_WAY',
                    'total_amount' => $amount, // 订单总金额，单位为元，精确到小数点后两位，取值范围 [0.01,100000000]
                    'subject' => $subject, // 商品的标题 / 交易标题 / 订单标题 / 订单关键字等
                ],
            ]);
            $result = $this->aop($config)->pageExecuteUrl($request);
            header('location: ' . $result);
        }


        exit;
    }

    /**
     * @param $config
     * @param callable $successCallback 成功回调 (系统单号,交易金额/分,支付渠道单号)
     * @return bool|string true 验证通过  string 失败原因
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {
            // post
            // {"gmt_create":"2019-03-03 11:25:52","charset":"UTF-8","seller_email":"jyhnetworks@gmail.com","subject":"20190303112547kNaB9",
            //  "sign":"xxx",
            //  "buyer_id":"2088912871498663","invoice_amount":"0.01","notify_id":"2019030300222112558098661047087964",
            //  "fund_bill_list":"[{\"amount\":\"0.01\",\"fundChannel\":\"ALIPAYACCOUNT\"}]",
            //  "notify_type":"trade_status_sync","trade_status":"TRADE_SUCCESS",
            //  "receipt_amount":"0.01","buyer_pay_amount":"0.01","app_id":"2019012163100265","sign_type":"RSA2","seller_id":"2088431220893143",
            //  "gmt_payment":"2019-03-03 11:25:58","notify_time":"2019-03-03 11:25:58","version":"1.0","out_trade_no":"20190303112547kNaB9",
            //  "total_amount":"0.01","trade_no":"2019030322001498661018630713","auth_app_id":"2019012163100265","buyer_logon_id":"312***@qq.com","point_amount":"0.00"}

            if ($this->aop($config)->verify($_POST)) {
                if ($_POST['trade_status'] === 'TRADE_SUCCESS') {
                    $trade_no = $_POST['trade_no'];//支付宝交易号
                    $total_fee = (int)round($_POST['total_amount'] * 100);
                    $successCallback($_POST['out_trade_no'], $total_fee, $trade_no);
                }
            } else {
                Log::error('Pay.AliAop.goPay.verify Error: ' . json_encode($_POST));
            }
            echo 'success'; // 输出 `success`，否则支付宝服务器将会重复通知
            exit;
        }

        if (!empty($config['out_trade_no'])) {
            // payReturn(带订单号) or 当面付主动查询 or 查询页面点击支付 先查询一下
            $out_trade_no = $config['out_trade_no'];
            $request = \Alipay\AlipayRequestFactory::create('alipay.trade.query', [
                'notify_url' => $this->url_notify,
                'biz_content' => [
                    'out_trade_no' => $out_trade_no, // 商户网站唯一订单号
                ],
            ]);
            try {
                $result = $this->aop($config)->execute($request)->getData();
            } catch (\Throwable $e) {
                return false;
            }
            if ($result['trade_status'] === 'TRADE_SUCCESS') {
                $trade_no = $result['trade_no'];//支付宝交易号
                $total_fee = (int)round($result['total_amount'] * 100);
                $successCallback($result['out_trade_no'], $total_fee, $trade_no);
                return true;
            }
        } else {
            // PC or MOBILE 支付完返回的地址
            // http://127.0.0.4/pay/return/4?charset=UTF-8&
            // out_trade_no=20190303161401iqDUK&method=alipay.trade.page.pay.return&total_amount=0.01&
            // sign=xxx&trade_no=2019030322001498661018828681&auth_app_id=2019012163100265&version=1.0&app_id=2019012163100265&
            // sign_type=RSA2&seller_id=2088431220893143&timestamp=2019-03-03+16%3A14%3A48
            if (!isset($_GET['out_trade_no']) || !isset($_GET['total_amount'])) {
                return false;
            }
            $passed = $this->aop($config)->verify($_GET);
            if (!$passed) {
                Log::error('Pay.AliAop.verify Error: 支付宝签名校验失败', ['$_GET' => $_GET]);
                return false;
            }
            $trade_no = $_GET['trade_no'];//支付宝交易号
            $total_fee = (int)round($_GET['total_amount'] * 100);
            $successCallback($_GET['out_trade_no'], $total_fee, $trade_no);
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
     * @throws \Throwable
     */
    function refund($config, $order_no, $pay_trade_no, $amount_cent)
    {
        $request = \Alipay\AlipayRequestFactory::create('alipay.trade.refund', [
            'notify_url' => $this->url_notify,
            'biz_content' => [
                'out_trade_no' => $order_no, // 订单支付时传入的商户订单号
                'refund_amount' => sprintf('%.2f', $amount_cent / 100),
                'refund_reason' => '订单#' . $order_no
            ]]);

        $result = $this->aop($config)->execute($request)->getData();
        if (!isset($result['code']) || $result['code'] !== '10000') { // string 类型
            throw new \Exception($result['sub_msg'], $result['code']);
        }
        return true;
    }
}