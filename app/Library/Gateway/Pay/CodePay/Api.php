<?php

namespace Gateway\Pay\CodePay;


use Gateway\Pay\ApiInterface;
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


    // payway: alipay qq weixin
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $amount = sprintf('%.2f', $amount_cent / 100); //元为单位

        $codepay_id = $config['id'];//这里改成码支付ID
        $codepay_key = $config['key']; //这是您的通讯密钥


        switch ($config['payway']) {
            case 'alipay':
                $type = 1;
                break;
            case 'qq':
                $type = 2;
                break;
            case 'weixin':
                $type = 3;
                break;
            default:
                throw new \Exception('支付方式填写错误, alipay/qq/weixin');
        }

        $data = array(
            'id' => $codepay_id,//你的码支付ID
            'pay_id' => $out_trade_no, //唯一标识 可以是用户ID,用户名,session_id(),订单ID,ip 付款后返回
            'type' => $type, //1支付宝支付 3微信支付 2QQ钱包
            'price' => $amount,//金额100元
            'param' => '',//自定义参数
            'notify_url' => $this->url_notify,
            'return_url' => $this->url_return
        ); //构造需要传递的参数

        ksort($data); //重新排序$data数组=

        $sign = ''; //初始化需要签名的字符为空
        $urls = ''; //初始化URL参数为空

        foreach ($data AS $key => $val) { //遍历需要传递的参数
            if ($val == '' || $key == 'sign') continue; //跳过这些不参数签名
            if ($sign != '') { //后面追加&拼接URL
                $sign .= '&';
                $urls .= '&';
            }
            $sign .= "$key=$val"; //拼接为url参数形式
            $urls .= "$key=" . urlencode($val); //拼接为url参数形式并URL编码参数值
        }
        $query = $urls . '&sign=' . md5($sign . $codepay_key); //创建订单所需的参数

        // var_dump("加载中");
        header('Location: http://api3.xiuxiu888.com/creat_order/?' . $query); //跳转到支付页面
        exit;
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        $codepay_id = $config['id'];   //这里改成码支付ID
        $codepay_key = $config['key']; //这是您的通讯密钥

        if (empty($_POST)) { //如果GET访问
            $_POST = $_GET;  //POST访问 为服务器或软件异步通知  不需要返回HTML
        }
        ksort($_POST); //排序post参数
        reset($_POST); //内部指针指向数组中的第一个元素

        $sign = ''; //加密字符串初始化

        foreach ($_POST AS $key => $val) {
            if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            if ($sign) $sign .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            $sign .= "$key=$val"; //拼接为url参数形式
        }

        // $price = (float)$_POST['price']; //订单的原价
        // $param = $_POST['param']; //自定义参数
        // $type = (int)$_POST['type']; //支付方式

        if (!isset($_POST['pay_no']) || md5($sign . $codepay_key) != $_POST['sign']) { //不合法的数据
            if ($isNotify) echo 'fail';  //返回失败 继续补单
            return false;
        } else {
            if (!isset($_POST['pay_id'])) {
                Log::error('Pay.CodePay.verify ERROR: there is no pay_id in $_POST: ' . json_encode($_POST));
                if ($isNotify) echo 'fail';  //返回失败 继续补单
                return false;
            }

            // price 提交订单的金额
            // money 实际付款的金额, 码支付用于区分不同订单而设立的规则

            $out_trade_no = $_POST['pay_id']; //需要充值的ID 或订单号 或用户名
            $total_fee = (int)round($_POST['price'] * 100);
            $trade_no = $_POST['pay_no']; //流水号
            $successCallback($out_trade_no, $total_fee, $trade_no);

            if ($isNotify) echo 'success';
            return true;
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