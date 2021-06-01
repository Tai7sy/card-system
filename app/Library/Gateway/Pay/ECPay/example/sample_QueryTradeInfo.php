<?php
/**
 *   查詢訂單範例
 */

    //載入SDK(路徑可依系統規劃自行調整)
    include('ECPay.Payment.Integration.php');
    try {

    	$obj = new ECPay_AllInOne();

        //服務參數
        $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/QueryTradeInfo/V5"; //服務位置
        $obj->HashKey     = '5294y06JbISpM5x9' ;                                            //測試用Hashkey，請自行帶入ECPay提供的HashKey
        $obj->HashIV      = 'v77hoKGq4kWxNNIS' ;                                            //測試用HashIV，請自行帶入ECPay提供的HashIV
        $obj->MerchantID  = '2000132';                                                      //測試用MerchantID，請自行帶入ECPay提供的MerchantID
        $obj->EncryptType = '1';                                                            //CheckMacValue加密類型，請固定填入1，使用SHA256加密

        //基本參數(請依系統規劃自行調整)
        $obj->Query['MerchantTradeNo'] = '2019091711192742';
        $obj->Query['TimeStamp']       = time() ;

        //查詢訂單
        $info = $obj->QueryTradeInfo();

        //顯示訂單資訊
        echo "<pre>" . print_r($info, true) . "</pre>";

    } catch (Exception $e) {
    	echo $e->getMessage();
    }


?>