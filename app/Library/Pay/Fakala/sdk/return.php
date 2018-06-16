<!DOCTYPE HTML>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>支付结果</title>
    <?php
    require_once('api.php');

    $api = new fakala();


    $no = $_GET['out_trade_no'];
    $total_fee = (int)$_GET['total_fee'];
    $attach = $_GET['attach'];


    if ($api->return_verify())//签名正确
    {
        echo '<h1>支付成功，订单号：' . $no . '，金额：￥' . sprintf('%.2f', $total_fee) . '</h1>';

    } else {
        echo '<h1>验证失败</h1>';
    }

    ?>
</head>
<body>
</body>
</html>