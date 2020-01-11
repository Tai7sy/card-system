<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $name }}</title>
    <meta name=description content="{{ $description }}">
    <meta name=keywords content="{{ $keywords }}">
    <script src="/shop_theme/classic/jquery-1.8.3.min.js"></script>
    <link href="/shop_theme/classic/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <script src="/shop_theme/classic/sweetalert2/sweetalert2.min.js"></script>
    <link href="/shop_theme/classic/iconfont.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="/plugins/css/quill.snow.css">
    <script type="text/javascript" src="/plugins/js/quill.min.js"></script>
    @if(@$config['captcha']['scene']['shop']['buy'] && $config['captcha']['driver'] === 'geetest')
        <script type="text/javascript" src="/plugins/js/gt.js"></script>
    @endif
    <link href="/shop_theme/classic/mobile.min.css?v={!! $version !!}" rel="stylesheet" type="text/css">
    <script src="/shop_theme/classic/tips.js"></script>
</head>
<body>
<!--顶部-->
<div class="top_bg">
    <div class="top">
        <div class="logo">
            <a href="/">{{ $config['shop']['name'] }}</a>
        </div>
        <div class="seller_name"><i class="iconfont icon-tehuishanghu" onclick="showShopInfo()"></i></div>
    </div>
    <div class="top_menu">
        <ul>
            @if(config('app.project') === 'card')
                <li onclick="window.open('/s#/report', '_blank')">
                    <i class="iconfont icon-zixun"></i>
                    <br><span>订单投诉</span>
                </li>
            @endif
            <li onclick="showAnn()">
                <i class="iconfont icon-dingdan"></i>
                <br><span>商家公告</span>
            </li>
            <li onclick="window.open('/s#/record')">
                <i class="iconfont icon-sousuo2"></i>
                <br><span>订单查询</span>
            </li>
        </ul>
    </div>
    <div class="clear"></div>
</div>
<div class="seller_form">
    <ul>
        @if(@$config['theme']['list_type'] === 'button')
            <li>
                <label for="categories" class="form_title">商品分类</label>
                <div id="categories" class="btn-container">
                </div>
            </li>
            <li>
                <label for="products" class="form_title">商品名称</label>
                <div id="products" class="btn-container">
                </div>
            </li>
        @else
            <li>
                <label for="categories" class="form_title">商品分类</label>
                <select id="categories" title="商品分类">
                    <option value="-1">请选择分类</option>
                </select>
            </li>
            <li>
                <label for="products" class="form_title">商品名称</label>
                <select id="products" title="商品名称">
                    <option value="-1">请选择商品</option>
                </select>
            </li>
        @endif
        <li>
            <label for="description" class="form_title">商品介绍：</label>
            <div class="seller_sm ql-snow">
                <div id="description" class="ql-editor quill-html" style="display: none"></div>
            </div>
        </li>
        <li>
            <div class="box_inline">
                <label for="price" class="form_title">商品单价</label>
                <p class="spdj">
                    <span class="big_text" id="price">-</span><span>元</span>
                    <a id="discount-btn" class="pf_btn" onclick="showPfyh()" style="display:none">（批发优惠）</a>
                </p>
            </div>
            <div class="box_inline" style="float:right;margin-right:0">
                <span class="form_title"><label for="quantity">购买数量</label>
                    <span style="margin-left:5px;font-weight: lighter;color: #fe825a">( <span id="invent">库存: -</span> )</span>
                </span>
                <input type="number" id="quantity" value="1" placeholder="请输入您需要购买的数量，必须为整数">
            </div>
        </li>
        <li id="contact-box">
            <span class="form_title"><label for="contact">联系方式</label>
                <span style="margin-left:5px;font-weight: lighter;color: #fe825a">（订单查询重要凭证）</span>
            </span>
            <input class="phone_num" type="text" id="contact" value="" placeholder="填写必须为真实QQ或邮箱或手机号">
        </li>
        <!--li id="pwdTackBox" style="display:none">
            <span class="form_title">取卡密码
                <span style="margin-left:5px;font-weight: lighter;color: #fe825a">（查询卡密时需填写，请牢记！）</span>
            </span>
            <input type="text" name="pwdTack" value="" placeholder="请输入取卡密码（6-20位）">
        </li-->
        <li id="coupon-box" style="display:none">
            <span class="form_title"><label for="coupon">优惠券</label>
                <span style="margin-left:5px;font-weight: lighter;color: #fe825a">(<span id="coupon-tip"></span>)</span>
            </span>
            <input id="coupon" type="text" name="coupon" placeholder="请填写你的优惠券，没有可不填" autocomplete="off">
        </li>
        <li>
            <span class="form_title" style="display: inline-block; margin-right: 4px">附加服务</span>
            @if(in_array('sms_send_order', $config['functions']))
                <label><input type="checkbox" name="send-sms" id="send-sms" onclick="$('#sms_to_container').toggle(this.checked);calcTotalPrice();">短信提醒（￥{{ $config['sms_send_order']['sms_price']/100 }}）</label>
            @endif
            @if(in_array('mail_send_order', $config['functions']))
                <label><input type="checkbox" name="send-mail" id="send-mail" onclick="$('#mail_to_container').toggle(this.checked);calcTotalPrice();">邮件提醒</label>
            @endif
        </li>
        <li id="sms_to_container" style="display:none">
            <span class="form_title"><label for="sms_to">接收订单手机号</label></span>
            <input id="sms_to" type="text" name="mobile" placeholder="请输入手机号，用于接收订单短信" autocomplete="off">
        </li>
        <li id="mail_to_container" style="display:none">
            <span class="form_title"><label for="mail_to">接收订单邮箱</label></span>
            <input id="mail_to" type="text" name="email" placeholder="请输入邮箱，用于接收订单邮件" autocomplete="off">
        </li>

    </ul>
