<?php

namespace Gateway\Pay\ECPay;

use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;

include 'sdk/ECPay.Payment.Integration.php';

class Api implements ApiInterface
{
    //异步通知页面需要隐藏防止CC之类的验证导致返回失败
    private $url_notify = '';
    private $url_return = '';
    private $pay_id;

    public function __construct($id)
    {
        $this->url_notify = SYS_URL_API . '/pay/notify/' . $id;
        $this->url_return = SYS_URL . '/pay/return/' . $id;
        $this->pay_id = $id;
    }

    private function getECPayObj($config)
    {
        $obj = new \ECPay_AllInOne();

        //服務參數
        $obj->ServiceURL = isset($config['ServiceURL']) ? $config['ServiceURL'] : "https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5";  // 服務位置
        $obj->HashKey = $config['HashKey'];         // '5294y06JbISpM5x9';               // 測試用Hashkey，請自行帶入ECPay提供的HashKey
        $obj->HashIV = $config['HashIV'];           // 'v77hoKGq4kWxNNIS';               // 測試用HashIV，請自行帶入ECPay提供的HashIV
        $obj->MerchantID = $config['MerchantID'];   // '2000132';                        // 測試用MerchantID，請自行帶入ECPay提供的MerchantID
        $obj->EncryptType = \ECPay_EncryptType::ENC_SHA256;                              // CheckMacValue加密類型，請固定填入1，使用SHA256加密

        return $obj;
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
        if (!isset($config['HashKey'])) {
            throw new \Exception('請填寫 HashKey');
        }
        if (!isset($config['HashIV'])) {
            throw new \Exception('請填寫 HashIV');
        }
        if (!isset($config['MerchantID'])) {
            throw new \Exception('請填寫 MerchantID');
        }

        $amount = sprintf('%d', $amount_cent / 100); // 元为单位
        if (intval(round($amount * 100)) !== $amount_cent) {
            throw new \Exception('此支付方式只支持整數');
        }

        $all_methods = [
            \ECPay_PaymentMethod::ALL,
            \ECPay_PaymentMethod::Credit,
            \ECPay_PaymentMethod::WebATM,
            \ECPay_PaymentMethod::ATM,
            \ECPay_PaymentMethod::CVS,
            \ECPay_PaymentMethod::BARCODE,
            \ECPay_PaymentMethod::AndroidPay,
            \ECPay_PaymentMethod::GooglePay,
        ];
        if (!in_array($config['payway'], $all_methods)) {
            throw new \Exception('支付方式需要在 ' . join('/', $all_methods) . ' 其中');
        }

        try {
            $obj = $this->getECPayObj($config);

            //基本參數(請依系統規劃自行調整)
            $obj->Send['ReturnURL'] = $this->url_notify;                    // 付款完成通知回傳的網址
            $obj->Send['OrderResultURL'] = $this->url_return . '/' . $out_trade_no;               // 付款完成Client跳转网址
            $obj->Send['MerchantTradeNo'] = $out_trade_no;                  // 訂單編號
            $obj->Send['MerchantTradeDate'] = date('Y/m/d H:i:s');  // 交易時間
            $obj->Send['TotalAmount'] = $amount;                               // 交易金額
            $obj->Send['TradeDesc'] = $subject;                             // 交易描述
            $obj->Send['ChoosePayment'] = \ECPay_PaymentMethod::ALL;        // 付款方式:全功能

            //訂單的商品資料
            array_push($obj->Send['Items'], array(
                'Name' => $subject,
                'Price' => $amount,
                'Currency' => "元",
                'Quantity' => 1,
                'URL' => SYS_URL
            ));

            //產生訂單(auto submit至ECPay)
            $obj->CheckOut();

        } catch (\Throwable $e) {
            Log::error('Pay.ECPay.goPay.order failed', ['exception' => $e]);
            throw new \Exception('獲取付款信息失敗, 請聯系客服反饋');
        }
    }

