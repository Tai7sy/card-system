<?php
ini_set('date.timezone', 'Asia/Shanghai'); require_once '../lib/WxPay.Api.php'; require_once 'WxPay.NativePay.php'; require_once 'log.php'; $sp92a4d6 = new NativePay(); $spb90bd2 = $sp92a4d6->GetPrePayUrl('123456789'); $sp317e70 = new WxPayUnifiedOrder(); $sp317e70->SetBody('test'); $sp317e70->SetAttach('test'); $sp317e70->SetOut_trade_no(WxPayConfig::MCHID . date('YmdHis')); $sp317e70->SetTotal_fee('1'); $sp317e70->SetTime_start(date('YmdHis')); $sp317e70->SetTime_expire(date('YmdHis', time() + 600)); $sp317e70->SetGoods_tag('test'); $sp317e70->SetNotify_url('http://paysdk.weixin.qq.com/example/notify.php'); $sp317e70->SetTrade_type('NATIVE'); $sp317e70->SetProduct_id('123456789'); $spe62cd8 = $sp92a4d6->GetPayUrl($sp317e70); $sp42be8e = $spe62cd8['code_url']; ?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" /> 
    <title>微信支付样例-退款</title>
</head>
<body>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div><br/>
	<img alt="模式一扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php  echo urlencode($spb90bd2); ?>
" style="width:150px;height:150px;"/>
	<br/><br/><br/>
	<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div><br/>
	<img alt="模式二扫码支付" src="http://paysdk.weixin.qq.com/example/qrcode.php?data=<?php  echo urlencode($sp42be8e); ?>
" style="width:150px;height:150px;"/>
	
</body>
</html><?php 