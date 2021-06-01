<?php

namespace Gateway\Pay\Youzan;

use Gateway\Pay\ApiInterface;

/** @noinspection SpellCheckingInspection */

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
     * @param $config
     * @return mixed
     * @throws \Exception
     */
    private function getAccessToken($config)
    {
        $clientId = $config['client_id']; // 有赞云 开通的时候选择微商城, 然后绑定微小店
        $clientSecret = $config['client_secret']; // 有赞云
        $keys = ['kdt_id' => $config['kdt_id']]; //有赞 微小店 店铺授权ID
        $result = (new Open\Token($clientId, $clientSecret))->getToken('self', $keys); // self=自用型应用
        if (!isset($result['access_token'])) {
            \Log::error('Pay.Youzan.goPay.getToken Error: ' . json_encode($result));
            throw new \Exception('平台支付Token获取失败');
        }
        return $result['access_token'];
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
        $bankType = strtolower($config['payway']);
        // wechat qq alipay

        try {
            $accessToken = $this->getAccessToken($config);
            $client = new Open\Client($accessToken);
        } catch (\Exception $e) {
            \Log::error('Pay.Youzan.goPay getAccessToken error', ['exception' => $e]);
            throw new \Exception('支付渠道响应超时，请刷新重试');
        }

        $params = [
            'qr_type' => 'QR_TYPE_DYNAMIC',  // 确定金额二维码，只能被支付一次
            'qr_price' => $amount_cent,  // 金额：分
            'qr_name' => $subject, // 收款理由
            'qr_source' => $out_trade_no, // 自定义字段，你可以设置为网站订单号
        ];
        // https://www.youzanyun.com/apilist/detail/group_trade/pay_qrcode/youzan.pay.qrcode.create
        $result = $client->get('youzan.pay.qrcode.create', '3.0.0', $params);
        $result = isset($result['response']) ? $result['response'] : $result;


        // \Log::error('Pay.Youzan.goPay' . json_encode($result));
        // qr_code(base64) qr_id qr_type qr_url
        if (!isset($result['qr_url'])) {
            \Log::error('Pay.Youzan.goPay.getQrcode Error: ' . json_encode($result));
            throw new \Exception('平台支付二维码获取失败');
        }

        \App\Order::whereOrderNo($out_trade_no)->update(['pay_trade_no' => $result['qr_id']]);

        header('location: /qrcode/pay/' . $out_trade_no . '/youzan_' . strtolower($bankType) . '?url=' . urlencode($result['qr_url']));
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
        $clientId = $config['client_id']; // 有赞云 开通的时候选择微商城, 然后绑定微小店
        $clientSecret = $config['client_secret']; // 有赞云

        if ($isNotify) {
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            if (@$data['test']) {
                echo 'test success';
                return false;
            }

            /**
             * 判断消息是否合法，若合法则返回成功标识
             */
            try {
                $msg = $data['msg'];
            } catch (\Exception $e) {
                \Log::error('Pay.Youzan.verify get input error#1', ['exception' => $e, 'post_raw' => $json]);
                echo 'fatal error';
                return false;
            }
            $sign_string = $clientId . '' . $msg . '' . $clientSecret;
            $sign = md5($sign_string);
            if ($sign != $data['sign']) {
                \Log::error('Pay.Youzan.verify, sign error $sign_string:' . $sign_string . ', $sign' . $sign);
                echo 'fatal error';
                return false;
            } else {
                echo json_encode(array('code' => 0, 'msg' => 'success'));
            }
            /**
             * msg内容经过 urlencode 编码，需进行解码
             */
            $msg = json_decode(urldecode($msg), true);

            // \Log::error('Pay.Youzan.Data' . json_encode($data));
            // \Log::error('Pay.Youzan.Msg' . json_encode($msg));

            /**
             * 根据 type 来识别消息事件类型，具体的 type 值以文档为准
             * https://www.youzanyun.com/docs/guide/3401/3455
             */
            // {"update_time":"2018-04-01 11:03:22","extra_info":"{\"is_retail_offline\":false}","payment":"38.88","pay_type":"\u5fae\u4fe1\u652f\u4ed8","book_id":"","tid":"E20180401110314048842246","status":"TRADE_SUCCESS"}
            if ($data['type'] === 'TRADE_ORDER_STATE' && $msg['status'] === 'TRADE_SUCCESS') {

                try {
                    $accessToken = $this->getAccessToken($config);
                    $client = new Open\Client($accessToken);
                } catch (\Exception $e) {
                    \Log::error('Pay.Youzan.verify getAccessToken error#1', ['exception' => $e]);
                    echo 'fatal error';
                    return false;
                }
                $params = ['tid' => $msg['tid']];
                // https://www.youzanyun.com/apilist/detail/group_trade/trade/youzan.trade.get
                $result = $client->get('youzan.trade.get', '3.0.0', $params);
                if (isset($result['error_response'])) {
                    \Log::error('Pay.Youzan.verify with error：' . $result['error_response']['msg']);
                    echo 'fatal error';
                    return false;
                }

                $trade = $result['response']['trade'];
                $order = \App\Order::where('pay_trade_no', $trade['qr_id'])->first();
                if ($order) {
                    $pay_trade_no = $msg['tid']; //有赞里面的订单号
                    $successCallback($order->order_no, (int)round($trade['payment'] * 100), $pay_trade_no);
                }
            }
            return true;

        } else {
            $out_trade_no = @$config['out_trade_no'];
            if (strlen($out_trade_no) < 5) {
                throw new \Exception('交易单号未传入');
            }
            $order = \App\Order::whereOrderNo($out_trade_no)->firstOrFail();

            if (!$order->pay_trade_no || !strlen($order->pay_trade_no)) {
                // 还没有支付过 不进行下一步验证
                return false;
            }

            try {
                $accessToken = $this->getAccessToken($config);
                $client = new Open\Client($accessToken);
            } catch (\Exception $e) {
                \Log::error('Pay.Youzan.verify getAccessToken error#2', ['exception' => $e]);
                throw new \Exception('支付渠道响应超时，请刷新重试');
            }

            $params = [
                'qr_id' => $order->pay_trade_no,
                'status' => 'TRADE_RECEIVED' // 已收款
            ];
            // https://www.youzanyun.com/apilist/detail/group_trade/pay_qrcode/youzan.trades.qr.get
            $result = $client->get('youzan.trades.qr.get', '3.0.0', $params);
            $info = isset($result['response']) ? $result['response'] : $result;

            if (!isset($info['total_results'])) {
                \Log::error(
                    'Pay.Youzan.verify with error：The result of [youzan.trades.qr.get] has no key named [total_results]',
                    ['result' => $result]
                );
                return false;
            }
            if ($info['total_results'] > 0 &&
                count($info['qr_trades']) > 0 &&
                isset($info['qr_trades'][0]['qr_id']) &&
                $info['qr_trades'][0]['qr_id'] === $order->pay_trade_no) {
                $yzTrades = $info['qr_trades'][0];
                // https://www.youzanyun.com/apilist/structparam/response/TradePayQrcode[]
                // {"status":"TRADE_RECEIVED","qr_id":"6598819","qr_price":"30.00","real_price":"30.00","pay_type":"WXPAY_BIGUNSIGN","book_date":"2018-04-01 10:56:40","pay_date":"2018-04-01 10:56:49","created_date":"2018-04-01 10:56:40","payer_nick":"\u533f\u540d","tid":"E20180401105640033933771","qr_url":"https:\/\/trade.koudaitong.com\/wxpay\/confirmQr?qr_id=6598819&kdt_id=40465329","qr_name":"\u53d1\u5361i - 30\u5143\u6708\u5361","outer_tid":""}
                // \Log::error('Youzan.Debug: return: '.json_encode($yzTrades));
                // 原始记录为二维码ID $yzTrades['qr_id'];
                $pay_trade_no = $yzTrades['tid'];  //有赞里面的订单号
                $successCallback($out_trade_no, (int)round($yzTrades['real_price'] * 100), $pay_trade_no);
                return true;
            } else {
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