<?php
$version = '1.3';
?>
        <!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
    <title>{{ $name }}</title>
    <meta name=description content="{{ $description }}">
    <meta name=keywords content="{{ $keywords }}">
    <script src="/shop_theme/ms/jquery-1.8.3.min.js"></script>
    <link href="/shop_theme/ms/app.css?v={!! $version !!}" rel="stylesheet" type="text/css">
    <link href="/plugins/css/quill.snow.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/plugins/js/quill.min.js"></script>
    @if(@$config['captcha']['scene']['shop']['buy'] && $config['captcha']['driver'] === 'geetest')
        <script type="text/javascript" src="/plugins/js/gt.js"></script>
    @endif
</head>
<body>
<section class="header-store">
    <div class="header">
        <!--导航-->
        <div class="logo">
            <a href="#">
                @if(@strlen(config('app.logo')))
                    <img src="{{ config('app.logo') }}" alt="" height="48">
                @else
                    <span>{{ config('app.name') }}</span>
                @endif
            </a>
        </div>

        <div class="nav-buttons">
            <div class="button-container"><a href="#">首页</a></div>
            <div class="button-container"><a target="_blank" href="/s#/record">查询订单</a></div>
            @if(config('app.project') === 'card')
                <div class="button-container"><a target="_blank" href="/s#/report">投诉订单</a></div>
            @endif
        </div>

        <!--手机菜单按钮-->
        <div class="nav-mobile-menu-btn">
            <div class="icon-menu">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                     preserveAspectRatio="xMidYMid" width="100%" height="100%" viewBox="0 0 18 16">
                    <path d="M-0.000,16.000 L-0.000,14.000 L18.000,14.000 L18.000,16.000 L-0.000,16.000 ZM-0.000,7.000 L18.000,7.000 L18.000,9.000 L-0.000,9.000 L-0.000,7.000 ZM-0.000,-0.000 L18.000,-0.000 L18.000,2.000 L-0.000,2.000 L-0.000,-0.000 Z"
                          fill="#767676" fill-rule="evenodd"></path>
                </svg>
            </div>
        </div>
        <!--手机菜单-->
        <div class="nav-mobile-menu">
            <ul class="menu">
                <li class="menu-item"><a href="#">首页</a></li>
                <li class="menu-item"><a target="_blank" href="/s#/record">查询订单</a></li>
                @if(config('app.project') === 'card')
                    <li class="menu-item"><a target="_blank" href="/s#/report">投诉订单</a></li>
                @endif
            </ul>
        </div>
    </div>
</section>

