<?php
require_once('api.php');

$api = new fakala();

$no = $_POST['out_trade_no'];
$total_fee = (int)$_POST['total_fee'];
$attach = $_POST['attach'];


if ($api->notify_verify()) //签名正确
{
    // 此处作逻辑处理
    // 订单号 $no, 支付金额 $total_fee, 附加信息 $attach
}
exit; // 请不要进行任何输出, 直接退出即可
