<?php

/**
 *
 * Stripe 支付,
 *
 * 支持的支付方式: alipay wechat(支持的货币: aud,cad,eur,gbp,hkd,jpy,sgd,usd)
 * 为了保证支付成功, 请配置 webhook (source.chargeable/charge.succeeded)
 *
 * @license MIT @author 风铃
 */


namespace Gateway\Pay\Stripe;

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


    /**
     * @param array $config
     * @param string $out_trade_no
     * @param string $subject
     * @param string $body
     * @param int $amount_cent
     * @throws \Throwable
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'https://api.stripe.com';
        }
        if (!isset($config['currency'])) {
            $config['currency'] = 'usd';
        }
        if (!isset($config['api_key'])) {
            throw new \Exception('请填写API秘钥 [api_key]');
        }
        if (!isset($config['endpoint_secret'])) {
            throw new \Exception('请填写Webhook密钥签名 [endpoint_secret]');
        }


        $amount_currency = $this->getCurrency($amount_cent, $config['currency']);

        $data = [
            'type' => $config['payway'], // alipay / wechat
            'amount' => $amount_currency,
            'currency' => $config['currency'],
            'metadata' => [
                'order_no' => $out_trade_no
            ],
            'owner' => [
                'name' => $out_trade_no
            ],
            'redirect' => [
                'return_url' => $this->url_return . '/' . $out_trade_no,
            ],
            'statement_descriptor' => $subject
        ];

        $response = CurlRequest::post($config['gateway'] . '/v1/sources', http_build_query($data), [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Authorization' => 'Bearer ' . $config['api_key']
        ]);

        $source = @json_decode($response, true);
        if (!$source || !isset($source['id']) || @$source['status'] !== 'pending') {
            Log::error('Pay.Stripe.goPay.order, request failed, response: ' . $response);
            throw new \Exception(@$source['message'] ?? '获取付款信息超时, 请刷新重试');
        }


        /** @var \App\Order $order */
        $order = \App\Order::where('order_no', $source['metadata']['order_no'])->firstOrFail();
        $order->pay_trade_no = json_encode([
            'source_id' => $source['id'],
            'client_secret' => $source['client_secret'],
            'currency' => $source['currency'],
            'currency_amount' => $amount_currency,
        ]);
        $order->saveOrFail();

        switch ($config['payway']) {
            case 'alipay':
                header('Location: ' . $source['redirect']['url']);
                break;
            case 'wechat':
                header('Location: ' . $source['wechat']['qr_code_url']);
                break;
            default:
                throw new \Exception('支付方式错误');
        }
        exit;
    }

    private function verify_source($config, $order, $source, $successCallback)
    {
        if (!isset($source['metadata']) || !isset($source['metadata']['order_no'])) {
            $response = CurlRequest::get($config['gateway'] . '/v1/sources/' . $source['id'] . '?client_secret=' . $source['client_secret'], [
                'Authorization' => 'Bearer ' . $config['api_key']
            ]);
            $source = @json_decode($response, true);
            if (!$source || !isset($source['id'])) {
                Log::error('Pay.Stripe.verify_source, get source failed, response: ' . $response);
                return false;
            }
        }

        /** @var \App\Order $order */
        if ($order == null) {
            $order = \App\Order::where('order_no', $source['metadata']['order_no'])->firstOrFail();
        }
        $currency_info = @json_decode($order->pay_trade_no, true);
        if (is_array($currency_info) && (!isset($currency_info['source_id']) || $currency_info['source_id'] !== $source['id'])) {
            Log::error('Pay.Stripe.verify_source, get a invalid source: ' . json_encode($source));
            return false;
        }

        if ($source['status'] === 'chargeable') {
            // 支付成功, 钱到了临时账户(source)里面, 从临时账户转账到主账户

            // 说明尚未处理过此订单, 因此 $order->pay_trade_no 一定是个数组
            assert(is_array($currency_info));

            $data = [
                'amount' => $source['amount'],
                'currency' => $source['currency'],
                'source' => $source['id']
            ];
            $response = CurlRequest::post($config['gateway'] . '/v1/charges', http_build_query($data), [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Authorization' => 'Bearer ' . $config['api_key']
            ]);

            $charge = @json_decode($response, true);
            if (!$charge || !isset($charge['id']) || @$charge['status'] !== 'succeeded') {

                $response = CurlRequest::get($config['gateway'] . '/v1/sources/' . $source['id'] . '?client_secret=' . $source['client_secret'], [
                    'Authorization' => 'Bearer ' . $config['api_key']
                ]);
                $source = @json_decode($response, true);
                if ($source && isset($source['id']) && $source['status'] === 'consumed') {
                    // 可能这时候在payNotify, 已经正在处理了
                    return true;
                }

                Log::error('Pay.Stripe.verify_source, create charge failed, response: ' . $response);
                return false;
            }


            $amount_cny = (int)round(($order->paid / $currency_info['currency_amount']) * $source['amount']);
            // Log::error('Pay.Stripe.verify, cny: ' . $amount_cny);
            $successCallback($order->order_no, $amount_cny, $charge['id']);
            return true;

        } elseif ($source['status'] === 'consumed') {
            // 支付成功, 钱到了临时账户(source)里面, [已经从] 临时账户转账到了主账户
            // 已经成功过了, 所以不处理成功事件
            // $successCallback($data['out_trade_no'], (int)round($data['money'] * 100), $data['trade_no']);
            return true;
        }

        return false;
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'https://api.stripe.com';
        }

        if ($isNotify) {
            $request_body = file_get_contents('php://input');


            if (!isset($_SERVER['HTTP_STRIPE_SIGNATURE'])) {
                echo 'invalid request';
                return false;
            }
            $signatures = [];
            $signature_array = explode(',', $_SERVER['HTTP_STRIPE_SIGNATURE']);
            foreach ($signature_array as &$signature) {
                $kv = explode('=', $signature);
                if (count($kv) === 2)
                    $signatures[$kv[0]] = $kv[1];
            }

            if (isset($config['endpoint_secret'])) {
                $hash = hash_hmac('sha256', $signatures['t'] . '.' . $request_body, $config['endpoint_secret']);

                if ($hash !== $signatures['v1']) {
                    Log::debug('Pay.Stripe.notify, Signature: ' . json_encode($signatures));
                    Log::debug('Pay.Stripe.notify, Signature.local: ' . $hash);
                    echo 'invalid signature';
                    return false;
                }
            } else {
                Log::error('Pay.Stripe.notify, please set a [endpoint_secret]');
                echo 'invalid request';
                return false;
            }

            $event = @json_decode($request_body, true);
            if (!isset($event['id'])) {
                echo 'invalid request';
                return false;
            }

            Log::debug('Pay.Stripe.notify: ' . $event['type']);


            if ($event['type'] == 'source.chargeable') {

                $source = $event['data']['object'];

                if ($this->verify_source($config, null, $source, $successCallback)) {
                    echo 'verify_source success';
                    return true;
                } else {
                    echo 'verify_source failed';
                    return false;
                }

            } elseif ($event['type'] == 'charge.succeeded') {

                if (isset($event['data']['object']['source']['id'])) {

                    $source = $event['data']['object']['source'];
                    $charge = $event['data']['object'];

                    /** @var \App\Order $order */
                    $order = \App\Order::where('order_no', $source['metadata']['order_no'])->firstOrFail();
                    if ($order->status == \App\Order::STATUS_SUCCESS) {
                        echo 'order already processed';
                        return false;
                    }
                    $currency_info = @json_decode($order->pay_trade_no, true);
                    if (!is_array($currency_info)) {
                        echo 'order already processed';
                        return false;
                    }
                    if (!isset($currency_info['source_id']) || $currency_info['source_id'] !== $source['id']) {
                        echo 'got a invalid event';
                        return false;
                    }

                    $amount_cny = (int)round(($order->paid / $currency_info['currency_amount']) * $source['amount']);
                    // Log::error('Pay.Stripe.verify, cny: ' . $amount_cny);
                    $successCallback($order->order_no, $amount_cny, $charge['id']);
                    return true;

                } else {
                    echo 'the source of current charge does not exists.';
                    return false;
                }

                // Log::error('charge.succeed, event: ' . $request_body);

            } else {
                echo 'unknown event type: ' . $event['type'];
                return false;
            }
        } else {

            if (!empty($config['out_trade_no'])) {
                // 主动查询, 重新支付之前验证是否已支付, 或者 return_url 传递了 order_no

                /** @var \App\Order $order */
                $order = \App\Order::where('order_no', $config['out_trade_no'])->firstOrFail();
                $currency_info = @json_decode($order->pay_trade_no, true);

                if (is_array($currency_info)) {
                    // 订单没有处理过
                    $source = [
                        'id' => $currency_info['source_id'],
                        'client_secret' => $currency_info['client_secret']
                    ];
                    return $this->verify_source($config, $order, $source, $successCallback);
                }
            }

            // redirect.return_url
            if (@$_GET['redirect_status'] === 'succeeded' && isset($_GET['client_secret']) && isset($_GET['source'])) {

                $source = [
                    'id' => $_GET['source'],
                    'client_secret' => $_GET['client_secret']
                ];
                return $this->verify_source($config, null, $source, $successCallback);
            }
            return false;
        }
    }


    function getCurrency($cny, $currency = 'usd')
    {
        if ($currency === 'cny') {
            return $cny;
        }

        $ZCcyNbr_table = [
            'aud' => '澳大利亚元',
            'cad' => '加拿大元',
            'eur' => '欧元',
            'gbp' => '英镑',
            'hkd' => '港币',
            'jpy' => '日元',
            'sgd' => '新加坡元',
            'usd' => '美元',
        ];

        if (!isset($ZCcyNbr_table[$currency])) {
            throw new \Exception('不支持的汇率, 目前支持: ' . join(',', array_keys($ZCcyNbr_table)));
        }

        $response = CurlRequest::get('https://m.cmbchina.com/api/rate/getfxrate');
        $data = @json_decode($response, true);
        if (!$data || !isset($data['data'])) {
            throw new \Exception('获取汇率失败');
        }

        $rate = 99999;
        $found = false;
        foreach ($data['data'] as $item) {
            if ($item['ZCcyNbr'] === $ZCcyNbr_table[$currency]) {
                $found = true;
                $rate = 100 / $item['ZRthBid']; // 下单报价
                break;
            }
        }

        if ($found !== true) {
            Log::error(__FILE__ . ':L' . __LINE__ . 'get currency rate failed: ' . $response);
            throw new \Exception('获取汇率失败#2');
        }

        return (int)ceil($cny * $rate); // 向上取整, 1428.12 => 1429
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