<?php
if (!isset($status)) {
    $status = [
        'success' => false,
        'msg' => '未知错误, 渲染失败'
    ];
}

if (!isset($status['success'])) {
    $status['success'] = false;
}

$title = '';
if ($status['success']) {
    $title = '恭喜，支付成功！';
} else {
    $title = '支付失败！';
}
if (isset($status['title'])) {
    $title = $status['title'];
}


?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style>
        html{background:#eee}
        body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}
        h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",,sans-serif;margin:30px 0 0 0;padding:0 0 7px}
        #page{margin-top:50px}
        h3{text-align:center}
        #page p{text-align: center;font-size:16px;line-height:1.5;margin:25px 0 20px}
        #page code{font-family:Consolas,Monaco,monospace}
        ul li{margin-bottom:10px;font-size:9px}
        a{color:#21759B;text-decoration:none;margin-top:-10px}
        a:hover{color:#D54E21}
    </style>
    <title>支付结果</title>
</head>
<body id="page">
<h3><?php echo $title ?></h3>
<div>
    <p class="status-msg"><?php echo $status['msg']; ?></p>
    <br/>
    <a style="float:right" href="javascript:window.close()">关闭</a>
    <br/>
</div>
</body>
</html>
