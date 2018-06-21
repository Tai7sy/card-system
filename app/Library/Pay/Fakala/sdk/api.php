<?php
/**
 * TODO 请修改此处为你的 id 和 key
 */
define('APP_ID', 'your_id');
define('APP_KEY', 'your_key');

class fakala
{

    public $uid = APP_ID;
    public $key = APP_KEY;


    function getSignStr($params){
        ksort($params);
        $signStr = '';
        foreach ($params as $k => $v) {
            if ('sign' !== $k) {
                $signStr .= $k . '=' . ($v ? $v : '') . '&';
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
     * @param string $out_trade_no 商户系统内唯一订单号
     * @param int $cost 商品成本(分), 用于后台统计利润, 不需要可输入0
     * @param int $total_fee 支付金额(分)
     * @param string $attach 附加信息
     * @param string $return_url 前台支付后跳转回的URL
     * @param string $notify_url 后台异步通知URL
     */
    function goPay($payway, $out_trade_no, $cost, $total_fee, $attach, $return_url, $notify_url)
    {
        $params = [
            'uid' => (int)$this->uid,
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
    <title>正在转到付款页</title>
</head>
<body onload="document.pay.submit()">
<form name="pay" action="http://fakala.xyz/api/order" method="post">
    <input type="hidden" name="uid" value="' . $params['uid'] . '">
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
            echo 'return success';
            return true;
        } else {
            echo 'return fail';
            return false;
        }
    }

}


