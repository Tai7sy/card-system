<?php
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<?php  ini_set('date.timezone', 'Asia/Shanghai'); error_reporting(E_ERROR); require_once '../lib/WxPay.Api.php'; require_once '../WxLog.php'; function printf_info($sp69c4ce) { foreach ($sp69c4ce as $sp1e4b49 => $sp39a65f) { echo "<font color='#f00;'>{$sp1e4b49}</font> : {$sp39a65f} <br/>"; } } if (isset($_REQUEST['transaction_id']) && $_REQUEST['transaction_id'] != '') { $sp5ebe4f = $_REQUEST['transaction_id']; $sp6f1ff6 = $_REQUEST['total_fee']; $spe0b4ef = $_REQUEST['refund_fee']; $sp5bf110 = new WxPayRefund(); $sp5bf110->SetTransaction_id($sp5ebe4f); $sp5bf110->SetTotal_fee($sp6f1ff6); $sp5bf110->SetRefund_fee($spe0b4ef); $sp5bf110->SetOut_refund_no(WxPayConfig::MCHID . date('YmdHis')); $sp5bf110->SetOp_user_id(WxPayConfig::MCHID); printf_info(WxPayApi::refund($sp5bf110)); die; } if (isset($_REQUEST['out_trade_no']) && $_REQUEST['out_trade_no'] != '') { $sp4510be = $_REQUEST['out_trade_no']; $sp6f1ff6 = $_REQUEST['total_fee']; $spe0b4ef = $_REQUEST['refund_fee']; $sp5bf110 = new WxPayRefund(); $sp5bf110->SetOut_trade_no($sp4510be); $sp5bf110->SetTotal_fee($sp6f1ff6); $sp5bf110->SetRefund_fee($spe0b4ef); $sp5bf110->SetOut_refund_no(WxPayConfig::MCHID . date('YmdHis')); $sp5bf110->SetOp_user_id(WxPayConfig::MCHID); printf_info(WxPayApi::refund($sp5bf110)); die; } ?>
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