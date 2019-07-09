<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>支付结果</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/plugins/css/wx_qr.css" rel="stylesheet">
    <script type="text/javascript" src="//ossweb-img.qq.com/images/js/jquery/jquery-1.9.1.min.js"></script>
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="text">支付处理中</span>
    </h1>
    <div class="mod-ct">
        <div class="order"></div>
        <div class="amount">￥{{ sprintf('%0.2f',$amount/100) }}</div>
        <div class="tip">
            <span class="dec dec-left"></span>
            <span class="dec dec-right"></span>
            <div class="tip-text">
                <p>订单已支付, 正在处理...</p>
            </div>
        </div>
    </div>
    <div class="foot">
        <div class="inner">
            <p><?php echo SYS_NAME ?>, 有疑问请联系客服</p>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {
        var time = 4000, interval;

        function getData() {
            $.post('/api/qrcode/query/{!! $pay_id !!}', {
                    id: '{!! $id !!}',
                    t: Math.random()
                },
                function (r) {
                    clearInterval(interval);
                    window.location = r.data;
                }, 'json');
        }

        (function run() {
            interval = setInterval(getData, time);
        })();
    });
</script>
</body>
</html>