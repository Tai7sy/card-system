<?php

/**
 * 即充宝
 * http://jcbpay.cn
 * 支付方式
 * 1=支付宝转账备注 2=支付宝扫码 3=财付通 4=手机QQ支付 5=微信支付
 * 软件接口填写如下:
 * 通知URL(后台支付渠道配置里面获取) + ?payno=#name&tno=#tno&money=#money&sign=#sign&key=接口KEY
 * 如 http://example.com/pay/return/1?payno=#name&tno=#tno&money=#money&sign=#sign&key=接口KEY
 */

namespace Gateway\Pay\JCBPay;

use App\Library\CurlRequest;
use App\Library\Helper;
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
        if (!isset($config['url'])) {
            throw new \Exception('请填写支付网页 [url]');
        }
        if (!isset($config['app_id'])) {
            throw new \Exception('请填写APPID [app_id]');
        }
        if (!isset($config['app_key'])) {
            throw new \Exception('请填写APPKEY [app_key]');
        }
        if (!isset($config['key'])) {
            throw new \Exception('请填写接口key [key]');
        }
        if (!isset($config['md5key'])) {
            throw new \Exception('请填写校验key [md5key]');
        }

        // 1=支付宝转账备注 2=支付宝扫码 3=财付通 4=手机QQ支付 5=微信支付

        header('Location: ' . $config['url'] . '/pay/pay.php?appid=' . $config['app_id'] .
            '&payno=' . $out_trade_no .
            '&typ=' . $config['payway'] .
            '&money=' . sprintf('%.2f', ($amount_cent / 100)) .
            '&back_url=' . $this->url_return);
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

            $getkey = $_REQUEST['key'];//接收参数key
            $tno = $_REQUEST['tno'];//接收参数tno 交易号
            $payno = $_REQUEST['payno'];//接收参数payno 一般是用户名 用户ID
            $money = $_REQUEST['money'];//接收参数money 付款金额
            $sign = $_REQUEST['sign'];//接收参数sign
            $typ = (int)$_REQUEST['typ'];//接收参数typ
            if ($typ == 1) {
                $typname = '手工充值';
            } else if ($typ == 2) {
                $typname = '支付宝充值';
            } else if ($typ == 3) {
                $typname = '财付通充值';
            } else if ($typ == 4) {
                $typname = '手Q充值';
            } else if ($typ == 5) {
                $typname = '微信充值';
            }
            if (!$tno) exit('没有订单号');
            if (!$payno) exit('没有付款说明');
            if ($getkey !== $config['key']) exit('KEY错误');
            if (strtoupper($sign) !== strtoupper(md5($tno . $payno . $money . $config['md5key']))) exit('签名错误');

            $order = \App\Order::whereOrderNo($payno)->first();
            if(!$order) exit('订单不存在');
            //此处作逻辑处理
            $successCallback($payno, (int)round($money * 100), $tno);

            //处理成功 输出1
            exit('1');
        } else {
            if (!empty($config['out_trade_no'])) {
                // 主动查询接口, 此接口不支持....
                // 此驱动, 不支持主动查询交易结果, 直接返回失败(未支付)
                return false;
            }


            // 同步返回接口
            if (!isset($_REQUEST['appid']) || !isset($_REQUEST['tno']) || !isset($_REQUEST['payno']) || !isset($_REQUEST['money']) || !isset($_REQUEST['typ']) || !isset($_REQUEST['paytime']) || !isset($_REQUEST['sign'])) {
                return false;
            }
            $appid = (int)$_REQUEST['appid'];
            $tno = $_REQUEST['tno'];//交易号 支付宝 微信 财付通 的交易号
            $payno = $_REQUEST['payno'];//网站充值的用户名
            $money = $_REQUEST['money'];//付款金额
            $typ = (int)$_REQUEST['typ'];
            $paytime = $_REQUEST['paytime'];
            $sign = $_REQUEST['sign'];
            if (!$appid || !$tno || !$payno || !$money || !$typ || !$paytime || !$sign) {
                exit('参数错误');
            }
            if ($config['app_id'] != $appid) exit('appid error');
            //sign 校验
            if ($sign != md5($appid . "|" . $config['app_key'] . "|" . $tno . "|" . $payno . "|" . $money . "|" . $paytime . "|" . $typ)) {
                exit('签名错误');
            }

            //处理用户充值
            if ($typ == 1) {
                $typname = '手工充值';
            } else if ($typ == 2) {
                $typname = '支付宝充值';
            } else if ($typ == 3) {
                $typname = '财付通充值';
            } else if ($typ == 4) {
                $typname = '手Q充值';
            } else if ($typ == 5) {
                $typname = '微信充值';
            }

            //此处作逻辑处理
            $successCallback($payno, (int)round($money * 100), $tno);

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