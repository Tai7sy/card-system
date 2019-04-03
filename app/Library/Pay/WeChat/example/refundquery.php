<?php
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-查退款单</title>
</head>
<?php  ini_set('date.timezone', 'Asia/Shanghai'); error_reporting(E_ERROR); require_once '../lib/WxPay.Api.php'; require_once 'log.php'; $spe4c4f6 = new CLogFileHandler('../logs/' . date('Y-m-d') . '.log'); $sp63838a = Log::Init($spe4c4f6, 15); function printf_info($spbda5b4) { foreach ($spbda5b4 as $spc37b44 => $sp12919e) { echo "<font color='#f00;'>{$spc37b44}</font> : {$sp12919e} <br/>"; } } if (isset($_REQUEST['transaction_id']) && $_REQUEST['transaction_id'] != '') { $sp37dae6 = $_REQUEST['transaction_id']; $sp317e70 = new WxPayRefundQuery(); $sp317e70->SetTransaction_id($sp37dae6); printf_info(WxPayApi::refundQuery($sp317e70)); } if (isset($_REQUEST['out_trade_no']) && $_REQUEST['out_trade_no'] != '') { $sp30c318 = $_REQUEST['out_trade_no']; $sp317e70 = new WxPayRefundQuery(); $sp317e70->SetOut_trade_no($sp30c318); printf_info(WxPayApi::refundQuery($sp317e70)); die; } if (isset($_REQUEST['out_refund_no']) && $_REQUEST['out_refund_no'] != '') { $spefabf0 = $_REQUEST['out_refund_no']; $sp317e70 = new WxPayRefundQuery(); $sp317e70->SetOut_refund_no($spefabf0); printf_info(WxPayApi::refundQuery($sp317e70)); die; } if (isset($_REQUEST['refund_id']) && $_REQUEST['refund_id'] != '') { $sp4b26f3 = $_REQUEST['refund_id']; $sp317e70 = new WxPayRefundQuery(); $sp317e70->SetRefund_id($sp4b26f3); printf_info(WxPayApi::refundQuery($sp317e70)); die; } ?>
<body>  
	<form action="#" method="post">
        <div style="margin-left:2%;color:#f00">微信订单号、商户订单号、微信订单号、微信退款单号选填至少一个，微信退款单号优先：</div><br/>
        <div style="margin-left:2%;">微信订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="transaction_id" /><br /><br />
        <div style="margin-left:2%;">商户订单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="out_trade_no" /><br /><br />
        <div style="margin-left:2%;">商户退款单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="out_refund_no" /><br /><br />
        <div style="margin-left:2%;">微信退款单号：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="refund_id" /><br /><br />
		<div align="center">
			<input type="submit" value="查询" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" />
		</div>
	</form>
</body>
</html><?php 