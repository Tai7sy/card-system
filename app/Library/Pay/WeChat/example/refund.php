<?php
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<?php  ini_set('date.timezone', 'Asia/Shanghai'); error_reporting(E_ERROR); require_once '../lib/WxPay.Api.php'; require_once 'log.php'; $spe4c4f6 = new CLogFileHandler('../logs/' . date('Y-m-d') . '.log'); $sp63838a = Log::Init($spe4c4f6, 15); function printf_info($spbda5b4) { foreach ($spbda5b4 as $spc37b44 => $sp12919e) { echo "<font color='#f00;'>{$spc37b44}</font> : {$sp12919e} <br/>"; } } if (isset($_REQUEST['transaction_id']) && $_REQUEST['transaction_id'] != '') { $sp37dae6 = $_REQUEST['transaction_id']; $sp51db0b = $_REQUEST['total_fee']; $spdac5bf = $_REQUEST['refund_fee']; $sp317e70 = new WxPayRefund(); $sp317e70->SetTransaction_id($sp37dae6); $sp317e70->SetTotal_fee($sp51db0b); $sp317e70->SetRefund_fee($spdac5bf); $sp317e70->SetOut_refund_no(WxPayConfig::MCHID . date('YmdHis')); $sp317e70->SetOp_user_id(WxPayConfig::MCHID); printf_info(WxPayApi::refund($sp317e70)); die; } if (isset($_REQUEST['out_trade_no']) && $_REQUEST['out_trade_no'] != '') { $sp30c318 = $_REQUEST['out_trade_no']; $sp51db0b = $_REQUEST['total_fee']; $spdac5bf = $_REQUEST['refund_fee']; $sp317e70 = new WxPayRefund(); $sp317e70->SetOut_trade_no($sp30c318); $sp317e70->SetTotal_fee($sp51db0b); $sp317e70->SetRefund_fee($spdac5bf); $sp317e70->SetOut_refund_no(WxPayConfig::MCHID . date('YmdHis')); $sp317e70->SetOp_user_id(WxPayConfig::MCHID); printf_info(WxPayApi::refund($sp317e70)); die; } ?>
<body>  
	<form action="#" method="post">
        <div style="margin-left:2%;color:#f00">微信订单号和商户订单号选少填一个，微信订单号优先：</div><br/>
        <div style="margin-left:2%;">微信订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="transaction_id" /><br /><br />
        <div style="margin-left:2%;">商户订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="out_trade_no" /><br /><br />
        <div style="margin-left:2%;">订单总金额(分)：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="total_fee" /><br /><br />
        <div style="margin-left:2%;">退款金额(分)：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="refund_fee" /><br /><br />
		<div align="center">
			<input type="submit" value="提交退款" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" />
		</div>
	</form>
</body>
</html><?php 