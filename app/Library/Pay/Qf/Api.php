<?php

namespace App\Library\Pay\Qf;

use App\Library\CurlRequest as Request;

use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/**
 *
 * 暂时不可用 仅测试
 *
 */

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


    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        // $amount = sprintf("%.2f", $amount_cent / 100);
        $bankType = strtolower($config['payway']);
        if (!isset($config['id'])) {
            throw new \Exception('请设置 id');
        }

        $headers = [];
        if ($bankType == 'qq') {
            $headers = ['User-Agent' => 'Mozilla/5.0 Mobile MQQBrowser/6.2 QQ/7.2.5.3305'];
        } elseif ($bankType == 'alipay') {
            $headers = ['User-Agent' => 'Mozilla/5.0 AlipayChannelId/5136 AlipayDefined(nt:WIFI,ws:411|0|2.625) AliApp(AP/10.1.10.1226101) AlipayClient/10.1.10.1226101'];
        }

        // https://o2.qfpay.com/q/info?code=&huid=G39Mp&opuid=&reqid=
        // {"resperr":"","respcd":"0000","respmsg":"","data":{"profile":{"huid":"G39Mp","opuid":0,"currency":"156","appid":"","nickname":"\u4e1a\u52a1\u5458\u674e\u6587\u8c6a","currency_code":"\uffe5"},"customer":{"hcid":"vl","openid":"","balance":0},"reqid":"040a57f2c2ed4f9d","url":{"activity_tip":"https://marketing.qfpay.com/v1/mkw/activity_tip_page"}}}
        $userInfo = Request::get('https://o2.qfpay.com/q/info?code=&huid=' . $config['id'] . '&opuid=&reqid=' . $out_trade_no, $headers);
        $reqId = static::str_between($userInfo, 'reqid":"', '"');
        $currency = static::str_between($userInfo, 'currency":"', '"');
        if ($reqId == '' || $currency == '') {
            Log::error('qfpay pay, 获取支付金额失败 - ' . $userInfo);
            throw new \Exception('获取支付请求id失败');
        }


        $payInfo = Request::post('https://o2.qfpay.com/q/payment',
            'txamt=' . $amount_cent .
            '&openid=&appid=&huid=' . $config['id'] .
            '&opuid=&reqid=' . $reqId . '&balance=0&currency=' . $currency,
            $headers
        );
        //{"resperr":"","respcd":"0000","respmsg":"","data":{"total_amt":11100,"reqid":"040a57f2c2ed4f9d","balance_amt":0,"coupon_code":"","coupon_title":"","pay_amt":11100,"redirect_uri":"https://marketing.qfpay.com/v1/mkw/payover_router?syssn=20180124000200020059243592","syssn":"20180124000200020059243592","pay_params":{"pubAccHint":"","pubAcc":"","tokenId":"6V437bc87d18b157dc0c218d68977a1b"},"coupon_amt":0,"type":"qqpay"}}
        $result = json_decode($payInfo, true);
        $sysSN = static::str_between($payInfo, 'syssn":"', '"');// qf 内部id
        if (!$result || $sysSN == '') {
            Log::error('qfpay pay, 生成支付单号失败#1 - ' . $payInfo);
            throw new \Exception('生成支付单号失败#1');
        }
        if ($result['respcd'] !== '0000') {
            if (isset($result['respmsg']) && $result['respmsg'] !== '')
                throw new \Exception($result['respmsg']);
            Log::error('qfpay pay, 生成支付单号失败#2 - ' . $payInfo);
            throw new \Exception('生成支付单号失败#2');
        }

        \App\Order::whereOrderNo($out_trade_no)->update(['pay_trade_no' => $sysSN]);


        header('location: /qrcode/pay/' . $out_trade_no . '/qf_' . $bankType . '?url=' .
            urlencode(json_encode($result['data']['pay_params'])));

    }

    function verify($config, $successCallback)
    {
        // $isNotify = isset($config['isNotify']) && $config['isNotify'];

        $order = \App\Order::whereOrderNo($config['out_trade_no'])->firstOrFail();
        $sysSN = $order->pay_trade_no;


        $queryRet = Request::get('https://marketing.qfpay.com/v1/mkw/activity?syssn=' . $sysSN);
        $queryJson = json_decode($queryRet, true);
        if (!$queryRet) {
            throw new \Exception('query error');
        }

        // https://marketing.qfpay.com/v1/mkw/activity?syssn=20180124000200020059243592
        // {"resperr":"trade is not success","respcd":"2101","respmsg":"trade is not success","data":{}}
        // {"resperr":"","respcd":"0000","respmsg":"","data":{"customer":{"province":null,"openid":null,"headimgurl":null,"city":null,"country":null,"nickname":"","id":"Rvka0B","sex":null},"tm_curr":1516799187,"prepaid":{},"trade":{"payamt":1100,"coupon_amt":0,"trade_amt":1100,"syssn":"20180124000200020059218970","pay_code":"8970"},"cardinfo_url":"http://m.haojin.in/v2/app.html#!/?huid=G39Mp&go=carddetail","mchnt":{"province":"\u5c71\u4e1c\u7701","shopname":"\u4e1a\u52a1\u5458\u674e\u6587\u8c6a","huid":"G39Mp","mcc":"21005","telephone":"","is_baipai":0,"address":"\u9655\u897f\u7701\u897f\u5b89\u5e02\u5c1a\u7a37\u8def5555\u53f7","jointime":"2017-12-14 17:51:30","id":1987910,"uid":1987910,"city":"\u4e34\u6c82\u5e02","name":"\u674e\u6587\u8c6a","mobile":"13145202277","longitude":0.0,"email":"13145202277@qfpay.com","state":4,"latitude":0.0,"groupid":1881631,"overdue":1},"coupons_url":"https://marketing.qfpay.com/paydone/show_coupon.html?customer_id=Rvka0B","card":{"customer_info":{},"actv":{},"overdue":{}},"adv":{},"adv_data":{"openid":"oo3Lss3YATDHlBVcSZbj10caD7Bk","userid":1987910,"adv_type":1,"adv_cnt":0,"customer_id":"111518648","groupid":1881631},"member":{},"theme":false,"activity":{},"type":0}}

        if (!isset($queryJson['respcd'])) {
            Log::error('qfpay query, 获取支付结果失败 - ' . $queryRet);
            throw new \Exception('获取支付结果失败');
        }

        if ($queryJson['respcd'] !== '0000') {
            return false;
        }

        $pay_amount = (int)static::str_between($queryRet, 'trade_amt":', ',');
        if ($pay_amount === 0) {
            $pay_amount = (int)static::str_between($queryRet, 'txamt":', ',');
            if ($pay_amount === 0) {
                Log::error('qfpay query, 获取支付金额失败 - ' . $queryRet);
                throw new \Exception('获取支付金额失败');
            }
        }

        if ($queryJson['respcd'] === '0000') {
            $successCallback($config['out_trade_no'], $pay_amount, $sysSN);
            return true;
        }


        return false;
    }


    public static function str_between($str, $mark1, $mark2)
    {
        $st = stripos($str, $mark1);
        if ($st === false) return '';
        $ed = stripos($str, $mark2, $st + strlen($mark1));
        if ($ed === false || $st >= $ed) return '';
        $len = strlen($mark1);
        $ret = substr($str, $st + $len, ($ed - $st - $len));
        return $ret;
    }
}