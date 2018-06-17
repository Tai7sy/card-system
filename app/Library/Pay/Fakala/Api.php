<?php

namespace App\Library\Pay\Fakala;


use App\Library\Pay\ApiInterface;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    public function __construct()
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/fakala';
        $this->url_return = SYS_URL . '/pay/return/fakala';
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
        if (!isset($config['api_id'])) {
            throw new \Exception('请填写api_id');
        }
        if (!isset($config['api_key'])) {
            throw new \Exception('请填写api_key');
        }
        include_once 'sdk/api.php';
        $api = new \fakala();
        $api->uid = $config['api_id'];
        $api->key = $config['api_key'];

        $payway = strtolower($config['payway']);

        $api->goPay($payway, $out_trade_no, 0, $amount_cent, '', $this->url_return, $this->url_notify);
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        include_once 'sdk/api.php';
        $api = new \fakala();
        $api->uid = $config['api_id'];
        $api->key = $config['api_key'];

        if ($isNotify) {
            $result = $api->notify_verify();
        } else {
            $result = $api->return_verify();
        }

        if ($result) {
            $out_trade_no = $_REQUEST['out_trade_no'];  // 本系统订单号
            $total_fee = $_REQUEST['total_fee'];
            $fakala_no = $_REQUEST['order_no']; // API渠道订单号
            $successCallback($out_trade_no, $total_fee, $fakala_no);
            return true;
        }

        return false;
    }
}