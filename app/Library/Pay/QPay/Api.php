<?php

namespace App\Library\Pay\QPay;

use App\Library\Helper;
use App\Library\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

require_once(__DIR__ . '/qpay_mch_sp/qpayMchAPI.class.php');

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';

    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
    }

    /**
     * @param array $config
     * @param string $out_trade_no 支付系统的订单号, 对于微信来说 是外部订单号
     * @param string $subject
     * @param string $body
     * @param int $amount_cent
     * @throws \Exception
     */
    function goPay($config, $out_trade_no, $subject, $body, $amount_cent)
    {
        if (!isset($config['mch_id']) || !isset($config['mch_key']))
            throw new \Exception('请设置 mch_id 和 mch_key');

        // Log::debug('Pay.QPay.goPay, order_no:' . $out_trade_no.', step1');
        //入参
        $params = array(
            'out_trade_no' => $out_trade_no,
            'body' => $subject,
            'device_info' => 'qq_19060',
            'fee_type' => 'CNY',
            'notify_url' => $this->url_notify,
            'spbill_create_ip' => Helper::getIP(),
            'total_fee' => $amount_cent,
            'trade_type' => 'NATIVE'
        );
        //api调用
        $qpayApi = new \QpayMchAPI('https://qpay.qq.com/cgi-bin/pay/qpay_unified_order.cgi', null, 10);
        $retXml = $qpayApi->req($params, $config);
        $result = \QpayMchUtil::xmlToArray($retXml);

        if (!isset($result['code_url'])) {
            Log::error('Pay.QPay.goPay, order_no:' . $out_trade_no . ', error:' . json_encode($result));

            if (isset($result['err_code_des']))
                throw new \Exception($result['err_code_des']);

            if (isset($result['return_msg']))
                throw new \Exception($result['return_msg']);

            throw new \Exception('获取支付数据失败');
        }
        // Log::debug('Pay.Wechat.goPay, order_no:' . $out_trade_no.', step4');
        header('location: /qrcode/pay/' . $out_trade_no . '/qq?url=' . urlencode($result['code_url']));
        exit;
    }


    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        $qpayApi = new \QpayMchAPI('https://qpay.qq.com/cgi-bin/pay/qpay_order_query.cgi', null, 10);

        if ($isNotify) {
            $params = $qpayApi->notify_params();
            if (!$qpayApi->notify_verify($params, $config)) {
                echo '<xml><return_code>FAIL</return_code></xml>';
                return false;
            }
            call_user_func_array($successCallback, [$params['out_trade_no'], $params['total_fee'], $params['transaction_id']]);
            echo '<xml><return_code>SUCCESS</return_code></xml>';
            return true;
        } else {
            $out_trade_no = @$config['out_trade_no'];

            $params = array(
                'out_trade_no' => $out_trade_no,
            );
            //api调用
            $retXml = $qpayApi->req($params, $config);
            $result = \QpayMchUtil::xmlToArray($retXml);
            if (!is_array($result)) {
                Log::error('Pay.QPay.verify Error, $retXml' . $retXml);
                return false;
            }

            if (array_key_exists('trade_state', $result) && $result['trade_state'] == 'SUCCESS') {
                call_user_func_array($successCallback, [$result['out_trade_no'], $result['total_fee'], $result['transaction_id']]);
                return true;
            } else {
                return false;
            }
        }
    }
}