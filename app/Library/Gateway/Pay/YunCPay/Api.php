<?php

/**
 * 云创支付
 * http://www.yuncpay.com/member/api
 * https://www.kancloud.cn/lym2155555546/yczf1/943883
 *
 *
 * 支付方式:
 * alipay   支付宝
 * tenpay   财付通
 * wxscan   微信扫码
 * aliwap   支付宝wap
 * tenwap   财付通wap
 * qqscan   手Q扫码
 * wxwap    微信wap
 * qqwap    QQ钱包wap
 * aliscan  支付宝扫码
 * wxgzh    微信公众号
 * bank-xx  网银直连, xx代表银行代码, 取值:
 *                   ABC     中国农业银行
 *                   BOCSH   中国银行
 *                   CCB     建设银行
 *                   CMB     招商银行
 *                   SPDB    浦发银行
 *                   GDB     广发银行
 *                   BOCOM   交通银行
 *                   PSBC    邮政储蓄银行
 *                   CNCB    中信银行
 *                   CMBC    民生银行
 *                   CEB     光大银行
 *                   HXB     华夏银行
 *                   CIB     兴业银行
 *                   BOS     上海银行
 *                   SRCB    上海农商
 *                   PAB     平安银行
 *                   BCCB    北京银行
 *
 */

namespace Gateway\Pay\YunCPay;

