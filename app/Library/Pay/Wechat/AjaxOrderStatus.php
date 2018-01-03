<?php
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(E_ERROR);
require("../func.php");
require_once "lib/WxPay.Api.php";
require_once 'log.php';


function printf_info($data)
{
    foreach ($data as $key => $value) {
        echo "<font color='#f00;'>$key</font> : $value <br/>";
    }
}

if (isset($_REQUEST["BillNO"]) && $_REQUEST["BillNO"] != "") {
    $out_trade_no = $_REQUEST["BillNO"];
    $input = new WxPayOrderQuery();
    $input->SetOut_trade_no($out_trade_no);
    $result = WxPayApi::orderQuery($input);
    if (array_key_exists("trade_state", $result) && $result["trade_state"] == "SUCCESS") {
        //printf_info($result);exit;
        $ret = array('ret' => 1, 'url' => $domain_web . "/payback.php?status=" . pay2account($result['attach'], $result['total_fee'], $result['out_trade_no'], 'Weixin') . '&msg=' . $result['attach'] . '@' . $result['total_fee']);
    } else {
        $ret = array('ret' => 0);
        //printf_info($result);exit;
    }
    echo json_encode($ret);
}
?>