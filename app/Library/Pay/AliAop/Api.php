<?php

namespace App\Library\Pay\AliAop;

use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

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
     * @throws \Alipay\Exception\AlipayErrorResponseException
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {


            if($this->aop($config)->verify($_POST)){
                if ($_POST['trade_status'] === 'TRADE_SUCCESS') {
                    $trade_no = $_POST['trade_no'];//支付宝交易号
                    $total_fee = (int)round($_POST['total_amount'] * 100);
                    $successCallback($_POST['out_trade_no'], $total_fee, $trade_no);
                }
            }else{
                Log::error('Pay.AliAop.goPay.verify Error: ' . json_encode($_POST));
            }
            echo 'success'; // 输出 `success`，否则支付宝服务器将会重复通知
            exit;
        }

        if (!empty($config['out_trade_no'])) {
            // 当面付主动查询 or 查询页面点击支付 先查询一下
            $out_trade_no = $config['out_trade_no'];
            $request = \Alipay\AlipayRequestFactory::create('alipay.trade.query', [
                'notify_url' => $this->url_notify,
                'biz_content' => [
                    'out_trade_no' => $out_trade_no, // 商户网站唯一订单号
                ],
            ]);
            $result = $this->aop($config)->execute($request)->getData();
            if ($result['trade_status'] === 'TRADE_SUCCESS') {
                $trade_no = $result['trade_no'];//支付宝交易号
                $total_fee = (int)round($result['total_amount'] * 100);
                $successCallback($result['out_trade_no'], $total_fee, $trade_no);
                return true;
            }
        }else{

            $passed = $this->aop($config)->verify($_GET);
            if (!$passed) {
                throw new \Exception('支付宝签名校验失败');
            }
            $trade_no = $_GET['trade_no'];//支付宝交易号
            $total_fee = (int)round($_GET['total_amount'] * 100);
            $successCallback($_GET['out_trade_no'], $total_fee, $trade_no);
            return true;
        }
        return false;
    }
}