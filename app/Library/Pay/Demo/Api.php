<?php

namespace App\Library\Pay\Demo;

use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/**
 * Demo 直接支付成功
 * Class Api
 * @package App\Library\Pay\Demo
 */
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
        sleep(5);
        header('Location:' . $this->url_return . '/' . $out_trade_no);
        exit;
    }

    /**
     * @param $config
     * @param callable $successCallback
     * @return bool|string
     * @throws \Exception
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {

        } else {
            $order_no = @$config['out_trade_no'];  //商户订单号
            if (strlen($order_no) < 5) {
                throw new \Exception('交易单号未传入');
            }

            $pay_trade_no = date('YmdHis'); //支付流水号
            $successCallback($order_no, \App\Order::whereOrderNo($order_no)->first()->paid, $pay_trade_no);
            return true;
        }
        return true;
    }


}