<div id="page-1">
    <div class="shop-ann" id="ann">
        <div>
            商家名称:&nbsp;{{ $config['shop']['name'] }}，商家ＱＱ:&nbsp;{{ $config['shop']['qq'] }}
        </div>
        <p class="container ql-editor quill-html"></p>
    </div>

    <div class="shop-crumbs">
        <div class="shop-crumbs-content" id="shop-crumbs-content">
            <a href="{{ (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}" }}">{{ $config['shop']['name'] }}</a>
            &gt; <a></a>
            &gt; <a></a>
        </div>
    </div>

    <section class="shop-body">

        <div class="pd-info">

            <div class="title-item">
                <h1 tabindex="0"></h1>
            </div>
            <div class="price-item cutline">
                <div class="priceName">
                    <span class="current">售价 :</span>
                </div>
                <ul class="price">
                    <li tabindex="0" id="price" class="current"><b></b></li>
                </ul>
                <button id="priceWholeBtn" class="seeMore">批发价格 &gt;</button>
            </div>

            <div class="categorySelect cutline">
                <div class="selectTitle" tabindex="0">请选择分类</div>
                <div id="categories">
                </div>
            </div>

            <div class="productSelect cutline">
                <div class="selectTitle" tabindex="0">请选择商品</div>
                <div id="products" class="options-half">
                </div>
            </div>
            @if(in_array('sms_send_order', $config['functions']) || in_array('mail_send_order', $config['functions']))
                <div class="extService cutline">
                    <div class="selectTitle" tabindex="0">增值服务</div>

                    <div class="options-half">
                        @if(in_array('sms_send_order', $config['functions']))
                            <div class="option option-sms">
                                <a href="javascript:;" tabindex="0">
                                    <div class="p-texts">
                                        <span class="p-name">通过短信接收订单信息</span>
                                        <span class="p-price">{{ $config['sms_send_order']['sms_price'] ? ('￥' . ($config['sms_send_order']['sms_price']/100)): '免费' }}</span>
                                    </div>
                                </a>
                            </div>
                        @endif
                        @if(in_array('mail_send_order', $config['functions']))
                            <div class="option option-mail">
                                <a href="javascript:;" tabindex="0">
                                    <div class="p-texts">
                                        <span class="p-name">通过邮箱接收订单信息</span>
                                        <span class="p-price">免费</span>
                                    </div>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="buy">
                <button class="add2cart buyBtn">立即购买</button>
            </div>

            <div class="buyMobile">
                <div class="navs"></div>
                <button class="btn buyBtn">立即购买</button>
            </div>

        </div>
        <div class="pd-gap"></div>
        <div class="pd-desc">
            <p class="description-content ql-editor quill-html">
            </p>
        </div>

        <div class="pd-desc-mobile">
            <p class="description-content ql-editor quill-html">
            </p>
        </div>

        <div class="pd-mobile-bottom"></div>
    </section>
</div>