    function verify($config, $successCallback)
    {
        $isNotify = isset($config['isNotify']) && $config['isNotify'];

        if ($isNotify) {

            try {
                // 收到綠界科技的付款結果訊息，並判斷檢查碼是否相符
                $obj = $this->getECPayObj($config);
                $feedback = $obj->CheckOutFeedback();

                // 以付款結果訊息進行相對應的處理
                /**
                 * 回傳的綠界科技的付款結果訊息如下:
                 * Array
                 * (
                 * [MerchantID] =>
                 * [MerchantTradeNo] =>
                 * [StoreID] =>
                 * [RtnCode] =>
                 * [RtnMsg] =>
                 * [TradeNo] =>
                 * [TradeAmt] =>
                 * [PaymentDate] =>
                 * [PaymentType] =>
                 * [PaymentTypeChargeFee] =>
                 * [TradeDate] =>
                 * [SimulatePaid] =>
                 * [CustomField1] =>
                 * [CustomField2] =>
                 * [CustomField3] =>
                 * [CustomField4] =>
                 * [CheckMacValue] =>
                 * )
                 */

                // 在網頁端回應 1|OK
                echo '1|OK';

                Log::debug('Pay.ECPay notify', ['$feedback' => $feedback]);
                $result = $feedback['RtnCode'] === 1 || $feedback['RtnCode'] === '1';
                $out_trade_no = $feedback['MerchantTradeNo'];  // 本系统订单号
                $total_fee = (int)round($feedback['TradeAmt'] * 100); // 元 -> 分
                $ecpay_no = $feedback['TradeNo']; // API渠道订单号

            } catch (\Throwable $e) {
                Log::error('Pay.ECPay notify failed', ['exception' => $e]);
                return false;
            }

        } else {
            // 可能是主动查询
            if (empty($config['out_trade_no'])) {
                // return_url 传递了 订单号, 不会到这一步
                throw new \Exception('internal error');
            } else {

                if ($_SERVER ['REQUEST_METHOD'] == 'POST') {
                    try {
                        // 收到綠界科技的付款結果訊息，並判斷檢查碼是否相符
                        $obj = $this->getECPayObj($config);
                        $feedback = $obj->CheckOutFeedback();

                        Log::debug('Pay.ECPay return page', ['$feedback' => $feedback]);
                        $result = $feedback['RtnCode'] === 1 || $feedback['RtnCode'] === '1';
                        $out_trade_no = $feedback['MerchantTradeNo'];  // 本系统订单号
                        $total_fee = (int)round($feedback['TradeAmt'] * 100); // 元 -> 分
                        $ecpay_no = $feedback['TradeNo']; // API渠道订单号

                    } catch (\Throwable $e) {
                        Log::error('Pay.ECPay notify failed', ['exception' => $e]);
                        return false;
                    }
                } else {
                    try {
                        $obj = $this->getECPayObj($config);
                        //基本參數(請依系統規劃自行調整)
                        $obj->Query['MerchantTradeNo'] = $config['out_trade_no'];
                        $obj->Query['TimeStamp'] = time();

                        //查詢訂單
                        $info = $obj->QueryTradeInfo();
                        Log::debug('Pay.ECPay query', ['$info' => $info]);

                        //顯示訂單資訊
                        // echo "<pre>" . print_r($info, true) . "</pre>";

                        $result = $info['TradeStatus'] === '1' || $info['TradeStatus'] === 1;
                        $out_trade_no = $info['MerchantTradeNo'];  // 本系统订单号
                        $total_fee = (int)round($info['TradeAmt'] * 100); // 元 -> 分
                        $ecpay_no = $info['TradeNo']; // API渠道订单号


                    } catch (\Throwable $e) {
                        Log::error('Pay.ECPay query failed', ['exception' => $e]);
                        return false;
                    }
                }
            }
        }

        if ($result) {
            $successCallback($out_trade_no, $total_fee, $ecpay_no);
        }

        return $result;
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
        return '此支付渠道不支持發起退款, 請手動操作';
    }
}