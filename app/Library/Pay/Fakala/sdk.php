<?php

class fakala
{

    public $gateway;
    public $uid;
    public $key;

    public function __construct($gateway, $id, $key)
    {
        $this->gateway = $gateway;
        $this->uid = $id;
        $this->key = $key;
    }

    function getSignStr($params)
    {
        ksort($params);
        $signStr = '';
        foreach ($params as $k => $v) {
            if ('sign' !== $k) {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        return $signStr;
    }

    function getSign($params, $key, &$out_url = false)
    {
        $signStr = self::getSignStr($params);
        $sign = md5($signStr . 'key=' . $key);
        if ($out_url !== false) {
            $out_url = $signStr . 'sign=' . $sign;
        }
        return $sign;
    }


    /**
     * @param string $payway 支付方式
     * @param string $subject 商品名称
     * @param string $out_trade_no 商户系统内唯一订单号
     * @param int $cost 商品成本(分), 用于后台统计利润, 不需要可输入0
     * @param int $total_fee 支付金额(分)
     * @param string $attach 附加信息
     * @param string $return_url 前台支付后跳转回的URL
     * @param string $notify_url 后台异步通知URL
     */
    function goPay($payway, $subject, $out_trade_no, $cost, $total_fee, $attach, $return_url, $notify_url)
    {
        $params = [
            'version' => '20190501',
            'uid' => (int)$this->uid,
            'subject' => $subject,
            'out_trade_no' => $out_trade_no,
            'total_fee' => (int)$total_fee, // 单位 分
            'cost' => (int)$cost, // 单位 分
            'payway' => $payway,
            'return_url' => $return_url,
            'notify_url' => $notify_url,
            'attach' => $attach,
        ];

        $params['sign'] = $this->getSign($params, $this->key);
        die('
<!doctype html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="' . $this->gateway . '/api/order" method="post">
    <input type="hidden" name="version" value="' . $params['version'] . '">
    <input type="hidden" name="uid" value="' . $params['uid'] . '">
    <input type="hidden" name="subject" value="' . $params['subject'] . '">
    <input type="hidden" name="out_trade_no" value="' . $params['out_trade_no'] . '">
    <input type="hidden" name="total_fee" value="' . $params['total_fee'] . '">
    <input type="hidden" name="cost" value="' . $params['cost'] . '">
    <input type="hidden" name="payway" value="' . $params['payway'] . '">
    <input type="hidden" name="return_url" value="' . $params['return_url'] . '">
    <input type="hidden" name="notify_url" value="' . $params['notify_url'] . '">
    <input type="hidden" name="attach" value="' . $params['attach'] . '">
    <input type="hidden" name="sign" value="' . $params['sign'] . '">
</form>
</body>
        ');
    }

    /**
     * 此函数仅供参考, 请务必再次验证付款金额
     * $_POST参数列表: uid, out_trade_no, order_no(系统订单号), total_fee, payway, attach
     */
    function notify_verify()
    {
        $params = $_POST;
        if ($params['sign'] === $this->getSign($params, $this->key)) {
            echo 'success';
            return true;
        } else {
            echo 'fail';
            return false;
        }
    }

    /**
     * 请尽量在notify中处理逻辑, 因为用户可能支付后关闭了网页, 因此不会跳到return_url
     * $_GET参数列表: 同上
     */
    function return_verify()
    {
        $params = $_GET;
        if ($params['sign'] === $this->getSign($params, $this->key)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 主动调用API查询是否成功, 用于没有服务器接受通知时的操作
     * @param $out_trade_no
     * @return array
     */
    function get_order($out_trade_no)
    {
        $result = $this->curl_post($this->gateway . '/api/order/query',
            'uid=' . $this->uid . '&out_trade_no=' . $out_trade_no);
        $result = @json_decode($result, true);
        if (is_array($result) && is_array($result['data']) && isset($result['data']['order'])) {
            return $result['data']['order'];
        }
        return [];
    }


    private function curl_post($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 返回获取的输出文本流
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); // SSL证书认证
        curl_setopt($curl, CURLOPT_POST, true); // post
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // post数据
        $response = curl_exec($curl);
        // var_dump(curl_error($curl)); // 如果执行curl过程中出现异常，可打开此开关，以便查看异常内容
        curl_close($curl);
        return $response;
    }
}


