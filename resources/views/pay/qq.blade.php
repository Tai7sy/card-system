<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>QQ钱包支付</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/plugins/css/qq_qr.css" rel="stylesheet">
    <script type="text/javascript" src="//ossweb-img.qq.com/images/js/jquery/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/plugins/js/qrcode.min.js"></script>
    <script type="text/javascript" src="/plugins/js/steal_alipay.js?v=1.1"></script>
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico-wechat"></span><span class="text">QQ钱包支付</span>
    </h1>
    <div class="mod-ct">
        <div class="order"></div>
        <div class="amount">￥{{ sprintf('%0.2f',$amount/100) }}</div>
        <div class="qr-image" id="qrcode"></div>
        <div id="open-app-container">
            <span style="display: block;margin-top: 24px">请截屏此界面或保存二维码，打开手机QQ扫码，选择相册图片</span>
            <a style="padding:6px 34px;border:1px solid #e5e5e5;display: inline-block;margin-top: 8px" id="open-app">点击打开手机QQ</a>
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
                <p>请使用手机QQ扫一扫</p>
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
        title: '请使用手机QQ扫一扫'
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
                    window.location = r.data;
                }, 'json');
        }

        (function run() {
            interval = setInterval(getData, time);
        })();
    });

    if (navigator.userAgent.match(/MQQBrowser/i) !== null) {
        location.href = code_url;
    } else {
        // call app
        if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|SymbianOS)/i) !== null) {
            var app_package = 'com.tencent.mobileqq';
            var app_url = 'mqqapi://forward/url?version=1&src_type=web&url_prefix={!! base64_encode('http://ptlogin2.tencent.com/jump?u1='.urlencode($qrcode)) !!}';
            $('#open-app').on('click', function () {
                goPage(app_url, app_package);
            });
            setTimeout(function () {
                goPage(app_url, app_package);
            }, 100);
        } else {
            $('#open-app-container').hide();
        }
    }
</script>
</body>
</html>