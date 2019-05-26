<?php
ini_set('date.timezone', 'Asia/Shanghai'); require_once '../lib/WxPay.Api.php'; require_once 'WxPay.NativePay.php'; require_once '../WxLog.php'; $sp3f3238 = new NativePay(); $sp80ba2f = $sp3f3238->GetPrePayUrl('123456789'); $sp5bf110 = new WxPayUnifiedOrder(); $sp5bf110->SetBody('test'); $sp5bf110->SetAttach('test'); $sp5bf110->SetOut_trade_no(WxPayConfig::MCHID . date('YmdHis')); $sp5bf110->SetTotal_fee('1'); $sp5bf110->SetTime_start(date('YmdHis')); $sp5bf110->SetTime_expire(date('YmdHis', time() + 600)); $sp5bf110->SetGoods_tag('test'); $sp5bf110->SetNotify_url('http://paysdk.weixin.qq.com/example/notify.php'); $sp5bf110->SetTrade_type('NATIVE'); $sp5bf110->SetProduct_id('123456789'); $sp820aff = $sp3f3238->GetPayUrl($sp5bf110); $sp808a2c = $sp820aff['code_url']; ?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<body>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div><br/>
	<img alt="模式一扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php  echo urlencode($sp80ba2f); ?>
" style="width:150px;height:150px;"/>
	<br/><br/><br/>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
	<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php  echo urlencode($sp808a2c); ?>
" style="width:150px;height:150px;"/>
	
</body>
</html><?php 