</div>
<div class="payments">
    <span class="form_title" style="margin-bottom: 4px">请选择支付方式</span>
    <ul>
        @foreach ($config['pays'] as $pay)
            <?php $img = '/shop_theme/classic/images/' . str_after($pay['img'], 'images/'); ?>
            <li class="{!! \App\Library\Helper::str_between_longest($pay['img'],'images/','.') !!}">
                <img src="{!! $img !!}" alt="{!! $pay['name'] !!}">{!! $pay['name'] !!}<span></span>
                <input type="radio" name="payway" value="{!! $pay['id'] !!}" title="{!! $pay['name'] !!}">
            </li>
        @endforeach
    </ul>
    <footer>商品问题请联系客服QQ:{{ strlen($config['shop']['qq']) ? $config['shop']['qq'] : '该商户没有填写QQ' }}</footer>
</div>
<div class="pay_bottom">
    <p>应付：<span class="pay_all" id="should-pay"> - </span> 元</p>
    <button type="button" id="order-btn">确认付款</button>
</div>
<!-- 提示其他浏览器打开遮罩 -->
<div id="browser_shadow" style="display:none;">
    <img src="/shop_theme/classic/images/open_other.png" alt="请使用其他浏览器打开">
</div>
<!-- 批发优惠信息 -->
<div id="discount-tip" style="display:none;">
</div>
<!--/ 批发优惠信息 -->
<!-- 店铺信息 -->
<div id="shop_html" style="display:none;">
    <p>店铺名称：{{ $config['shop']['name'] }}</p>
    <p>商品类型：数字卡密</p>
    <p>发货方式：自动发货</p>
    <p>卖家Q Q：<a target="_blank" href="mqqwpa://im/chat?chat_type=wpa&uin={{ $config['shop']['qq'] }}&version=1&src_type=web&web_src=" style="color:#449CF3">{{ $config['shop']['qq'] }}</a></p>
</div>
<!--/ 店铺信息 -->
<!-- 商家公告 -->
<div id="ann" style="display:none;">
    <div style="padding-bottom:5px;font-weight:bold;color:#449CF3"><span>卖家QQ：</span>
        <a target="_blank" href="mqqwpa://im/chat?chat_type=wpa&uin={{ $config['shop']['qq'] }}&version=1&src_type=web&web_src=">{{ $config['shop']['qq'] }}</a>
    </div>
    <div class="container ql-editor quill-html" style="max-height:500px;overflow: scroll"></div>
</div>
<!--/ 商家公告 -->

<script type=text/javascript>var config = @json($config);</script>
<script type=text/javascript src="/shop_theme/classic/common.min.js?v={!! $version !!}"></script>
<script type=text/javascript src="/shop_theme/classic/mobile.min.js?v={!! $version !!}"></script>
{!! $js_tj !!} {!! $js_kf !!}
</body>
</html>