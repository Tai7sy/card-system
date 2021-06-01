<?php
/**
*   一般產生訂單(全功能)範例
*/
    
    //載入SDK(路徑可依系統規劃自行調整)
    include('ECPay.Payment.Integration.php');
    try {
        
    	$obj = new ECPay_AllInOne();
   
        //服務參數
        $obj->ServiceURL  = "https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5";  //服務位置
        $obj->HashKey     = '5294y06JbISpM5x9' ;                                          //測試用Hashkey，請自行帶入ECPay提供的HashKey
        $obj->HashIV      = 'v77hoKGq4kWxNNIS' ;                                          //測試用HashIV，請自行帶入ECPay提供的HashIV
        $obj->MerchantID  = '2000132';                                                    //測試用MerchantID，請自行帶入ECPay提供的MerchantID
        $obj->EncryptType = '1';                                                          //CheckMacValue加密類型，請固定填入1，使用SHA256加密


        //基本參數(請依系統規劃自行調整)

        $obj->Send['DateType']      = $MerchantTradeNo;                         
        $obj->Send['BeginDate']     = date('Y/m/d H:i:s');                      
        $obj->Send['EndDate']       = 2000;                                       
        $obj->Send['MediaFormated'] = "good to drink" ;                           

        $obj->TradeNoAio();
        
    } catch (Exception $e) {
    	echo $e->getMessage();
    } 


 
?>