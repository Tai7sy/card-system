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

        $obj->Funding['PayDateType']   = 'close';                         
        $obj->Funding['StartDate']     = '2017-09-01';                      
        $obj->Funding['EndDate']       = '2017-12-31';                      

        $result = $obj->FundingReconDetail();
        echo $result ;
        file_put_contents('new.csv', $result);

    } catch (Exception $e) {
    	echo $e->getMessage();
    } 

?>