<?php

/**
 * 幻兮支付
 * https://www.zhapay.com/
 * https://www.zhapay.com/apidoc.html
 *
 *
 * 支付方式:
 * 1微信、2支付宝
 *
 */

namespace Gateway\Pay\ZhaPay;

use Gateway\Pay\ApiInterface;

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';

    /**
     * 需要传入支付方式ID (因为一个支付方式可能添加了多次)
     * ApiInterface constructor.
     * @param $id
     */
    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
    }

    /**
     * @param array $config 支付渠道配置
     * @param string $out_trade_no 外部订单号
     * @param string $subject 商品名称
     * @param string $body 商品介绍
     * @param int $amount_cent 金额/分
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        $total_fee = sprintf('%.2f', $amount_cent / 100); // 元为单位

        if (!isset($config['id'])) {
            throw new \Exception('请设置id');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请设置key');
        }
        if (!isset($config['gateway'])) {
            throw new \Exception('请设置gateway');
        }

        $mch_id = $config['id'];
        $mch_key = $config['key'];
        $data = array(
            "mch_uid" => $mch_id,
            "out_trade_no" => $out_trade_no,
            "pay_type_id" => $config['payway'], // 1微信支付 2支付宝
            "mepay_type" => '2',                // 收款方式：2 个人收款码
            "total_fee" => $total_fee,//金额
            "notify_url" => $this->url_notify,
            "return_url" => $this->url_return . '/' . $out_trade_no, // 由于没有同步通知, 这里同步过程需要拿着订单号看下是否订单处理完了
        );

        ksort($data);
        reset($data);
        $sign_str = '';
        $urls = '';
        foreach ($data AS $key => $val) {
            if ($val == '' || $key == 'sign') continue;
            if ($sign_str != '') { //后面追加&拼接URL
                $sign_str .= "&";
                $urls .= "&";
            }
            $sign_str .= "$key=$val";
            $urls .= "$key=" . urlencode($val);
        }
        $query = $urls . '&sign=' . md5($sign_str . $mch_key);
        header('Location: ' . $config['gateway'] . '?' . $query); //跳转到支付页面
        exit(0);
    }

    /**
     * @param $config
     * @param callable $successCallback 成功回调 (系统单号,交易金额/分,支付渠道单号)
     * @return bool|string true 验证通过  string 失败原因
     * @throws \Exception
     */
    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];
        if ($isNotify) {
            // 异步通知交易结果
            ksort($_POST);
            reset($_POST);
            $mch_key = $config['key'];
            $sign_str = '';
            foreach ($_POST AS $key => $val) { //遍历POST参数
                if ($val == '' || $key == 'sign') continue; //跳过这些不签名
                if ($sign_str) $sign_str .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
                $sign_str .= "$key=$val"; //拼接为url参数形式
            }
            if (!$_POST['transaction_id'] || md5($sign_str . $mch_key) !== $_POST['sign'] || $_POST['status'] != 1) { //不合法的数据
                echo 'fail';
                return false;
            } else {
                //合法的数据, 进行业务处理
                $successCallback($_POST['out_trade_no'], (int)round((float)$_POST['total_fee'] * 100), $_POST['transaction_id']);
                echo 'success';
                return true;
            }
        } else {
            // 主动查询交易结果
            if (!empty($config['out_trade_no'])) {
                $order_no = @$config['out_trade_no'];  //商户订单号
                return false; // 不支持
            }

            // // 同步返回页面
            // ksort($_GET);
            // reset($_GET);
            // $mch_key = $config['key'];
            // $sign_str = '';
            // foreach ($_GET AS $key => $val) { //遍历POST参数
            //     if ($val == '' || $key == 'sign') continue; //跳过这些不签名
            //     if ($sign_str) $sign_str .= '&'; //第一个字符串签名不加& 其他加&连接起来参数
            //     $sign_str .= "$key=$val"; //拼接为url参数形式
            // }

            // // http://127.0.0.4/pay/return/15?mepay_total=0.01&order=20200105143614Mypoz&out_trade_no=20200105143614Mypoz&pay_type=1&paytime=1578206222&status=1&total_fee=0.01&transaction_id=1000020200105143634178647&sign=ab0bfcd4348aa271241d1508286d1e99
            // // mepay_total=0.01&order=20200105143614Mypoz&out_trade_no=20200105143614Mypoz&pay_type=1&paytime=1578206222&status=1&total_fee=0.01&transaction_id=10000202001051436341786471
            // var_dump($sign_str . $mch_key);
            // var_dump(md5($sign_str . $mch_key));
            // if (!$_GET['transaction_id'] || md5($sign_str . $mch_key) !== $_GET['sign'] || $_GET['status'] !== '1') { //不合法的数据
            //     return false;
            // } else {
            //     //合法的数据, 进行业务处理
            //     $successCallback($_GET['out_trade_no'], (int)round((float)$_GET['total_fee'] * 100), $_GET['transaction_id']);
            //     return true;
            // }

            // 根据作者所说不需要同步
            return false;
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