<div id="page-checkout" style="display: none">

    <div class="checkoutContainer">
        <div class="showMobileOrder" onclick="$('.rightContainer').toggleClass('show');">
            <div class="numbers"><span>订单信息 : </span></div>
            <div class="price"><span>总价 : </span>
                <span class="price-label">¥19,852.00</span>
                <span class="vector go" name="go">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         preserveAspectRatio="xMidYMid" width="100%" height="100%" viewBox="0 0 5.281 9">
                        <path d="M1.186,0.494 L5.469,4.500 L1.186,8.506 " stroke="#666" stroke-width="1" fill="none"
                              fill-rule="evenodd"></path>
                    </svg>
                </span>
            </div>
            <div class="loading"></div>
        </div>
        <div class="leftContainer">
            <div class="moduleList">
                <div class="moduleTitle bottomline"><span>1.收货信息</span></div>
                <form>
                    <div class="li" id="count-box">
                        <label for="count"><i>*</i><em>购买数量</em></label>
                        <div class="inputbox">
                            <input id="count" type="number">
                        </div>
                    </div>
                    <div class="li" id="contact-box">
                        <label for="contact"><i>*</i><em>联系方式</em></label>
                        <div class="inputbox">
                            <input id="contact" placeholder="">
                        </div>
                    </div>
                    <div class="li" id="coupon-box">
                        <label for="coupon"><em>优惠券</em></label>
                        <div class="inputbox">
                            <input id="coupon" placeholder="请输入优惠券(没有可不填)">
                        </div>
                        <p id="coupon-tip"></p>
                    </div>
                    @if(in_array('sms_send_order', $config['functions']))
                        <p class="tip">
                            <label>
                                <input type="checkbox" id="send-sms"
                                       onclick="$('#sms_to-box')[this.checked ? 'show' : 'hide']();calcTotalPrice();">
                                <span>&nbsp;通过短信接收订单信息（￥{{ $config['sms_send_order']['sms_price']/100 }}）</span>
                            </label>
                        </p>
                        <div class="li" id="sms_to-box">
                            <label for="sms_to"><em>接收手机号</em></label>
                            <div class="inputbox">
                                <input id="sms_to" name="mobile" placeholder="请输入手机号，用于接收订单短信">
                            </div>
                        </div>
                    @endif
                    @if(in_array('mail_send_order', $config['functions']))
                        <p class="tip">
                            <label>
                                <input type="checkbox" id="send-mail"
                                       onclick="$('#mail_to-box')[this.checked ? 'show' : 'hide']();calcTotalPrice();">
                                <span>&nbsp;通过邮件接收订单信息</span>
                            </label>
                        </p>
                        <div class="li" id="mail_to-box">
                            <label for="mail_to"><em>接收邮箱</em></label>
                            <div class="inputbox">
                                <input id="mail_to" name="email" placeholder="请输入邮箱，用于接收订单邮件">
                            </div>
                        </div>
                    @endif
                </form>
                <div class="moduleTitle bottomline"><span>2.付款方式</span></div>
                <div class="paywayContainer">
                    <?php $i_pay = 0;?>
                    @foreach ($config['pays'] as $pay)
                        <?php $i_pay++; ?>
                        <label class="paywayBtn {!! $i_pay == 1 ? 'checked' : '' !!}">
                            <input name="payway" value="{!! $pay['id'] !!}" title="{!! $pay['name'] !!}"
                                   type="radio" {!! $i_pay == 1 ? 'checked' : '' !!}>
                            <img src="{!! $pay['img'] !!}" alt="{!! $pay['name'] !!}">
                        </label>
                    @endforeach
                </div>

                <div class="checkoutBtnContainer">
                    <button class="checkout checkoutBtn">提交订单</button>
                </div>

                <div class="buyMobile">
                    <div class="navs"></div>
                    <button class="btn checkoutBtn">提交订单</button>
                </div>
            </div>
        </div>
        <div class="gapContainer"></div>
        <div class="rightContainer" onclick="event.target === this ? $('.rightContainer').toggleClass('show') : '';">
            <div class="rightContainerInner moduleList">
                <div class="moduleTitle">
                    <span>订单信息</span>
                    <a class="edit" href="javascript:history.go(-1);">返回商店</a>
                </div>
                <div class="closeList" onclick="$('.rightContainer').toggleClass('show');">
                    <span class="vector go">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                             preserveAspectRatio="xMidYMid" width="100%" height="100%" viewBox="0 0 5.281 9">
                            <path d="M1.186,0.494 L5.469,4.500 L1.186,8.506 " stroke="#666" stroke-width="1" fill="none"
                                  fill-rule="evenodd"></path>
                        </svg>
                    </span>
                    <em>收起订单信息</em>
                </div>
                <div class="shopList">
                    <ul>
                        <li class="mainList">
                            <div class="product-name"><p>商品名称</p></div>
                            <div class="product-priceBox">
                                <p>¥ 5,788.00</p>
                                <p>数量：2</p>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="result">
                    <ul>
                        <li class="all">
                            <em>总计</em><b class="price-label">¥19,852.00</b>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>

<script type=text/javascript>var config = @json($config);</script>
<script type=text/javascript src="/shop_theme/ms/app.min.js?v={!! $version !!}"></script>
{!! $js_tj !!} {!! $js_kf !!}
<div id="message-template" style="display: none">
    <div class="modal-background" id="{id}">
        <div class="modal-content" onclick="closeModal('{id}', this, event);">
            <div class="modal-msg" tabindex="0">
                {message}
                <button class="close"
                        onclick="closeModal('{id}');"
                        tabindex="0">
                    <div class="vector-icon icon">
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                             preserveAspectRatio="xMidYMid" width="100%" height="100%" viewBox="0 0 14.124 14.125">
                            <path d="M12.719,14.134 L7.062,8.477 L1.405,14.134 L-0.009,12.719 L5.648,7.063 L-0.009,1.406 L1.405,-0.009 L7.062,5.648 L12.719,-0.009 L14.133,1.406 L8.476,7.063 L14.133,12.719 L12.719,14.134 Z"
                                  fill-rule="evenodd" fill="#767676"></path>
                        </svg>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>
</body>
</html>