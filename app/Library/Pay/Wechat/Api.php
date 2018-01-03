<?php

namespace App\Library\Pay\Wechat;

use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    public function __construct()
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/wechat';
        $this->url_return = SYS_URL . '/pay/return/wechat';
    }

    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount = $amount_cent;
        $this->defineWxConfig($config);
        require_once __DIR__ . '/lib/WxPay.Api.php';
        require_once 'WxPay.NativePay.php';
        require_once 'wxLog.php';
        /*模式二流程：
         * 1、调用统一下单，取得code_url，生成二维码
         * 2、用户扫描二维码，进行支付
         * 3、支付完成之后，微信服务器会通知支付成功
         * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
         */
        $notify = new \NativePay();
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetAttach($out_trade_no);
        $input->SetOut_trade_no($out_trade_no);
        $input->SetTotal_fee($amount);//微信里面按分来计费的
        $input->SetTime_start(date('YmdHis'));
        $input->SetTime_expire(date('YmdHis', time() + 600));
        $input->SetGoods_tag('pay');//商品标记 主要用于优惠券立减功能
        $input->SetNotify_url($this->url_notify);
        $input->SetTrade_type('NATIVE');
        $input->SetProduct_id($out_trade_no);//商品ID  可自定义
        $result = $notify->GetPayUrl($input);
        if (!isset($result['code_url'])) {
            Log::error($result);

            if (isset($result['err_code_des']))
                throw new \Exception($result['err_code_des']);

            if (isset($result['return_msg']))
                throw new \Exception($result['return_msg']);

            throw new \Exception('获取支付数据失败');
        }
        header('location: /qrcode/pay/' . $out_trade_no . '/wechat/' . base64_encode($result['code_url']));
    }


    private function defineWxConfig($config)
    {
        define('wx_APPID', $config['APPID']);//绑定支付的APPID（必须配置，开户邮件中可查看）
        define('wx_MCHID', $config['MCHID']);//商户号（必须配置，开户邮件中可查看）

        /**
         * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
         * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
         */
        define('wx_KEY', $config['KEY']);
        /**
         * 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
         * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
         */
        define('wx_APPSECRET', $config['APPSECRET']);//
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        $this->defineWxConfig($config);

        require_once __DIR__ . '/lib/WxPay.Api.php';
        require_once 'wxLog.php';

        if ($isNotify) {
            return (new PayNotifyCallBack($successCallback))->Handle(false);
        } else {
            $out_trade_no = @$config['out_trade_no'];
            $input = new \WxPayOrderQuery();
            $input->SetOut_trade_no($out_trade_no);
            $result = \WxPayApi::orderQuery($input);
            if (array_key_exists('trade_state', $result) && $result['trade_state'] == 'SUCCESS') {
                call_user_func_array($successCallback, [$result['out_trade_no'], $result['total_fee'], $result['transaction_id']]);
                return true;
            } else {
                return false;
            }
        }

    }
}