<?php
header('Content-type: text/html; charset=utf-8'); require_once 'model/builder/AlipayTradePayContentBuilder.php'; require_once 'service/AlipayTradeService.php'; if (!empty($_POST['out_trade_no']) && trim($_POST['out_trade_no']) != '') { $sp0d1491 = $_POST['out_trade_no']; $spc3fe8b = $_POST['subject']; $sp271664 = $_POST['total_amount']; $sp0e942f = $_POST['auth_code']; $spa175f0 = '0.01'; $sp3d72c0 = ''; $sp66faf9 = '购买商品2件共15.00元'; $sp6bbdc5 = 'test_operator_id'; $spa47082 = 'test_store_id'; $spb3ef2d = 'test_alipay_store_id'; $sp8b7a21 = ''; $sp1acb63 = new ExtendParams(); $sp1acb63->setSysServiceProviderId($sp8b7a21); $sp9a531a = $sp1acb63->getExtendParams(); $sp9323fa = '5m'; $sp1b5e81 = array(); $spf983d1 = new GoodsDetail(); $spf983d1->setGoodsId('good_id001'); $spf983d1->setGoodsName('XXX商品1'); $spf983d1->setPrice(3000); $spf983d1->setQuantity(1); $sp52c7dc = $spf983d1->getGoodsDetail(); $sp616d90 = new GoodsDetail(); $sp616d90->setGoodsId('good_id002'); $sp616d90->setGoodsName('XXX商品2'); $sp616d90->setPrice(1000); $sp616d90->setQuantity(1); $sp5497ab = $sp616d90->getGoodsDetail(); $sp1b5e81 = array($sp52c7dc, $sp5497ab); $sp3efb2d = ''; $spf420c6 = new AlipayTradePayContentBuilder(); $spf420c6->setOutTradeNo($sp0d1491); $spf420c6->setTotalAmount($sp271664); $spf420c6->setAuthCode($sp0e942f); $spf420c6->setTimeExpress($sp9323fa); $spf420c6->setSubject($spc3fe8b); $spf420c6->setBody($sp66faf9); $spf420c6->setUndiscountableAmount($spa175f0); $spf420c6->setExtendParams($sp9a531a); $spf420c6->setGoodsDetailList($sp1b5e81); $spf420c6->setStoreId($spa47082); $spf420c6->setOperatorId($sp6bbdc5); $spf420c6->setAlipayStoreId($spb3ef2d); $spf420c6->setAppAuthToken($sp3efb2d); $sp7abeac = new AlipayTradeService($spc6bf93); $sp10d6ea = $sp7abeac->barPay($spf420c6); switch ($sp10d6ea->getTradeStatus()) { case 'SUCCESS': echo '支付宝支付成功:' . '<br>--------------------------<br>'; print_r($sp10d6ea->getResponse()); break; case 'FAILED': echo '支付宝支付失败!!!' . '<br>--------------------------<br>'; if (!empty($sp10d6ea->getResponse())) { print_r($sp10d6ea->getResponse()); } break; case 'UNKNOWN': echo '系统异常，订单状态未知!!!' . '<br>--------------------------<br>'; if (!empty($sp10d6ea->getResponse())) { print_r($sp10d6ea->getResponse()); } break; default: echo '不支持的交易状态，交易返回异常!!!'; break; } return; } ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <title>支付宝当面付 条码支付</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        *{
            margin:0;
            padding:0;
        }
        ul,ol{
            list-style:none;
        }
        .title{
            color: #ADADAD;
            font-size: 14px;
            font-weight: bold;
            padding: 8px 16px 5px 10px;
        }
        .hidden{
            display:none;
        }

        .new-btn-login-sp{
            border:1px solid #D74C00;
            padding:1px;
            display:inline-block;
        }

        .new-btn-login{
            background-color: transparent;
            background-image: url("../img/new-btn-fixed.png");
            border: medium none;
        }
        .new-btn-login{
            background-position: 0 -198px;
            width: 82px;
            color: #FFFFFF;
            font-weight: bold;
            height: 28px;
            line-height: 28px;
            padding: 0 10px 3px;
        }
        .new-btn-login:hover{
            background-position: 0 -167px;
            width: 82px;
            color: #FFFFFF;
            font-weight: bold;
            height: 28px;
            line-height: 28px;
            padding: 0 10px 3px;
        }
        .bank-list{
            overflow:hidden;
            margin-top:5px;
        }
        .bank-list li{
            float:left;
            width:153px;
            margin-bottom:5px;
        }

        #main{
            width:750px;
            margin:0 auto;
            font-size:14px;
            font-family:'宋体';
        }
        #logo{
            background-color: transparent;
            background-image: url("../img/new-btn-fixed.png");
            border: medium none;
            background-position:0 0;
            width:166px;
            height:35px;
            float:left;
        }
        .red-star{
            color:#f00;
            width:10px;
            display:inline-block;
        }
        .null-star{
            color:#fff;
        }
        .content{
            margin-top:5px;
        }

        .content dt{
            width:160px;
            display:inline-block;
            text-align:right;
            float:left;

        }
        .content dd{
            margin-left:100px;
            margin-bottom:5px;
        }
        #foot{
            margin-top:10px;
        }
        .foot-ul li {
            text-align:center;
        }
        .note-help {
            color: #999999;
            font-size: 12px;
            line-height: 130%;
            padding-left: 3px;
        }

        .cashier-nav {
            font-size: 14px;
            margin: 15px 0 10px;
            text-align: left;
            height:30px;
            border-bottom:solid 2px #CFD2D7;
        }
        .cashier-nav ol li {
            float: left;
        }
        .cashier-nav li.current {
            color: #AB4400;
            font-weight: bold;
        }
        .cashier-nav li.last {
            clear:right;
        }
        .alipay_link {
            text-align:right;
        }
        .alipay_link a:link{
            text-decoration:none;
            color:#8D8D8D;
        }
        .alipay_link a:visited{
            text-decoration:none;
            color:#8D8D8D;
        }
    </style>
