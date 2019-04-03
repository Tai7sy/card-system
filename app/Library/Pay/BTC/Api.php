<?php
/**
 * BTC付款
 * 调用 blockchain.info 的API
 * 实测没什么用, 只能监听20个钱包的地址
 */
namespace App\Library\Pay\BTC;

use App\Library\CurlRequest;
use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

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
     * @param array $config
     * @param string $out_trade_no
     * @param string $subject
     * @param string $body
     * @param int $amount_cent
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount_btc = CurlRequest::get('https://api.blockchain.info/tobtc?currency=CNY&value=' .
            sprintf('%.2f', $amount_cent / 100));
        if (!$amount_btc) {
            Log::error('Pay.BTC.goPay, get price error:' . @$amount_btc);
            throw new \Exception('获取BTC价格失败，请联系客服');
        }

        $address_raw = CurlRequest::get('https://api.blockchain.info/v2/receive?xpub=' . $config['xpub'] .
            '&callback=' . urlencode($this->url_notify . '?secret=' . $config['secret']) .
            '&key=' . $config['key']);
        $receive = @json_decode($address_raw, true);
        if (!$receive || !isset($receive['address'])) {
            if($receive['description'] === 'Gap between last used address and next address too large. This might make funds inaccessible.') {
                throw new \Exception('钱包地址到达限制, 请等待之前的用户完成付款');
            }
            Log::error('Pay.BTC.goPay, get address error:' . @$address_raw);
            throw new \Exception('获取BTC地址失败，请联系客服');
        }

        // 小数点后8位
        $code_url = 'bitcoin:' . $receive['address'] . '?amount=' . $amount_btc;

        // 这个地址是唯一的
        if (\App\Order::wherePayTradeNo($code_url)->exists()) {
            throw new \Exception('支付失败, 当前钱包地址重复');
        }

        \App\Order::whereOrderNo($out_trade_no)->update(['pay_trade_no' => $code_url]);

        header('location: /qrcode/pay/' . $out_trade_no . '/btc?url=' . urlencode(json_encode([
                'address' => $receive['address'],
                'amount' => $amount_btc
            ])));
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
            if (@$_GET['secret'] !== $config['secret']) {
                echo 'error';
                return false;
            }

            if (isset($_GET['confirmations'])) {
                $address = $_GET['address'];
                $code_url = 'bitcoin:' . $address . '?amount=' . rtrim(rtrim(sprintf('%.8f', $_GET['value'] / 1e8), '0'), '.');
                $order = \App\Order::wherePayTradeNo($code_url)->first();
                if (!$order) {
                    echo 'error';
                    Log::error('Pay.BTC.verify, cannot find order:' . json_encode([
                            'url' => $code_url,
                            'params' => $_GET,
                        ]));
                    return false;
                }
                $pay_trade_no = $code_url; //支付地址 作为支付流水号储存
                $successCallback($order->order_no, $order->paid, $pay_trade_no);
            }

            echo '*ok*';
            return true;
        } else {
            // 此驱动, 不支持主动查询交易结果
            return false;
        }
    }

}