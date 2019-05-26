<?php
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-查退款单</title>
</head>
<?php  require_once '../lib/WxPay.Api.php'; require_once 'WxPay.MicroPay.php'; require_once '../WxLog.php'; function printf_info($sp69c4ce) { foreach ($sp69c4ce as $sp1e4b49 => $sp39a65f) { echo "<font color='#00ff55;'>{$sp1e4b49}</font> : {$sp39a65f} <br/>"; } } if (isset($_REQUEST['auth_code']) && $_REQUEST['auth_code'] != '') { $sp33286c = $_REQUEST['auth_code']; $sp5bf110 = new WxPayMicroPay(); $sp5bf110->SetAuth_code($sp33286c); $sp5bf110->SetBody('刷卡测试样例-支付'); $sp5bf110->SetTotal_fee('1'); $sp5bf110->SetOut_trade_no(WxPayConfig::MCHID . date('YmdHis')); $spc85715 = new MicroPay(); printf_info($spc85715->pay($sp5bf110)); } ?>
<body>  
	<form action="#" method="post">
        <div style="margin-left:2%;">商品描述：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" readonly value="刷卡测试样例-支付" name="auth_code" /><br /><br />
        <div style="margin-left:2%;">支付金额：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" readonly value="1分" name="auth_code" /><br /><br />
        <div style="margin-left:2%;">授权码：</div><br/>
        <input type="text" style="width:96%;height:35px;margin-left:2%;" name="auth_code" /><br /><br />
       	<div align="center">
			<input type="submit" value="提交刷卡" style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" />
		</div>
	</form>
</body>
</html><?php 