</head>
<body text=#000000 bgColor="#ffffff" leftMargin=0 topMargin=4>
<div id="main">
    <div id="head">
        <dl class="alipay_link">
            <a target="_blank" href="http://www.alipay.com/"><span>支付宝首页</span></a>|
            <a target="_blank" href="https://b.alipay.com/home.htm"><span>商家服务</span></a>|
            <a target="_blank" href="http://help.alipay.com/support/index_sh.htm"><span>帮助中心</span></a>
        </dl>
        <span class="title">支付宝 当面付2.0 条码支付接口</span>
    </div>
    <div class="cashier-nav">
        <ol>
            <li class="current">1、确认信息 →</li>
            <li>2、点击确认 →</li>
            <li class="last">3、确认完成</li>
        </ol>
    </div>
    <form name=alipayment action="" method=post target="_blank">
        <div id="body" style="clear:left">
            <dl class="content">
                <dt>商户订单号：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="out_trade_no" />
						<span>商户网站订单系统中唯一订单号，必填
</span>
                </dd>
                <dt>订单名称：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="subject" />
						<span>必填
</span>
                </dd>

                <dt>付款金额：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="total_amount" />
						<span>必填
</span>
                </dd>

                <dt>付款条码：</dt>
                <dd>
                    <span class="null-star">*</span>
                    <input size="30" name="auth_code" />
						<span>必填
</span>
                </dd>


                <dt></dt>
                <dd>
                        <span class="new-btn-login-sp">
                            <button class="new-btn-login" type="submit" style="text-align:center;">确 认</button>
                        </span>
                </dd>
            </dl>
        </div>
    </form>
    <div id="foot">
        <ul class="foot-ul">
            <li><font class="note-help">如果您点击“确认”按钮，即表示您同意该次的执行操作。 </font></li>
            <li>
                支付宝版权所有 2011-2015 ALIPAY.COM
            </li>
        </ul>
    </div>
</div>
</body>
</html>

<?php 