use App\Library\CurlRequest;
use App\Library\Helper;
use App\Library\UrlShorten;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

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

        $version = '1.0';
        $customerid = $config['id'];
        $sdorderno = $out_trade_no;

        $paytype = $config['payway'];
        $bankcode = '';
        if (substr($paytype, 0, 4) === 'bank') {
            $bankcode = substr($paytype, 5);
            $paytype = 'bank';
        }

        $remark = '';
        $is_qrcode = @intval($config['is_qrcode']);

        $sign = md5('version=' . $version . '&customerid=' . $customerid .
            '&total_fee=' . $total_fee . '&sdorderno=' . $sdorderno .
            '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $config['key']);


        if (!$is_qrcode) {
            ?>
            <!doctype html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>正在转到付款页</title>
            </head>
            <body onload="document.pay.submit()">
            <form name="pay" action="http://api.yuncpay.com/api/submit" method="get">
                <input type="hidden" name="version" value="<?php echo $version ?>">
                <input type="hidden" name="customerid" value="<?php echo $customerid ?>">
                <input type="hidden" name="sdorderno" value="<?php echo $sdorderno ?>">
                <input type="hidden" name="total_fee" value="<?php echo $total_fee ?>">
                <input type="hidden" name="paytype" value="<?php echo $paytype ?>">
                <input type="hidden" name="notifyurl" value="<?php echo $this->url_notify ?>">
                <input type="hidden" name="returnurl" value="<?php echo $this->url_return ?>">
                <input type="hidden" name="remark" value="<?php echo $remark ?>">
                <input type="hidden" name="bankcode" value="<?php echo $bankcode ?>">
                <input type="hidden" name="is_qrcode" value="0">
                <input type="hidden" name="sign" value="<?php echo $sign ?>">
            </form>
            </body>
            </html>

            <?php
        } else {
            $post = 'version=' . $version .
                '&customerid=' . $customerid .
                '&sdorderno=' . $sdorderno .
                '&total_fee=' . $total_fee .
                '&paytype=' . $paytype .
                '&notifyurl=' . urlencode($this->url_notify) .
                '&returnurl=' . urlencode($this->url_return) .
                '&remark' . urlencode($remark) .
                '&bankcode=' . $bankcode .
                '&is_qrcode=1' .
                '&sign=' . $sign;
            $ret_raw = CurlRequest::post('http://api.yuncpay.com/api/submit', $post);
            $ret = json_decode($ret_raw, true);
            if (!isset($ret['status']) || $ret['status'] !== 1 || empty($ret['code_url'])) {
                $ret = [
                    'code_url' => Helper::str_between($ret_raw, "='", "'")
                ];
                if (!starts_with($ret['code_url'], 'http')) {
                    Log::error('Pay.YunCPay.order Error: ' . $ret_raw);
                    throw new \Exception('获取付款信息超时, 请刷新重试');
                }
                $ret['code_url'] = UrlShorten::shorten($ret['code_url']);
            }

            if ($paytype === 'alipay' || $paytype === 'aliwap' || $paytype === 'aliscan') {
                header('location: /qrcode/pay/' . $out_trade_no . '/aliqr?url=' . urlencode($ret['code_url']));
            } elseif ($paytype === 'wxscan' || $paytype === 'wxwap' || $paytype === 'wxgzh') {
                header('location: /qrcode/pay/' . $out_trade_no . '/wechat?url=' . urlencode($ret['code_url']));
            } elseif ($paytype === 'qqscan' || $paytype === 'qqwap') {
                header('location: /qrcode/pay/' . $out_trade_no . '/qq?url=' . urlencode($ret['code_url']));
            } else {
                throw new \Exception('该支付方式不支持扫码');
            }
        }
        exit;
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
            $status = $_POST['status'];
            $customerid = $_POST['customerid'];
            $sdorderno = $_POST['sdorderno']; // 商户订单号
            $total_fee = $_POST['total_fee']; // 订单金额 元
            $paytype = $_POST['paytype'];
            $sdpayno = $_POST['sdpayno']; // 平台订单号
            $sign = $_POST['sign'];

            $mysign = md5('customerid=' . $customerid . '&status=' . $status . '&sdpayno=' . $sdpayno . '&sdorderno=' . $sdorderno . '&total_fee=' . $total_fee . '&paytype=' . $paytype . '&' . $config['key']);

            if ($sign === $mysign) {
                if ($status === '1') {
                    $total_fee = (int)round($total_fee * 100);
                    $successCallback($sdorderno, $total_fee, $sdpayno);
                    echo 'success';
                    return true;
                } else {
                    echo 'success'; // 表示收到通知
                }
            } else {
                echo 'sign_err';
            }
        } else {
            if (!empty($config['out_trade_no']) || (!isset($_GET['sign']) && isset($_GET['sdorderno']))) {
                $sdorderno = '';
                if (!empty($config['out_trade_no'])) {
                    $sdorderno = $config['out_trade_no'];
                } elseif (isset($_GET['sdorderno'])) {
                    $sdorderno = $_GET['sdorderno'];
                }
                $post = 'customerid=' . $config['id'] . '&sdorderno=' . $sdorderno . '&reqtime=' . date('YmdHis');
                $post .= '&sign=' . md5($post . '&' . $config['key']);
                $ret_raw = CurlRequest::post('http://api.yuncpay.com/api/query', $post);
                $ret = json_decode($ret_raw, true);
                if (!isset($ret['status'])) {
                    Log::error('Pay.YunCPay.verify Error: ' . $ret_raw);
                }
                if ($ret['status'] === 1) {
                    $total_fee = (int)round($ret['total_fee'] * 100);
                    $successCallback($ret['sdorderno'], $total_fee, $ret['sdpayno']);
                    return true;
                }

                // 查询页面点击支付 先查询一下, 不支持
                return false;
            }
            $status = $_GET['status'];
            $customerid = $_GET['customerid'];
            $sdorderno = $_GET['sdorderno'];
            $total_fee = $_GET['total_fee'];
            $paytype = $_GET['paytype'];
            $sdpayno = $_GET['sdpayno'];
            $sign = $_GET['sign'];

            $mysign = md5('customerid=' . $customerid . '&status=' . $status . '&sdpayno=' . $sdpayno . '&sdorderno=' . $sdorderno . '&total_fee=' . $total_fee . '&paytype=' . $paytype . '&' . $config['key']);

            if ($sign === $mysign) {
                if ($status === '1') {
                    $total_fee = (int)round($total_fee * 100);
                    $successCallback($sdorderno, $total_fee, $sdpayno);
                    return true;
                }
            }
        }
        return false;
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