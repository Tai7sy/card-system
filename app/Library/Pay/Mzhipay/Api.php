<?php

namespace App\Library\Pay\Mzhipay;

use App\Library\Pay\ApiInterface;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    public function __construct()
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/Mzhipay';
        $this->url_return = SYS_URL . '/pay/return/Mzhipay';
    }


    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount = sprintf('%.2f', $amount_cent / 100); //支付宝元为单位

        $alipay_config = $this->buildAliConfig($config);
        require_once(__DIR__ . '/lib/alipay_submit.class.php');

        // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数
        $alipay_config['notify_url'] = $this->url_notify;
        // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数
        $alipay_config['return_url'] = $this->url_return . '/' . $out_trade_no;
        //构造要请求的参数数组，无需改动
        $parameter = array(
            'type' => 'alipay',
            'pid' => $alipay_config['partner'],
            'notify_url' => $alipay_config['notify_url'],
            'return_url' => $alipay_config['return_url'],
            'out_trade_no' => $out_trade_no,
            'money' => $amount,
            'name' => $subject
        );
        $alipaySubmit = new \AlipaySubmit($alipay_config);//建立请求
        $html_text = $alipaySubmit->buildRequestForm($parameter, 'post', '确认');
        echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>正在跳转到支付渠道...</title></head><body><h1 style="text-align: center">正在跳转到支付渠道...</h1>';
        echo $html_text;
    }


    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        $alipay_config = $this->buildAliConfig($config);

        require __DIR__ . '/lib/alipay_notify.class.php';
        $alipay = new \AlipayNotify($alipay_config);

        $result = $alipay->verifyNotify();
        if ($result) {//验证成功
            $out_trade_no = $_REQUEST['out_trade_no'];//商户订单号
            $trade_no = $_REQUEST['trade_no'];//支付宝交易号
            $trade_status = $_REQUEST['trade_status'];//交易状态
            $total_fee = (int)($_REQUEST['money'] * 100);

            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {//处理
                $successCallback($out_trade_no, $total_fee, $trade_no);
            }
            if ($isNotify) echo 'success';
            return true;
        } else {
            //验证失败
            if ($isNotify) echo 'fail';
            return false;
        }
    }

    private function buildAliConfig($config)
    {
        return [
            'partner' => $config['partner'],         //合作身份者ID，
            'seller_id' => $config['partner'],       //收款支付宝账号
            'key' => $config['key'],                 // MD5密钥，安全检验码
            'sign_type' => 'MD5',                    //签名方式
            'input_charset' => 'utf-8',
            //ca证书路径地址，用于curl中ssl校验,  请保证cacert.pem文件在当前文件夹目录中
            'cacert' => __DIR__ . DIRECTORY_SEPARATOR . 'cacert.pem',
            //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            'transport' => 'https',
            'payment_type' => '1',                   //支付类型 ，无需修改
            'service' => 'create_direct_pay_by_user',// 产品类型，无需修改

            //↓↓↓↓↓↓↓↓↓↓ 请在这里配置防钓鱼信息，如果没开通防钓鱼功能，为空即可 ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
            // 防钓鱼时间戳  若要使用请调用类文件submit中的query_timestamp函数
            'anti_phishing_key' => '',
            // 客户端的IP地址 非局域网的外网IP地址，如：221.0.0.1
            'exter_invoke_ip' => ''
        ];
    }
}