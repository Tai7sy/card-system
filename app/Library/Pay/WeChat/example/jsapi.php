<?php
ini_set('date.timezone', 'Asia/Shanghai'); require_once '../lib/WxPay.Api.php'; require_once 'WxPay.JsApiPay.php'; require_once '../WxLog.php'; function printf_info($sp69c4ce) { foreach ($sp69c4ce as $sp1e4b49 => $sp39a65f) { echo "<font color='#00ff55;'>{$sp1e4b49}</font> : {$sp39a65f} <br/>"; } } $spc24b62 = new JsApiPay(); $sp23551f = $spc24b62->GetOpenid(); $sp5bf110 = new WxPayUnifiedOrder(); $sp5bf110->SetBody('test'); $sp5bf110->SetAttach('test'); $sp5bf110->SetOut_trade_no(WxPayConfig::MCHID . date('YmdHis')); $sp5bf110->SetTotal_fee('1'); $sp5bf110->SetTime_start(date('YmdHis')); $sp5bf110->SetTime_expire(date('YmdHis', time() + 600)); $sp5bf110->SetGoods_tag('test'); $sp5bf110->SetNotify_url('http://paysdk.weixin.qq.com/example/notify.php'); $sp5bf110->SetTrade_type('JSAPI'); $sp5bf110->SetOpenid($sp23551f); $sp61541f = WxPayApi::unifiedOrder($sp5bf110); echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>'; printf_info($sp61541f); $sp76137e = $spc24b62->GetJsApiParameters($sp61541f); $sp7ac487 = $spc24b62->GetEditAddressParameters(); ?>

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
			<?php  echo $sp76137e; ?>
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
			<?php  echo $sp7ac487; ?>
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