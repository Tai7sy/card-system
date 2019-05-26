<?php
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-查退款单</title>
</head>
<?php  ini_set('date.timezone', 'Asia/Shanghai'); error_reporting(E_ERROR); require_once '../lib/WxPay.Api.php'; require_once '../WxLog.php'; function printf_info($sp69c4ce) { foreach ($sp69c4ce as $sp1e4b49 => $sp39a65f) { echo "<font color='#f00;'>{$sp1e4b49}</font> : {$sp39a65f} <br/>"; } } if (isset($_REQUEST['transaction_id']) && $_REQUEST['transaction_id'] != '') { $sp5ebe4f = $_REQUEST['transaction_id']; $sp5bf110 = new WxPayRefundQuery(); $sp5bf110->SetTransaction_id($sp5ebe4f); printf_info(WxPayApi::refundQuery($sp5bf110)); } if (isset($_REQUEST['out_trade_no']) && $_REQUEST['out_trade_no'] != '') { $sp4510be = $_REQUEST['out_trade_no']; $sp5bf110 = new WxPayRefundQuery(); $sp5bf110->SetOut_trade_no($sp4510be); printf_info(WxPayApi::refundQuery($sp5bf110)); die; } if (isset($_REQUEST['out_refund_no']) && $_REQUEST['out_refund_no'] != '') { $spdd30ae = $_REQUEST['out_refund_no']; $sp5bf110 = new WxPayRefundQuery(); $sp5bf110->SetOut_refund_no($spdd30ae); printf_info(WxPayApi::refundQuery($sp5bf110)); die; } if (isset($_REQUEST['refund_id']) && $_REQUEST['refund_id'] != '') { $sp176d90 = $_REQUEST['refund_id']; $sp5bf110 = new WxPayRefundQuery(); $sp5bf110->SetRefund_id($sp176d90); printf_info(WxPayApi::refundQuery($sp5bf110)); die; } ?>
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