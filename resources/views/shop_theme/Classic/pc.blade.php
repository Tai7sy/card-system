<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
    <title>{{ $name }}</title>
    <meta name=description content="{{ $description }}">
    <meta name=keywords content="{{ $keywords }}">
    <script src="/shop_theme/classic/jquery-1.8.3.min.js"></script>
    <link href="/shop_theme/classic/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <script src="/shop_theme/classic/sweetalert2/sweetalert2.min.js"></script>
    <link href="/shop_theme/classic/sweetalert2/sweetalert2.min.css" rel="stylesheet" type="text/css">
    <link href="/shop_theme/classic/pc.min.css?v={!! $version !!}" rel="stylesheet" type="text/css">
    <script src="/shop_theme/classic/layui/layer.js"></script>
    <link href="/plugins/css/quill.snow.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/plugins/js/quill.min.js"></script>
    @if(@$config['captcha']['scene']['shop']['buy'] && $config['captcha']['driver'] === 'geetest')
        <script type="text/javascript" src="/plugins/js/gt.js"></script>
    @endif
</head>
<body>
<section class="page_top">
    <div class="container">
        <!--导航-->
        <div class="top">
            <div class="logo"><a href="/"><img src="{{ config('app.logo') }}" alt="" height="64"></a></div>
            <div class="nav_btn"><i></i></div>
            <div class="user_btns">
                <a href="/s#/record" target="_blank" class="reg_btn" style="width: 180px">
                    <i class="iconfont icon-sousuo"></i>查询订单</a>
            </div>
            <div class="nav">
                <ul>
                    <li><a class="btn" target="_blank" href="/">首页</a></li>
                    @if(config('app.project') === 'card')
                        <li><a class="btn" target="_blank" href="/s#/report">投诉订单</a></li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</section>
