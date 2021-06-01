<?php

namespace Gateway\Pay\WeChat;

use App\Library\CurlRequest;
use App\Library\Helper;
use Gateway\Pay\ApiInterface;
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
        if (strpos(@$_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            $payway = 'JSAPI'; // 微信内部
            $pay_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . '/pay/' . $out_trade_no;
            $auth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $config['APPID'] . '&redirect_uri=' . urlencode($pay_url) . '&response_type=code&scope=snsapi_base#wechat_redirect';

            if (!isset($_GET['code'])) {
                header('Location: ' . $auth_url);
                exit;
            }

            $request_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $config['APPID'] . '&secret=' . $config['APPSECRET'] . '&code=' . $_GET['code'] . '&grant_type=authorization_code';
            $ret = @json_decode(CurlRequest::get($request_url), true);
            if (!is_array($ret) || empty($ret['openid'])) {
                if (isset($ret['errcode']) && $ret['errcode'] === 40163) {
                    // code been used, 已经用过(用户的刷新行为), 重新发起
                    header('Location: ' . $auth_url);
                    exit;
                }
                die('<h1>获取微信OPENID<br>错误信息: ' . (isset($ret['errcode']) ? $ret['errcode'] : $ret) . '<br>' . (isset($ret['errmsg']) ? $ret['errmsg'] : $ret) . '<br>请返回重试</h1>');
            }
            $openid = $ret['openid'];
        } else {
            // 如果在微信外部, 且支付方式是JSAPI, 二维码为当前页面
            if ($payway === 'JSAPI') {
                $pay_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . '/pay/' . $out_trade_no;
                // $auth_url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=' . $config['APPID'] . '&redirect_uri=' . urlencode($pay_url) . '&response_type=code&scope=snsapi_base#wechat_redirect';
                // $auth_url 太复杂了以至于不能生成二维码....
                header('Location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($pay_url));
                exit;
            }
        }
        $this->defineWxConfig($config);
        require_once __DIR__ . '/lib/WxPay.Api.php';
        require_once 'WxPay.NativePay.php';
        require_once 'WxLog.php';
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
        $input->SetTrade_type($payway);
        if ($payway === 'MWEB')
            $input->SetScene_info('{"h5_info": {"type":"Wap","wap_url": "' . SYS_URL . '","wap_name": "DEV"}}');
        if ($payway === 'JSAPI')
            $input->SetOpenid($openid);
        $input->SetProduct_id($out_trade_no);//商品ID  可自定义
        $input->SetSpbill_create_ip(Helper::getIP());
        $input->SetNotify_url($this->url_notify); // 异步通知url

        $result = $notify->unifiedOrder($input);

        function getValue($out_trade_no, $result, $key)
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
            $url = getValue($out_trade_no, $result, 'code_url');
            header('Location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($url));
        } elseif ($payway === 'JSAPI') {
            // 微信内部
            $params = [
                'appId' => $config['APPID'],                // 公众号名称，由商户传入
                'timeStamp' => strval(time()),              // 时间戳，自1970年以来的秒数
                'nonceStr' => md5(time() . 'nonceStr'), // 随机串
                'package' => 'prepay_id=' . getValue($out_trade_no, $result, 'prepay_id'),
                'signType' => 'MD5',                        // 微信签名方式：
            ];
            $data = new \WxPayJsApiPay();
            $data->FromArray($params);
            $params['paySign'] = $data->MakeSign(); // 微信签名
            header('Location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode(json_encode($params)));
        } elseif ($payway === 'MWEB') {
            // H5支付
            $url = getValue($out_trade_no, $result, 'mweb_url');
            $result_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}" . '/qrcode/pay/' . $out_trade_no . '/wechat?url=query';
            // header('location: ' . $url);
            // 这里是为了API支付的时候跳转微信带上referer, 不然微信要发疯咬人
            echo view('utils.redirect', ['url' => $url . '&redirect_url=' . urlencode($result_url)]);
        }
        exit;
    }


    private function defineWxConfig($config)
    {
        if (!defined('wx_APPID'))
            define('wx_APPID', $config['APPID']);//绑定支付的APPID（必须配置，开户邮件中可查看）
        if (!defined('wx_MCHID'))
            define('wx_MCHID', $config['MCHID']);//商户号（必须配置，开户邮件中可查看）

        if (!defined('wx_SUBAPPID'))
            define('wx_SUBAPPID', @$config['sub_appid']);
        if (!defined('wx_SUBMCHID'))
            define('wx_SUBMCHID', @$config['sub_mch_id']);
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
        require_once 'WxLog.php';

        if ($isNotify) {
            return (new PayNotifyCallBack($successCallback))->Handle(false);
        } else {
            $out_trade_no = @$config['out_trade_no'];
            $input = new \WxPayOrderQuery();
            $input->SetOut_trade_no($out_trade_no);
            if (isset($config['sub_mch_id'])) {
                $input->SetSub_mch_id($config['sub_mch_id']);
            }
            try {
                $result = \WxPayApi::orderQuery($input);
                if (array_key_exists('trade_state', $result) && $result['trade_state'] == 'SUCCESS') {
                    call_user_func_array($successCallback, [$result['out_trade_no'], $result['total_fee'], $result['transaction_id']]);
                    return true;
                } else {
                    Log::debug('Pay.WeChat.verify, orderQuery failed. ' . json_encode($result));
                    return false;
                }
            } catch (\Throwable $e) {
                Log::error('Pay.WeChat.verify, orderQuery exception:. ' . $e->getMessage(), ['exception' => $e]);
                return false;
            }

        }
    }

    /**
     * 退款操作
     * @param array $config 支付渠道配置
     * @param string $order_no 订单号
     * @param string $pay_trade_no 支付渠道流水号
     * @param int $amount_cent 金额/分
     * @return true|string true 退款成功  string 失败原因
     * @throws \WxPayException
     */
    function refund($config, $order_no, $pay_trade_no, $amount_cent)
    {
        if (!isset($config['ssl_cert']) || !isset($config['ssl_key']))
            throw new \Exception('请设置 ssl_cert(证书文件) 和 ssl_key(证书key)');

        $this->defineWxConfig($config);

        if (!defined('wx_SSLCERT')) {
            $tmpFile = tmpfile();
            fwrite($tmpFile, "-----BEGIN CERTIFICATE-----\n" . wordwrap(trim($config['ssl_cert']), 64, "\n", true) . "\n-----END CERTIFICATE-----");
            define('wx_SSLCERT', stream_get_meta_data($tmpFile)['uri']);
        }

        if (!defined('wx_SSLKEY')) {
            $tmpFile2 = tmpfile();
            fwrite($tmpFile2, "-----BEGIN PRIVATE KEY-----\n" . wordwrap(trim($config['ssl_key']), 64, "\n", true) . "\n-----END PRIVATE KEY-----");
            define('wx_SSLKEY', stream_get_meta_data($tmpFile2)['uri']);
        }

        require_once __DIR__ . '/lib/WxPay.Api.php';
        require_once 'WxLog.php';

        $input = new \WxPayRefund();
        $input->SetOut_refund_no('anfaka' . date('YmdHis')); // 退款单号, 随机
        $input->SetOut_trade_no($order_no);
        $input->SetTotal_fee($amount_cent); // 订单总金额也要传递, 真傻比
        $input->SetRefund_fee($amount_cent); // 单位为分
        if (isset($config['sub_mch_id'])) {
            $input->SetSub_mch_id($config['sub_mch_id']);
        }
        $result = \WxPayApi::refund($input);

        if ($result['return_code'] !== 'SUCCESS') {
            throw new \Exception($result['return_msg']);
        }

        if ($result['result_code'] !== 'SUCCESS') {
            throw new \Exception($result['err_code_des'], $result['err_code']);
        }
        return true;
    }
}