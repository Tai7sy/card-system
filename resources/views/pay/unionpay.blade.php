<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>银联支付</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/plugins/css/unionpay_qr.css" rel="stylesheet">
    <script type="text/javascript" src="//ossweb-img.qq.com/images/js/jquery/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/plugins/js/qrcode.min.js"></script>
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico-wechat"></span><span class="text">银联支付</span>
    </h1>
    <div class="mod-ct">
        <div class="order"></div>
        <div class="amount">￥{{ sprintf('%0.2f',$amount/100) }}</div>
        <div class="qr-image" id="qrcode"></div>
        <div id="open-app-container">
            <span style="display: block;margin-top: 24px">请截屏此界面或保存二维码，打开银联APP扫码，选择相册图片</span>
        </div>
        <div class="detail" id="orderDetail">
            <dl class="detail-ct" style="display: none;">
                <dt>商品</dt>
                <dd id="storeName">{{ $name }}</dd>
                <!--dt>说明</dt>
                <dd id="productName">用户充值</dd-->
                <dt>订单号</dt>
                <dd id="billId">{{ $id }}</dd>
                <dt>时间</dt>
                <dd id="createTime"><?php echo date('Y-m-d H:i:s')?></dd>
            </dl>
            <a href="javascript:void(0)" class="arrow"><i class="ico-arrow"></i></a>
        </div>
        <div class="tip">
            <span class="dec dec-left"></span>
            <span class="dec dec-right"></span>
            <div class="ico-scan"></div>
            <div class="tip-text">
                <p>使用银闪付/各大银行APP</p>
                <p>扫描二维码完成支付</p>
            </div>
        </div>
        <div class="tip-text">
        </div>
    </div>
    <div class="foot">
        <div class="inner">
            <p><?php echo SYS_NAME ?>, 有疑问请联系客服</p>
        </div>
    </div>
</div>

<script>
    var code_url = decodeURIComponent('{!! urlencode($qrcode) !!}');
    var qrcode = new QRCode("qrcode", {
        text: code_url,
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
        title: '请使用银闪付/各大银行APP扫描二维码'
    });

    // 订单详情
    var orderDetail = $('#orderDetail');
    orderDetail.find('.arrow').click(function (event) {
        if (orderDetail.hasClass('detail-open')) {
            orderDetail.find('.detail-ct').slideUp(500, function () {
                orderDetail.removeClass('detail-open');
            });
        } else {
            orderDetail.find('.detail-ct').slideDown(500, function () {
                orderDetail.addClass('detail-open');
            });
        }
    });

    $(document).ready(function () {
        var time = 4000, interval;

        function getData() {
            $.post('/api/qrcode/query/{!! $pay_id !!}', {
                    id: '{!! $id !!}',
                    t: Math.random()
                },
                function (r) {
                    clearInterval(interval);
                    $('.qr-image').remove();
                    $('.tip').html('<p style="font-size:24px">已支付，正在处理...</p>');
                    window.location = r.data;
                }, 'json');
        }

        (function run() {
            interval = setInterval(getData, time);
        })();
    });


    if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|SymbianOS)/i) !== null) {
        // call app
    } else {
        $('#open-app-container').hide();
    }
</script>
</body>
</html>