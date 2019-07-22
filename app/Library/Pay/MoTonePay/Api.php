<?php
namespace App\Library\Pay\MoTonePay; use App\Library\Pay\ApiInterface; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($sp3c46ab) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp3c46ab; $this->url_return = SYS_URL . '/pay/return/' . $sp3c46ab; } function goPay($sp9d4382, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spc686cf = sprintf('%.2f', $sp076ec7 / 100); $sp4d0456 = '1.0'; $sp64758d = $sp9d4382['payway']; $sp67f8ec = '0'; $sp964415 = md5('version=' . $sp4d0456 . '&customerid=' . $sp9d4382['id'] . '&total_fee=' . $spc686cf . '&sdorderno=' . $sp2e47fc . '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $sp9d4382['key']); ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf8">
            <title>正在转到付款页</title>
        </head>
        <body onLoad="document.pay.submit()">
        <form name="pay" action="http://www.motonepay.com/apisubmit" method="post">
            <input type="hidden" name="version" value="<?php  echo $sp4d0456; ?>
">
            <input type="hidden" name="customerid" value="<?php  echo $sp9d4382['id']; ?>
">
            <input type="hidden" name="sdorderno" value="<?php  echo $sp2e47fc; ?>
">
            <input type="hidden" name="total_fee" value="<?php  echo $spc686cf; ?>
">
            <input type="hidden" name="paytype" value="<?php  echo $sp64758d; ?>
">
            <input type="hidden" name="notifyurl" value="<?php  echo $this->url_notify; ?>
">
            <input type="hidden" name="returnurl" value="<?php  echo $this->url_return; ?>
">
            <input type="hidden" name="sign" value="<?php  echo $sp964415; ?>
">
            <input type="hidden" name="get_code" value="<?php  echo $sp67f8ec; ?>
">
        </form>
        </body>
        </html>
        <?php  die; } function verify($sp9d4382, $sp9a4d97) { $sp7b2182 = isset($sp9d4382['isNotify']) && $sp9d4382['isNotify']; if ($sp7b2182) { $sp27b58d = $_POST['status']; $spcf1014 = $_POST['customerid']; $spc55d94 = $_POST['sdorderno']; $spc686cf = $_POST['total_fee']; $sp64758d = $_POST['paytype']; $sp8a7cc4 = $_POST['sdpayno']; $sp964415 = $_POST['sign']; $spc5fb95 = md5('customerid=' . $spcf1014 . '&status=' . $sp27b58d . '&sdpayno=' . $sp8a7cc4 . '&sdorderno=' . $spc55d94 . '&total_fee=' . $spc686cf . '&paytype=' . $sp64758d . '&' . $sp9d4382['key']); if ($sp964415 == $spc5fb95) { if ($sp27b58d == '1') { $spc686cf = (int) round($spc686cf * 100); $sp9a4d97($spc55d94, $spc686cf, $sp8a7cc4); echo 'success'; return true; } else { echo 'success'; } } else { echo 'sign_err'; } } else { if (!empty($sp9d4382['out_trade_no'])) { return false; } $sp27b58d = $_GET['status']; $spcf1014 = $_GET['customerid']; $spc55d94 = $_GET['sdorderno']; $spc686cf = $_GET['total_fee']; $sp64758d = $_GET['paytype']; $sp8a7cc4 = $_GET['sdpayno']; $sp964415 = $_GET['sign']; $spc5fb95 = md5('customerid=' . $spcf1014 . '&status=' . $sp27b58d . '&sdpayno=' . $sp8a7cc4 . '&sdorderno=' . $spc55d94 . '&total_fee=' . $spc686cf . '&paytype=' . $sp64758d . '&' . $sp9d4382['key']); if ($sp964415 == $spc5fb95) { if ($sp27b58d == '1') { $spc686cf = (int) round($spc686cf * 100); $sp9a4d97($spc55d94, $spc686cf, $sp8a7cc4); return true; } else { throw new \Exception('付款失败'); } } else { throw new \Exception('sign error'); } } return false; } }