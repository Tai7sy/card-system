<?php

namespace App\Library\Pay\Alipay;

use App\Library\Pay\ApiInterface;

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
     * @param array $config
     * @param string $out_trade_no 外部订单号
     * @param string $subject
     * @param string $body
     * @param $amount_cent
     * @internal param int $amount 1 = 0.01元
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount = sprintf('%.2f', $amount_cent / 100); //支付宝元为单位

        $alipay_config = $this->buildAliConfig($config);
        require_once(__DIR__ . '/sdk/alipay_submit.class.php');

        // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数
        $alipay_config['notify_url'] = $this->url_notify;
        // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数
        $alipay_config['return_url'] = $this->url_return . '/' . $out_trade_no;

        // 即时到账: https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.kiX33I&treeId=62&articleId=103740&docType=1
        // 手机网站支付: https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
        $parameter = array(
            'service' => $alipay_config['service'],
            'partner' => $alipay_config['partner'],
            'seller_id' => $alipay_config['seller_id'],
            'payment_type' => $alipay_config['payment_type'],
            'notify_url' => $alipay_config['notify_url'],
            'return_url' => $alipay_config['return_url'],
            'out_trade_no' => $out_trade_no,
            'total_fee' => $amount,
            'subject' => $subject,//商品名称
            'body' => $body,//商品描述
            'show_url' => config('app.url'), // 收银台页面上，商品展示的超链接，必填
            'app_pay' => 'Y',//启用此参数能唤起钱包APP支付宝
            '_input_charset' => 'utf-8',
        );
        $alipaySubmit = new \AlipaySubmit($alipay_config);//建立请求
        $html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
        echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>正在跳转到支付渠道...</title></head><body><h1 style="text-align: center">正在跳转到支付渠道...</h1>';
        echo $html_text;
    }


    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        $alipay_config = $this->buildAliConfig($config);

        require __DIR__ . '/sdk/alipay_notify.class.php';
        $alipay = new \AlipayNotify($alipay_config);

        if ($isNotify) {
            $result = $alipay->verifyNotify();
        } else {
            $result = $alipay->verifyReturn();
        }

        if ($result) {//验证成功
            $out_trade_no = $_REQUEST['out_trade_no'];//商户订单号
            $trade_no = $_REQUEST['trade_no'];//支付宝交易号
            $trade_status = $_REQUEST['trade_status'];//交易状态
            $total_fee = (int)round($_REQUEST['total_fee'] * 100);

            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {//处理
                $successCallback($out_trade_no, $total_fee, $trade_no);
            }
            if ($isNotify) echo 'success';
            return true;
        } else {
            //验证失败
            if ($isNotify) {
                echo 'fail';
                $logTag = 'payNotify pay_id: ' . $this->pay_id . ',Alipay';
            } else {
                $logTag = 'payReturn pay_id: ' . $this->pay_id . ',Alipay';
            }
            \Log::error($logTag . ' Alipay.Api.verify failed');
            return false;
        }
    }

    private function buildAliConfig($config)
    {
        return [
            'partner' => $config['partner'],         //合作身份者ID，
            'seller_id' => $config['partner'],       //收款支付宝账号
            'key' => $config['key'],                 //MD5密钥，安全检验码
            'sign_type' => 'MD5',                    //签名方式
            'input_charset' => 'utf-8',
            //ca证书路径地址，用于curl中ssl校验,  请保证cacert.pem文件在当前文件夹目录中
            'cacert' => __DIR__ . DIRECTORY_SEPARATOR . 'cacert.pem',
            //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
            'transport' => 'https',
            'payment_type' => '1',                   // 支付类型 ，无需修改
            'service' => $config['payway'] === 'wap' ?
                'alipay.wap.create.direct.pay.by.user' : // 手机网站支付
                'create_direct_pay_by_user', // 即时到账
        ];
    }
}