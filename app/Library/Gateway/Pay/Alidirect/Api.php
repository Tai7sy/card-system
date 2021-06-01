<?php
namespace Gateway\Pay\Alidirect;
use Gateway\Pay\ApiInterface;
use Illuminate\Support\Facades\Log;
class Api implements ApiInterface {
	private $url_notify = '';
	private $url_return = '';
	public function __construct($spe8e527) {
		$this->url_notify = SYS_URL_API . '/pay/notify/' . $spe8e527;
		$this->url_return = SYS_URL . '/pay/return/' . $spe8e527;
	}
	function goPay($sp0619ae, $sp37aa0b, $sp4748a3, $spd21014, $spe53651) {
/* By Mr.Point QQ:40386277 www.zfbjk.com */
		$pid = isset($sp0619ae['pid'])?$sp0619ae['pid']:"";
		$result = \App\Order::where('order_no', $sp37aa0b)->first();
		$payAmount = sprintf('%.2f', $spe53651 / 100);
		$title = $result->id;
		switch ($sp0619ae['payway']) {
			case 'alipay': $paytype = 'alipay';
			break;
			case 'weixin': $paytype = 'weixin';
			break;
			default: throw new \Exception('支付方式填写错误, alipay/weixin');
		}
		header("Location: ../../qrcode/pay/{$sp37aa0b}/alidirect_{$paytype}?url={$pid}&title={$title}");
		exit;
	}
	function verify($sp0619ae, $sped5468) {
		$spdebef0 = isset($sp0619ae['isNotify'])?$sp0619ae['isNotify']:"";
		$spd123c4 = isset($sp0619ae['id'])?$sp0619ae['id']:"";
		$spf44c2d = isset($sp0619ae['key'])?$sp0619ae['key']:"";
		$tradeNo = isset($_POST['tradeNo'])?$_POST['tradeNo']:'';
		$Money = isset($_POST['Money'])?$_POST['Money']:0;
		$title = isset($_POST['title'])?$_POST['title']:'';
		$memo = isset($_POST['memo'])?$_POST['memo']:'';
		$alipay_account = isset($_POST['alipay_account'])?$_POST['alipay_account']:'';
		$Gateway = isset($_POST['Gateway'])?$_POST['Gateway']:'';
		$Sign = isset($_POST['Sign'])?$_POST['Sign']:'';
		$orderid = isset($_POST['orderid'])?$_POST['orderid']:'';
		if($orderid&&is_numeric($orderid))
		{
			$result = \App\Order::where('id', $orderid)->first();
			if($result&&$result->status==2)
			{
				exit("success");
			}
			exit;
		}
		if(strtoupper(md5($spd123c4 . $spf44c2d . $tradeNo . $Money .  iconv("utf-8","gb2312",$title) .  iconv("utf-8","gb2312",$memo))) != strtoupper($Sign)) {
			exit("Fail");
		} else {
			if(!is_numeric($title))
			{
				exit("FAIL");
			}
			$result = \App\Order::where('id', $title)->first();
			if(!$result)
			{
				exit("IncorrectOrder");
			}
			elseif($result->paid!=$Money*100)
			{
				exit("fail");
			}
			$sp37aa0b = $result->order_no;
			$sp1ca2f3 = (int) round($Money * 100);
			$sp3959e4 = $tradeNo;
			$sped5468($sp37aa0b, $sp1ca2f3, $sp3959e4);
			if ($spdebef0) {
				echo 'success';
			}
			return true;
		}
	}
	function refund($sp0619ae, $sp71c458, $sp537fc6, $spe53651) {
		return '此支付渠道不支持发起退款, 请手动操作';
	}
}