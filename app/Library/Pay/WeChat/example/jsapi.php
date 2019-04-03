<?php
ini_set('date.timezone', 'Asia/Shanghai'); require_once '../lib/WxPay.Api.php'; require_once 'WxPay.JsApiPay.php'; require_once 'log.php'; $spe4c4f6 = new CLogFileHandler('../logs/' . date('Y-m-d') . '.log'); $sp63838a = Log::Init($spe4c4f6, 15); function printf_info($spbda5b4) { foreach ($spbda5b4 as $spc37b44 => $sp12919e) { echo "<font color='#00ff55;'>{$spc37b44}</font> : {$sp12919e} <br/>"; } } $spdb16f7 = new JsApiPay(); $sp576cde = $spdb16f7->GetOpenid(); $sp317e70 = new WxPayUnifiedOrder(); $sp317e70->SetBody('test'); $sp317e70->SetAttach('test'); $sp317e70->SetOut_trade_no(WxPayConfig::MCHID . date('YmdHis')); $sp317e70->SetTotal_fee('1'); $sp317e70->SetTime_start(date('YmdHis')); $sp317e70->SetTime_expire(date('YmdHis', time() + 600)); $sp317e70->SetGoods_tag('test'); $sp317e70->SetNotify_url('http://paysdk.weixin.qq.com/example/notify.php'); $sp317e70->SetTrade_type('JSAPI'); $sp317e70->SetOpenid($sp576cde); $sp09599a = WxPayApi::unifiedOrder($sp317e70); echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>'; printf_info($sp09599a); $spd91e5f = $spdb16f7->GetJsApiParameters($sp09599a); $sp555091 = $spdb16f7->GetEditAddressParameters(); ?>

<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/> 
    <title>微信支付样例-支付</title>
    <script type="text/javascript">
	//调用微信JS api 支付
	function jsApiCall()
	{
		WeixinJSBridge.invoke(
			'getBrandWCPayRequest',
			<?php  echo $spd91e5f; ?>
,
			function(res){
				WeixinJSBridge.log(res.err_msg);
				alert(res.err_code+res.err_desc+res.err_msg);
			}
		);
	}

	function callpay()
	{
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	}
	</script>
	<script type="text/javascript">
	//获取共享地址
	function editAddress()
	{
		WeixinJSBridge.invoke(
			'editAddress',
			<?php  echo $sp555091; ?>
,
			function(res){
				var value1 = res.proviceFirstStageName;
				var value2 = res.addressCitySecondStageName;
				var value3 = res.addressCountiesThirdStageName;
				var value4 = res.addressDetailInfo;
				var tel = res.telNumber;
				
				alert(value1 + value2 + value3 + value4 + ":" + tel);
			}
		);
	}
	
	window.onload = function(){
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', editAddress, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', editAddress); 
		        document.attachEvent('onWeixinJSBridgeReady', editAddress);
		    }
		}else{
			editAddress();
		}
	};
	
	</script>
</head>
<body>
    <br/>
    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>
	<div align="center">
		<button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()" >立即支付</button>
	</div>
</body>
</html><?php 