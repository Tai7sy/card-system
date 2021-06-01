<?php

namespace Gateway\Pay\PayBeaver;

use App\Library\CurlRequest;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

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

    private function sign($data, $app_secret)
    {
        ksort($data);
        return md5(http_build_query($data) . $app_secret);
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
            $config['gateway'] = 'https://api.paybeaver.com';
        }
        if (!isset($config['app_id'])) {
            throw new \Exception('请填写 app_id');
        }
        if (!isset($config['app_secret'])) {
            throw new \Exception('请填写 app_secret');
        }


        $return_url = SYS_URL . '/qrcode/pay/' . $out_trade_no . '/query';
        $data = [
            'app_id' => $config['app_id'],
            'out_trade_no' => $out_trade_no,
            'total_amount' => $amount_cent,
            'notify_url' => $this->url_notify,
            'return_url' => $return_url,
        ];

        $data['sign'] = $this->sign($data, $config['app_secret']);

        $ret_raw = CurlRequest::post($config['gateway'] . '/v1/gateway/fetch', http_build_query($data));
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['code']) || $ret['code'] !== 200) {
            Log::error('Pay.PayBeaver.goPay.order, request failed: ' . $ret_raw);
            throw new \Exception(@$ret['message'] ?? '获取付款信息超时, 请刷新重试');
        }

        header('location: ' .  $ret['data']['pay_url']);
        die;
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'https://api.paybeaver.com/';
        }

        if ($isNotify) {

            // app_id=kNpZCR87CvdytMyd&out_trade_no=20201219203115K8XjK&sign=a11fdaa1c260ad4d1d9f916769b8f0ff&trade_no=b614e0a2265c443d9618141cd9035dfc
            $data = $_POST;
            unset($data['sign']);
            if ($this->sign($data, $config['app_secret']) === $_POST['sign']) {
                echo "success";

                $order = \App\Order::whereOrderNo($data['out_trade_no'])->first();
                if (!$order) {
                    echo 'error';
                    Log::error('Pay.PayBeaver.verify, cannot find order:', $data);
                    return false;
                }

                $successCallback($data['out_trade_no'], $order->paid, $data['trade_no']);
                return true;
            } else {
                echo "error sign";
                return false;
            }
        } else {

            if (isset($_GET['out_trade_no'])) {

                // return page
                $data = $_GET;
                unset($data['sign']);
                if ($this->sign($data, $config['app_secret']) === $_POST['sign']) {
                    $successCallback($data['out_trade_no'], (int)$data['total_amount'], $data['trade_no']);
                    return true;
                } else {
                    return false;
                }
            } else {
                // 主动查询
                // 不支持
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
     */
    function refund($config, $order_no, $pay_trade_no, $amount_cent)
    {
        return '此支付渠道不支持发起退款, 请手动操作';
    }
}