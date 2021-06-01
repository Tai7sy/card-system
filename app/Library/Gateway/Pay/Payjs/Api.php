<?php

namespace Gateway\Pay\Payjs;

use Gateway\Pay\ApiInterface;

require_once __DIR__ . '/sdk/Payjs.php';

use Xhat\Payjs\Payjs;

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
        if (!isset($config['mchid'])) {
            throw new \Exception('请填写mchid');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写key');
        }

        $payjs = new Payjs($config);

        $payway = strtolower($config['payway']);


        $data = [
            'total_fee' => $amount_cent, // 金额，单位 分
            'out_trade_no' => $out_trade_no,       // 商户订单号, 在用户侧请唯一
            'body' => $out_trade_no,        // 订单标题
            'notify_url' => $this->url_notify,
            'callback_url' => SYS_URL.'/pay/result/'. $out_trade_no  // 前台地址
        ];



        if($payway === 'native'){
            $rst = $payjs->native($data); // 扫码

            if(@(int)$rst['return_code'] !== 1){
                die('<h1>支付渠道出错: '.$rst['msg'].'</h1>');
            }
            header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($rst['code_url']));

        }elseif ($payway === 'cashier'){

            $rst = $payjs->cashier($data); // 收银台
            // header('Location: ' . $rst);

            header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($rst));
        }else{
            die('<h1>请填写支付方式</h1>');
        }

        echo '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>正在跳转到支付渠道...</title></head><body><h1 style="text-align: center">正在跳转到支付渠道...</h1>';
        exit;

    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        $payjs = new Payjs($config);

        if ($isNotify) {
            $result = $payjs->checkSign($_POST);
            echo $result ? 'success' : 'fail';
        } else {
            // 前台返回 没有参数 只能返回失败了
            // $result = $payjs->check(); // 这里没文档 不一定成功
            // 这里只能等待后台 notify 然后通知前台
            $result = false;
        }

        if ($result) {
            $out_trade_no = $_REQUEST['out_trade_no'];  // 本系统订单号
            $total_fee = $_REQUEST['total_fee'];
            $payjs_order_id = $_REQUEST['payjs_order_id']; // API渠道订单号
            $successCallback($out_trade_no, $total_fee, $payjs_order_id);
            return true;
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