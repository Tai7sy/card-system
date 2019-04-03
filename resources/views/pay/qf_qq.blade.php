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
    <script type="text/javascript" src="//pub.idqqimg.com/qqmobile/qqapi.js?_bid=152"></script>
    <script type="text/javascript" src="/plugins/js/qrcode.min.js"></script>
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico-wechat"></span><span class="text">QQ钱包支付</span>
    </h1>
    <div class="mod-ct" id="pay_body">
        <div class="order"></div>
        <!--div class="amount">￥0.01</div-->
        <div class="qr-image" id="qrcode"></div>

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
    <div class="mod-ct" id="jump_to_pay" style="display: none">
        <div class="tip">
            <div class="tip-text">
                <p>若没有弹出支付窗口,&nbsp;您可以</p>
            </div>
            <div class="detail" style="margin-top: 12px; padding-top: 12px">
                <a href="javascript:jumpToPay()" class="arrow">点击重试</a>
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
        <?php
        if (!isset($qrcode)) {
            die('alert("qrcode error")</script>');
        }
        ?>


    var jumpToPay = function () {
            $('#pay_body').show();
            $('#jump_to_pay').hide();
        };
    if (/ QQ\//i.test(navigator.userAgent) && mqq !== undefined) {
        jumpToPay = function () {
            $('#pay_body').hide();
            $('#jump_to_pay').show();
            var pay_params = decodeURIComponent('{!! urlencode($qrcode) !!}');
            // 一个超级无敌尴尬的事情: pay_params是有了, 但是mqq.tenpay.pay()需要从腾讯认证的域名调用
            // 所以这个支付方式宣告破产
            mqq.tenpay.pay(pay_params, function (e) {
                e.resultCode = parseInt(e.resultCode);
                0 === e.resultCode ? alert('支付成功') :
                    e.resultCode === -11001 || e.resultCode === -1 || alert("唤起QQ钱包失败，请稍后再试"),alert(e)
            });
        }
    }

    var code_url = location.href;
    var qrcode = new QRCode("qrcode", {
        text: code_url,
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        title:'test',
        correctLevel: QRCode.CorrectLevel.H
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
        if (jumpToPay) jumpToPay();

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