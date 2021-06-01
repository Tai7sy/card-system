<?php
/**
 * 编码格式为UTF-8
 **/

class yekeAPI
{

    function __construct()
    {
    }

    /*
    *获取远程API支付方式
    */
    public static function getPayGate()
    {
        $params = array(
            'action' => 'getPayType',
            'userid' => yeke_USER_ID,
            'sign' => md5(yeke_USER_ID . yeke_USER_KEY),
        );
        $result = HttpClient::quickPost(yeke_API_GATE, $params);
        return $result;
    }

    /*
    *获取支付方式
    *网银、点卡、支付宝、财付通
    */
    public static function getPayType()
    {
        $list = json_decode(self::getPayGate(), true);
        $data = array();
        if ($list['status']) {
            foreach ($list['list'] as $key => $val) {
                $data[] = array(
                    'paytype' => $val['paytype'],
                    'channelname' => $val['channelname'],
                );
            }
        }
        return $data;
    }

    /*
    *获取通道列表
    *网银、点卡、支付宝、财付通
    */
    public static function getChannel($t)
    {
        $list = json_decode(self::getPayGate(), true);
        $data = array();
        if ($t == 'card') {
            if ($list['status'] && $list['list']) {
                foreach ($list['list'] as $val) {
                    if ($val['paytype'] == $t) {
                        foreach ($val['datalist'] as $key => $val2) {
                            $data[] = array(
                                'channelid' => $val2['channelid'],
                                'channelname' => $val2['channelname'],
                                'imgurl' => $val2['imgurl'],
                            );
                        }
                    }
                }
            }

        } else {

            if ($list['status'] && $list['list']) {
                foreach ($list['list'] as $val) {
                    if ($val['paytype'] == $t) {
                        foreach ($val['datalist'] as $key => $val2) {
                            $data[] = array(
                                'bankcode' => $val2['bankcode'],
                                'bankname' => $val2['bankname'],
                                'imgurl' => $val2['imgurl'],
                            );
                        }
                    }
                }
            }
        }
        return $data;
    }

    /*
    *获取点卡面值
    */
    public static function getCardValue()
    {
        $list = json_decode(self::getPayGate(), true);
        $data = array();
        if ($list['status'] && $list['list']) {
            foreach ($list['list'] as $val) {
                if ($val['paytype'] == 'card') {
                    foreach ($val['datalist'] as $key => $val) {
                        $data[] = array(
                            'channelid' => $val['channelid'],
                            'channelname' => $val['channelname'],
                            'cardvalue' => $val['cardvalue'],
                            'cardlength' => $val['cardlength'],
                        );
                    }
                }
            }
        }
        return $data;
    }

    /*
    *生成订单号
    *例子：20130514150801342956
    */
    public static function getOrderID()
    {
        return date('Y') . date('m') . date('d') . date('H') . date('i') . date('s') . rand(100000, 999999);
    }

    /*
    *API网关接口
    *网银、支付宝和财付通充值直连支付，卡类将返回处理结果
    */
    public function payGate($params)
    {
        $params = array_merge(array('P_userid' => yeke_USER_ID), $params);
        $sign = $this->makeSign($params);
        $params = array_merge($params, array('P_sign' => $sign, 'action' => 'payGate'));

        switch ($params['P_paytype']) {
            case 'bank':
            case 'alipay':
            case 'tenpay':
            case 'weixin':
            case 'wxwap':
            case 'sqzf':
                return $this->payGateBank($params);
                break;
            case 'card':
                return $this->payGateCard($params);
                break;
            default:
                return 'error,支付方式错误';
        }
    }

    public function payGateBank($params)
    {
        $str = '<html><head><meta http-equiv="content-type" content="text/html;charset=utf-8"><title>请稍候，正在跳转...</title></head>';
        $str .= '<body onload="document.pay.submit()">';
        $str .= '请稍候，正在跳转...';
        $str .= '<form name="pay" action=' . yeke_API_GATE . ' method="post">';
        foreach ($params as $key => $val) {
            $str .= '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
        $str .= '</body></html>';
        return $str;
    }

    public function payGateCard($params)
    {
        if ($params['P_cardnum'] == '' || $params['P_cardpwd'] == '' || $params['P_cardvalue'] == '') {
            return 'error,卡信息不完整';
        }
        $result = HttpClient::quickPost(yeke_API_GATE, $params);
        return $result;
    }

    /*
    *生成签名串
    */
    public function makeSign($params)
    {
        $str = '';
        foreach ($params as $key => $val) {
            $str .= $str ? '&' : '';
            $str .= $key . '=' . $val;
        }
        //die ($str.yeke_USER_KEY);
        $sign = md5($str . yeke_USER_KEY);
        return $sign;
    }

    /*
    *验证notify_url
    */
    public function verifyNotify()
    {
        if (empty($_POST)) {
            return false;
        }
        $_POST['P_productname'] = urlencode($_POST['P_productname']);
        $_POST['P_productinfo'] = urlencode($_POST['P_productinfo']);
        $_POST['P_remark'] = urlencode($_POST['P_remark']);
        $_POST['P_custom_1'] = urlencode($_POST['P_custom_1']);
        $_POST['P_custom_2'] = urlencode($_POST['P_custom_2']);
        $_POST['P_contact'] = urlencode($_POST['P_contact']);

        //删除sign
        $P_sign = $_POST['P_sign'];
        foreach ($_POST as $key => $val) {
            if ($key == 'P_sign') {
                unset($_POST['P_sign']);
            }
        }

        $makeSign = $this->makeSign($_POST);
        //写入日志
        $this->logs($_POST['P_api_orderid'], $_POST, $P_sign . '=' . $makeSign);

        if ($P_sign == $makeSign) {
            return true;
        } else {
            return false;
        }
    }

    /*
    *验证return_url
    */
    public function verifyReturn()
    {
        if (empty($_GET)) {
            return false;
        }

        $_GET['P_productname'] = urlencode($_GET['P_productname']);
        $_GET['P_productinfo'] = urlencode($_GET['P_productinfo']);
        $_GET['P_remark'] = urlencode($_GET['P_remark']);
        $_GET['P_custom_1'] = urlencode($_GET['P_custom_1']);
        $_GET['P_custom_2'] = urlencode($_GET['P_custom_2']);
        $_GET['P_contact'] = urlencode($_GET['P_contact']);
        //删除sign
        $P_sign = $_GET['P_sign'];
        foreach ($_GET as $key => $val) {
            if ($key == 'P_sign') {
                unset($_GET['P_sign']);
            }
        }

        $makeSign = $this->makeSign($_GET);
        if ($P_sign == $makeSign) {
            return true;
        } else {
            return false;
        }
    }

    /*
    *写入日志
    */
    public function logs($orderid, $params, $makeSign)
    {
        date_default_timezone_set('PRC');
        if (!empty($params)) {
            $str = '';
            foreach ($params as $key => $val) {
                $str .= $str ? '&' : '';
                $str .= $key . '=' . $val;
            }

            $content = date('Y-m-d H:i:s') . "\r\n" . $orderid . "\r\n" . $str . "\r\n" . $makeSign . "\r\n\r\n........................................\r\n\r\n";
            $logdir = 'log';
            if (!file_exists($logdir)) mkdir($logdir, 0777, true);
            $filename = yeke_USER_LOG_PREFIX . '-' . date('Y-m-d') . '.txt';
            $fp = fopen($logdir . '/' . $filename, 'ab');
            fwrite($fp, $content);
            fclose($fp);
        }
    }
}

?>