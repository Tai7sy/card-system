<?php
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-查退款单</title>
</head>
<?php  require_once '../lib/WxPay.Api.php'; require_once 'WxPay.MicroPay.php'; require_once 'log.php'; $spe4c4f6 = new CLogFileHandler('../logs/' . date('Y-m-d') . '.log'); $sp63838a = Log::Init($spe4c4f6, 15); function printf_info($spbda5b4) { foreach ($spbda5b4 as $spc37b44 => $sp12919e) { echo "<font color='#00ff55;'>{$spc37b44}</font> : {$sp12919e} <br/>"; } } if (isset($_REQUEST['auth_code']) && $_REQUEST['auth_code'] != '') { $spd4bcc5 = $_REQUEST['auth_code']; $sp317e70 = new WxPayMicroPay(); $sp317e70->SetAuth_code($spd4bcc5); $sp317e70->SetBody('刷卡测试样例-支付'); $sp317e70->SetTotal_fee('1'); $sp317e70->SetOut_trade_no(WxPayConfig::MCHID . date('YmdHis')); $sp5ba7ee = new MicroPay(); printf_info($sp5ba7ee->pay($sp317e70)); } ?>
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