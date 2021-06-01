<?php

namespace Gateway\Pay\Yeke;


use Gateway\Pay\ApiInterface;

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
        $amount = sprintf("%.2f", $amount_cent / 100);

        define('yeke_USER_ID', $config['id']);//接入商户ID
        define('yeke_USER_KEY', $config['key']);//接入密钥
        define('yeke_USER_LOG_PREFIX', 'log');
        define('yeke_API_GATE', 'http://www.yekepay.com/api/ApiGo.php');//网关地址

        $bankType = strtolower($config['payway']);
        $yekePayType = '';
        $yekePayGate = '';
        if ($bankType == 'alipay' || $bankType == 'alipay_wap') {
            $yekePayType = 'alipay';
            $yekePayGate = 'ALIPAY';
        } elseif ($bankType == 'tenpay' || $bankType == 'tenpay_wap') {
            $yekePayType = 'tenpay';
            $yekePayGate = 'TENPAY';
        } elseif ($bankType == 'weixin') {
            $yekePayType = 'weixin';
            $yekePayGate = 'WEIXIN';
        } elseif ($bankType == 'weixin_wap') {
            $yekePayType = 'wxwap';
            $yekePayGate = 'wxwap';
        } elseif ($bankType == 'qq' || $bankType == 'qq_wap') {
            $yekePayType = 'sqzf';
            $yekePayGate = 'sqzf';
        } elseif ($bankType == 'qq_wap') {
            $yekePayType = 'sqzfqb';
            $yekePayGate = 'sqzfqb';
        }


        require 'yekeApiLib.php';
        require 'HttpClient.php';


        $subject = mb_substr($subject, 0, 50, 'UTF-8');
        $body = mb_substr($body, 0, 100, 'UTF-8');

        $params = array(
            'P_orderid' => $out_trade_no, //外部订单号
            'P_paymoney' => $amount, //充值金额,格式为 18.60
            'P_productname' => urlencode($subject), //商品名称,最多支持100个字符
            'P_productinfo' => urlencode($body), //商品简介,最多支持200个字符
            'P_remark' => urlencode($out_trade_no), //备注说明,最多支持300个字符
            'P_custom_1' => urlencode($yekePayGate), //自定义内容一,最多支持100个字符
            'P_custom_2' => '', //自定义内容二,最多支持100个字符
            'P_contact' => urlencode(SYS_NAME), //联系方式
            'P_paytype' => $yekePayType, //充值方式
            'P_gateway' => $yekePayGate, //充值网关
            'P_cardnum' => '',
            'P_cardpwd' => '',
            'P_cardvalue' => '',
            'P_notify_url' => $this->url_notify,
            'P_return_url' => $this->url_return . '/' . $out_trade_no,
        );
        $API = new \yekeAPI();
        $result = $API->payGate($params);
        echo $result;
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        define('yeke_USER_ID', $config['id']);//接入商户ID
        define('yeke_USER_KEY', $config['key']);//接入密钥
        define('yeke_USER_LOG_PREFIX', 'log');
        define('yeke_API_GATE', 'http://www.yekepay.com/api/ApiGo.php');//网关地址
        require 'HttpClient.php';
        require 'yekeApiLib.php';

        //验证订单
        $API = new \yekeAPI();
        if ($isNotify) {
            $result = $API->verifyNotify();//   高能  这个函数里面异常吃屎的又  urlencode了一次
        } else {
            $result = $API->verifyReturn();//   高能  这个函数里面异常吃屎的又  urlencode了一次
        }

        if ($result) {
            $P_orderid = $_REQUEST['P_orderid'];//API交易订单号
            $P_api_orderid = $_REQUEST['P_api_orderid'];//商户订单号
            $P_money = $_REQUEST['P_money'];
            // $attach = urldecode($_POST['P_remark']);
            // $P_custom_1 = $_POST['P_custom_1'];
            // 订单成功状态
            if ($_REQUEST['P_status'] == 'SUCCESS') {
                $successCallback($P_api_orderid, (int)round($P_money * 100), $P_orderid);
                if ($isNotify) echo 'success';
                return true;
            } else {
                if ($isNotify) echo '失败订单';
            }
        } else {
            if ($isNotify) echo '验证失败';
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
     */
    function refund($config, $order_no, $pay_trade_no, $amount_cent)
    {
        return '此支付渠道不支持发起退款, 请手动操作';
    }
}