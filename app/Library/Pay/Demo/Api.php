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
                // 这里可能是payReturn支付返回页面的第一种情况, 没有传递 out_trade_no
                // 这里的URL, $_GET 里面可能有订单参数用于校验订单是否成功(参考支付宝的AliAop逻辑)
                throw new \Exception('交易单号未传入');
            }

            // 用于payReturn支付返回页面第二种情况(传递了out_trade_no), 或者重新发起支付之前检查一下, 或者二维码支付页面主动请求
            // 主动查询交易结果
            $pay_trade_no = date('YmdHis'); //支付流水号
            $successCallback($order_no, \App\Order::whereOrderNo($order_no)->first()->paid, $pay_trade_no);
            return true;
        }
        return true;
    }


}