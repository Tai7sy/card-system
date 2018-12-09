<?php

namespace App\Library\Pay\WeChat;

use App\Library\Helper;
use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

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
        // payway: NATIVE 二维码支付
        // payway: MWEB 微信H5 需要开通权限

        // Log::debug('Pay.WeChat.goPay, order_no:' . $out_trade_no.', step1');
        $amount = $amount_cent;
        $payway = strtoupper($config['payway']);
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
        $input->SetTrade_type($payway);
        if ($payway === 'MWEB')
            $input->SetScene_info('{"h5_info": {"type":"Wap","wap_url": "' . SYS_URL . '","wap_name": "发卡平台"}}');
        $input->SetProduct_id($out_trade_no);//商品ID  可自定义
        $input->SetSpbill_create_ip(Helper::getIP());


        function getResultUrl($out_trade_no, $result, $key)
        {
            if (!isset($result[$key])) {
                Log::error('Pay.WeChat.goPay, order_no:' . $out_trade_no . ', error:' . json_encode($result));
                if (isset($result['err_code_des']))
                    throw new \Exception($result['err_code_des']);
                if (isset($result['return_msg']))
                    throw new \Exception($result['return_msg']);
                throw new \Exception('获取支付数据失败');
            }
            return $result[$key];
        }

        if ($payway === 'NATIVE') {
            // 二维码支付
            $result = $notify->GetPayUrl($input);
            $url = getResultUrl($out_trade_no,$result,'code_url');
            header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($url));

        } elseif ($payway === 'MWEB') {
            // H5支付
            $result = $notify->GetH5PayUrl($input);
            $url = getResultUrl($out_trade_no,$result,'mweb_url');
            // header('location: ' . $url);
            // 这里是为了API支付的时候跳转微信带上referer, 不然微信要发疯咬人
            echo view('utils.redirect', ['url' => $url]);
        }
        exit;
    }


    private function defineWxConfig($config)
    {
        if (!defined('wx_APPID'))
            define('wx_APPID', $config['APPID']);//绑定支付的APPID（必须配置，开户邮件中可查看）
        if (!defined('wx_MCHID'))
            define('wx_MCHID', $config['MCHID']);//商户号（必须配置，开户邮件中可查看）

        /**
         * KEY：商户支付密钥，参考开户邮件设置（必须配置，登录商户平台自行设置）
         * 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
         */
        if (!defined('wx_KEY'))
            define('wx_KEY', $config['KEY']);
        /**
         * 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
         * 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
         */
        if (!defined('wx_APPSECRET'))
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