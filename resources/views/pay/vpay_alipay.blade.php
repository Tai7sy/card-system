<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
    <title>支付宝扫码</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <link type="text/css" href="/plugins/css/ali_qr.css?v=20200212" rel="stylesheet">
    <script type="text/javascript" src="//ossweb-img.qq.com/images/js/jquery/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/plugins/js/qrcode.min.js"></script>
    <script type="text/javascript" src="/plugins/js/steal_alipay.js?v=1.11"></script>
    <style type="text/css">
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            padding-top: 100px;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: #000;
            background-color: rgba(0, 0, 0, 0.4)
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 320px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold
        }

        .close:hover, .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer
        }
    </style>
</head>
<body>
<div class="body">
    <h1 class="mod-title">
        <span class="ico-wechat"></span><span class="text">支付宝扫码</span>
    </h1>
    <div class="mod-ct">
        <div class="order"></div>
        <div><span style="color:red;font-size: 18px;font-weight:bold;display: block;margin-top: 24px">请输入准确的付款金额（精确到分）</span></div>
        <div class="amount">￥{{ $_GET["real_price"] }}</div>
        <div class="qr-image" id="qrcode"></div>

        <div id="open-app-container">
            <span style="display: block;margin-top: 24px" id="open-app-tip">请截屏此界面或保存二维码，打开支付宝扫码，选择相册图片</span>
            <a style="padding:6px 34px;border:1px solid #e5e5e5;display: inline-block;margin-top: 8px" id="open-app">点击打开支付宝</a>
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
                <p>请使用支付宝扫一扫</p>
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
    <!-- Modal Dialog -->
    <div id="myModal" class="modal">
        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" id="dialog-close">&times;</span>
            <img src="https://s2.ax1x.com/2019/06/20/Vj4sWq.jpg" style="max-width: 100%"/>
        </div>

    </div>
</div>

<script>
    var code_url = decodeURIComponent('{!! urlencode($qrcode) !!}');

    new QRCode("qrcode", {
        text: code_url,
        width: 230,
        height: 230,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H,
        title: '请使用支付宝扫一扫'
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
    // call app
    if (navigator.userAgent.match(/(iPhone|iPod|Android|ios|SymbianOS)/i) !== null) {
        var app_package = 'com.eg.android.AlipayGphone';
        var app_url = 'alipays://platformapi/startapp?saId=10000007&clientVersion=3.7.0.0718&qrcode=' + encodeURIComponent(code_url);
        $('#open-app').on('click', function () {
            goPage(app_url, app_package);
        });

        // 需要展示准确的付款金额, 所以不自动跳转
        // setTimeout(function () {
        //     // 好像有点问题, 加了限制 2019年4月3日 13:42:34
        //     // 重新开启吧, 限制没了 2019年06月24日20:05:05
        //     goPage(app_url, app_package);
        // }, 100);
    } else {
        $('#open-app-container').hide();
    }
</script>
</body>
</html>