<div class="nyroModal">
    <div class="order_form">
        <div class="left_card" style="min-height: 276px;margin-top: -20px;">
            <div class="dianpu"><i class="iconfont icon-dengpao"></i>{{ $config['shop']['name'] }}</div>
            <div class="small_card">数字卡密</div>
            <div class="small_card">自动发货</div>
            <div class="small_card">信誉卖家</div>
            <div class="clear">
            </div>
            <p>
                <b>卖家QQ </b><span>{{ $config['shop']['qq'] }}</span>
                <a href="//wpa.qq.com/msgrd?v=1&uin={{ $config['shop']['qq'] }}&site=fakaxitong.com&menu=yes" target="_blank" class="qq1_btn">
                    <i class="iconfont icon-qq-white"></i>咨询卖家</a>
                <br><span style="color:red;font-size:12px">商品问题联系卖家</span>
            </p>
            <p>
                <b>卖家公告 </b><span id="ann"><span class="container ql-editor quill-html"></span></span>
            </p>
        </div>
        <div class="right_form">
            <ul>
                @if(@$config['theme']['list_type'] === 'button')
                    <li class="btn-container-li">
                        <label for="categories" class="span_up">商品分类</label>
                        <div id="categories" class="btn-container">
                        </div>
                    </li>
                    <li class="btn-container-li">
                        <label for="products" class="span_up">商品名称</label>
                        <div id="products" class="btn-container">
                        </div>
                        <a class="spsm" onclick="showAnn()">[ 卖家公告 ]</a>
                        <span id="notice" style="display: none"></span>
                    </li>
                @else
                    <li>
                        <label for="categories" class="span_up">商品分类</label>
                        <select id="categories" title="商品分类">
                            <option value="-1">请选择分类</option>
                        </select>
                    </li>
                    <li>
                        <label for="products" class="span_up">商品名称</label>
                        <select id="products" title="商品名称">
                            <option value="-1">请选择商品</option>
                        </select>
                        <a class="spsm" onclick="showAnn()">[ 卖家公告 ]</a>
                        <span id="notice" style="display: none"></span>
                    </li>
                @endif
                <li>
                    <label class="span_up">商品单价</label>
                    <p class="big_txt"><b id="price"> - </b>元</p>
                    <a style="display:none;top: 10px;" id="discount-btn" class="spsm">[ 批发优惠 ]</a>
                    <div id="discount-tip" style="display: none;"></div>

                </li>
                <li>
                    <label for="quantity" class="span_up">购买数量</label>
                    <input type="number" id="quantity" value="1" title="购买数量">
                    <a class="spsm">[ <span id="invent" style="width: 110px"></span> ]</a>
                </li>
                <li id="contact-box">
                    <label for="contact" class="span_up">联系方式</label>
                    <input class="phone_num" id="contact" name="contact" type="text" placeholder="可以输入QQ、邮箱、手机号等等，用于查询订单">
                </li>
                <li id="coupon-box" style="display:none">
                    <label for="coupon" class="span_up">优惠券</label>
                    <input id="coupon" name="coupon" type="text" placeholder="[选填]请输入优惠券代码">
                    <a class="spsm">[ <span id="coupon-tip" style="width: 110px"></span> ]</a>
                </li>

                <!--li id="pwdforsearch1" style="display:none">
                    <span class="span_up">取卡密码</span>
                    <input type="text" name="pwdforsearch1" placeholder="[必填]请输入取卡密码（6-20位）">
                </li-->
                <li style="height: 44px;line-height: 44px;">
                    @if(in_array('sms_send_order', $config['functions']))
                        <label class="fz_lab" style="padding-right: 4px">短信提醒（￥{{ $config['sms_send_order']['sms_price']/100 }}）<input type="checkbox" name="send-sms" id="send-sms" onclick="$('#sms_to_container').toggle(this.checked);calcTotalPrice();">
                        </label>
                    @endif
                    @if(in_array('mail_send_order', $config['functions']))
                        <label class="fz_lab">邮箱提醒<input type="checkbox" name="send-mail" id="send-mail" onclick="$('#mail_to_container').toggle(this.checked);calcTotalPrice();">
                        </label>
                    @endif
                </li>
                <li id="sms_to_container" style="display: none">
                    <label for="sms_to" class="span_up">接收订单手机号</label>
                    <input type="text" id="sms_to" name="mobile" placeholder="请输入手机号，用于接收订单短信">
                </li>
                <li id="mail_to_container" style="display: none">
                    <label for="mail_to" class="span_up">接收订单邮箱</label>
                    <input type="text" id="mail_to" name="email" placeholder="请输入邮箱，用于接收订单邮件">
                    <p style="color: red">注：如果没收到邮件，请在邮件垃圾箱查找。</p>
                </li>
                <!--li class="youhui_show">
                    <span class="span_up">优惠券</span>
                    <input type="text" name="couponcode" placeholder="请填写你的优惠券" onchange="checkCoupon2()">
                </li-->
                @if(1 || @$config['theme']['list_type'] !== 'button')
                    <li style="width: calc( 100% - 40px ); height: auto">
                        <label for="description" class="span_up">商品说明</label>
                        <p id="description" class="ql-editor quill-html">
                        </p>
                    </li>
                @endif
            </ul>
        </div>
        @if(0 && @$config['theme']['list_type'] === 'button')
            <div class="right_form" style="width: 100%">
                <ul>
                    <li style="margin: 0 0 24px 0; width: 100%">
                        <label for="description" class="span_up">商品说明</label>
                        <p id="description" class="ql-editor quill-html">
                        </p>
                    </li>
                </ul>
            </div>
        @endif
        <div class="clear">
        </div>
        <!--付款方式-->
        <div class="pay_box">
            <div class="pay_menu">
                <div class="pay pay_cj_1 checked1">支付</div>
                <div class="all_pay"><i class="iconfont icon-zijin"></i> 应付总额：<b class="tprice" id="should-pay">0.00</b>元</div>
            </div>
            <div class="pay_list1">
                <?php $i_pay = 0;?>
                @foreach ($config['pays'] as $pay)
                    <?php $i_pay++; ?>
                    <label class="lab3 {!! $i_pay == 1 ? 'checked2' : '' !!}">
                        <input name="payway" value="{!! $pay['id'] !!}" title="{!! $pay['name'] !!}" type="radio" {!! $i_pay == 1 ? 'checked' : '' !!}>
                        <img src="{!! $pay['img'] !!}" alt="{!! $pay['name'] !!}" width="140">
                    </label>
                @endforeach
            </div>
        </div>
        <div id="submit">
            <button name="check_pay" class="check_pay" id="order-btn">确认支付</button>
        </div>
    </div>
</div>
<div>

</div>
<script type=text/javascript>var config = @json($config);</script>
<script type=text/javascript src="/shop_theme/classic/common.min.js?v={!! $version !!}"></script>
<script type=text/javascript src="/shop_theme/classic/pc.min.js?v={!! $version !!}"></script>
{!! $js_tj !!} {!! $js_kf !!}
</body>
</html>