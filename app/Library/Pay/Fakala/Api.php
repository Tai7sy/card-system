<?php

namespace App\Library\Pay\Fakala;


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
     * @param string $out_trade_no
     * @param string $subject
     * @param string $body
     * @param int $amount_cent
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        if (!isset($config['gateway'])) {
            throw new \Exception('请填写gateway');
        }
        if (!isset($config['api_id'])) {
            throw new \Exception('请填写api_id');
        }
        if (!isset($config['api_key'])) {
            throw new \Exception('请填写api_key');
        }
        include_once 'sdk.php';
        $api = new \fakala($config['gateway'], $config['api_id'], $config['api_key']);

        $payway = strtolower($config['payway']);

        $api->goPay($payway, $subject, $out_trade_no, 0, $amount_cent, '', $this->url_return, $this->url_notify);
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        include_once 'sdk.php';
        $api = new \fakala($config['gateway'], $config['api_id'], $config['api_key']);

        if ($isNotify) {
            $result = $api->notify_verify();
            $out_trade_no = $_POST['out_trade_no'];  // 本系统订单号
            $total_fee = $_POST['total_fee'];
            $fakala_no = $_POST['order_no']; // API渠道订单号
        } else {
            // 可能是主动查询
            if (empty($config['out_trade_no'])) {
                $result = $api->return_verify();
                $out_trade_no = $_GET['out_trade_no'];  // 本系统订单号
                $total_fee = $_GET['total_fee'];
                $fakala_no = $_GET['order_no']; // API渠道订单号
            }else{
                $order =  @$api->get_order($config['out_trade_no']);
                if(empty($order['status'])){
                    return false;
                }

                $result = @$order['status'] === 2;
                $out_trade_no = $order['api_out_no'];  // 本系统订单号
                $total_fee = $order['paid'];
                $fakala_no = $order['order_no']; // API渠道订单号
            }
        }

        if ($result) {
            $successCallback($out_trade_no, $total_fee, $fakala_no);
        }

        return $result;
    }
}