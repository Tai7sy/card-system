<?php

namespace Xhat\Payjs;

class Payjs
{
    private $mchid;
    private $key;
    private $api_url_native;
    private $api_url_cashier;
    private $api_url_refund;
    private $api_url_close;
    private $api_url_check;
    private $api_url_user;
    private $api_url_info;
    private $api_url_bank;

    public function __construct($config = null)
    {
        if (!$config) exit('config needed');

        $this->mchid = $config['mchid'];
        $this->key = $config['key'];
        $api_url = isset($config['api_url']) ? $config['api_url'] : 'https://payjs.cn/api/';

        $this->api_url_native = $api_url . 'native';
        $this->api_url_cashier = $api_url . 'cashier';
        $this->api_url_refund = $api_url . 'refund';
        $this->api_url_close = $api_url . 'close';
        $this->api_url_check = $api_url . 'check';
        $this->api_url_user = $api_url . 'user';
        $this->api_url_info = $api_url . 'info';
        $this->api_url_bank = $api_url . 'bank';
    }

    // 扫码支付
    public function native(array $data)
    {
        $this->url = $this->api_url_native;
        return $this->post($data);
    }

    // 收银台模式
    public function cashier(array $data)
    {
        $this->url = $this->api_url_cashier;
        $data = $this->sign($data);
        $url = $this->url . '?' . http_build_query($data);
        return $url;
    }

    // 退款
    public function refund($payjs_order_id)
    {
        $this->url = $this->api_url_refund;
        $data = ['payjs_order_id' => $payjs_order_id];
        return $this->post($data);
    }

    // 关闭订单
    public function close($payjs_order_id)
    {
        $this->url = $this->api_url_close;
        $data = ['payjs_order_id' => $payjs_order_id];
        return $this->post($data);
    }

    // 检查订单
    public function check($payjs_order_id)
    {
        $this->url = $this->api_url_check;
        $data = ['payjs_order_id' => $payjs_order_id];
        return $this->post($data);
    }

    // 用户资料
    public function user($openid)
    {
        $this->url = $this->api_url_user;
        $data = ['openid' => $openid];
        return $this->post($data);
    }

    // 商户资料
    public function info()
    {
        $this->url = $this->api_url_info;
        $data = [];
        return $this->post($data);
    }

    // 银行资料
    public function bank($name)
    {
        $this->url = $this->api_url_bank;
        $data = ['bank' => $name];
        return $this->post($data);
    }

    // 异步通知接收
    public function notify()
    {
        $data = $_POST;
        if ($this->checkSign($data) === true) {
            return $data;
        } else {
            return '验签失败';
        }
    }

    // 数据签名
    public function sign(array $data)
    {
        $data['mchid'] = $this->mchid;
        array_filter($data);
        ksort($data);
        $data['sign'] = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->key)));
        return $data;
    }

    // 校验数据签名
    public function checkSign($data)
    {
        $in_sign = $data['sign'];
        unset($data['sign']);
        array_filter($data);
        ksort($data);
        $sign = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->key)));
        return $in_sign == $sign ? true : false;
    }


    private static function curl_post($url, $post_data = '')
    {
        $headers['Accept'] = "*/*";
        $headers['Referer'] = $url;
        $headers['Content-Type'] = "application/x-www-form-urlencoded";
        $headers['User-Agent'] = "ni shi sha bi ba, yi ge sdk hai you di san fang ku";

        $sendHeaders = array();
        foreach ($headers as $headerName => $headerVal) {
            $sendHeaders[] = $headerName . ': ' . $headerVal;
        }
        $sendHeaders[] = 'Expect:';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);

        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//返回获取的输出文本流
        curl_setopt($ch, CURLOPT_HEADER, 1);//将头文件的信息作为数据流输出
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $sendHeaders);
        $response = curl_exec($ch);

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $headerSize);
        curl_close($ch);

        return $body;
    }


    // 数据发送
    public function post($data)
    {
        $data = $this->sign($data);

        /*
        $client = new \GuzzleHttp\Client([
            'header' => ['User-Agent' => 'PAYJS Larevel Http Client'],
            'timeout' => 10,
            'http_errors' => false,
            'defaults' => ['verify' => false],
        ]);
        $rst = $client->request('POST', $this->url, ['form_params' => $data]);
        */
        return json_decode(self::curl_post($this->url, http_build_query($data)), true);
    }

}
