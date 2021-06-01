<?php

namespace Gateway\Pay\WeChat;

require_once __DIR__ . '/lib/WxPay.Api.php';
require_once __DIR__ . '/lib/WxPay.Notify.php';
require_once 'WxLog.php';


class PayNotifyCallBack extends \WxPayNotify
{
    /**
     * @var callable
     */
    private $successCallback = null;
    public function __construct($successCallback)
    {
        $this->successCallback = $successCallback;
    }

    //查询订单
    public function QueryOrder($transaction_id)
    {
        $input = new \WxPayOrderQuery();
        $input->SetTransaction_id($transaction_id);
        $result = \WxPayApi::orderQuery($input);
        \WxLog::DEBUG('query:' . json_encode($result));
        if (array_key_exists('return_code', $result) &&
            array_key_exists('result_code', $result) &&
            $result['return_code'] == 'SUCCESS' &&
            $result['result_code'] == 'SUCCESS') {
            return true;
        }
        return false;
    }

    //重写回调处理函数
    public function NotifyProcess($data, &$msg)
    {
        \WxLog::DEBUG('call back:' . json_encode($data));

        if (!array_key_exists('transaction_id', $data)) {
            $msg = '输入参数不正确';
            \WxLog::DEBUG('begin process 输入参数不正确');
            return false;
        }
        //查询订单，判断订单真实性
        if (!$this->QueryOrder($data['transaction_id'])) {
            $msg = '订单查询失败';
            \WxLog::DEBUG('begin process 订单查询失败');
            return false;
        }
        //貌似是这里处理流程?  卧槽他大爷的微信开发者
        //\WxLog::DEBUG('begin process:'.$data['attach'].'--'.$data['total_fee'].'--'.$data['out_trade_no']);
        
        if($this->successCallback){
            call_user_func_array($this->successCallback,[$data['out_trade_no'],$data['total_fee'],$data['transaction_id']]);
        }
        return true;
    }
}

//\WxLog::DEBUG('begin notify');
