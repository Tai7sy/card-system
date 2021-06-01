<?php

namespace Gateway\Pay\AiMing;

use App\Library\CurlRequest;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

/**
 * 爱铭微付
 * https://yun.944km.cn/
 *
 * Class Api
 * @package App\Library\Gateway\Pay\AiMing
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
        $total_fee = strval($amount_cent / 100); // 元为单位


        if (!isset($config['cert_id'])) {
            throw new \Exception('请设置[cert_id]');
        }
        if (!isset($config['public_key'])) {
            throw new \Exception('请设置商户私钥[public_key]');
        }
        if (!isset($config['private_key'])) {
            throw new \Exception('请设置网站公钥[private_key]');
        }
        if (!isset($config['payment_type'])) {
            throw new \Exception('请设置支付方式[payment_type]');
        }
        if (!isset($config['payment_id'])) {
            throw new \Exception('请设置银行接口代码[payment_id]');
        }
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'https://yun.944km.cn';
        }

        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($config['private_key'], 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
        $pubKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($config['public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";

        $data = [
            'version' => '1.0',
            'price' => $total_fee,
            'name' => $subject,
            'body' => $body,
            'payment' => [
                'type' => $config['payment_type'],
                'id' => $config['payment_id']
            ],
            'server' => [
                'return' => $this->url_return,
                'notify' => $this->url_notify,
                'url' => config('app.url')
            ],
            'orderid' => time()
        ];
        $data['price'] = number_format($data['price'], 2, ".", ""); // 必须格式化金额
        $data = json_encode($data);

        //对$data进行加密 要加密的数据字符串 得到加密后的数据 加密所需要的公钥
        /** @noinspection PhpComposerExtensionStubsInspection */
        if (!openssl_public_encrypt($data, $encrypted, $pubKey)) {
            throw new \Exception('openssl 加密失败, 请检查密钥是否填写正确');
        }

        $post = [
            'cert_id' => $config['cert_id'], // 爱铭微付官方证书序列号
            'pay_data' => base64_encode($encrypted) // 必须此方式BASE64加密数据
        ];

        var_dump($post);
        $ret_raw = CurlRequest::post($config['gateway'] . '/gateway_index.do', http_build_query($post), [
            'Content-Type' => 'application/x-www-form-urlencoded'
        ]);
        var_dump($ret_raw);
        // 暂未开发完毕
        exit;
        $ret = @json_decode($ret_raw, true);
        if (!$ret || !isset($ret['Code']) || $ret['Code'] !== '1009') {
            Log::error('Pay.AiMing.goPay.order, request failed', ['response' => $ret_raw]);
            throw new \Exception('获取付款信息超时, 请刷新重试');
        }

        switch (intval($config['payway'])) {
            case 6:
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($ret['Info']));
                break;
            case 4:
                header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($ret['Info']));
                break;
            default:
                throw new \Exception('支付方式错误');
        }
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
        if (!isset($config['gateway'])) {
            $config['gateway'] = 'https://yun.944km.cn';
        }

        $privateKey = "-----BEGIN RSA PRIVATE KEY-----\n" . wordwrap($config['private_key'], 64, "\n", true) . "\n-----END RSA PRIVATE KEY-----";
        $pubKey = "-----BEGIN PUBLIC KEY-----\n" . wordwrap($config['public_key'], 64, "\n", true) . "\n-----END PUBLIC KEY-----";

        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {

            $encrypted = base64_decode($_POST['notify']);

            /** @noinspection PhpComposerExtensionStubsInspection */
            if (!@openssl_private_decrypt($encrypted, $decrypted, $privateKey)) {
                echo 'FAILED';
                Log::error('Pay.AiMing.verify failed, decrypt failed: ' . $encrypted);
                return false;
            }

            $data = json_decode($decrypted, true);
            $order_no = $data['order']['orderid']; // 本系统内订单号
            $total_fee = (int)round((float)$data['price'] * 100);
            $pay_trade_no = $data['order']['order_no']; //聚合支付系统订单号
            $successCallback($order_no, $total_fee, $pay_trade_no);
            echo 'OK';
            return true;

        } else {

            if (empty($config['out_trade_no'])) {
                // return page
                // 同步跳转
                return false;
            } else {
                // 主动查询
                // 不支持主动查询
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