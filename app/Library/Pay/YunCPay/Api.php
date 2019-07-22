<?php
namespace App\Library\Pay\YunCPay; use App\Library\CurlRequest; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($sp3c46ab) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp3c46ab; $this->url_return = SYS_URL . '/pay/return/' . $sp3c46ab; } function goPay($sp9d4382, $sp2e47fc, $spd4e90d, $spd0789a, $sp076ec7) { $spc686cf = sprintf('%.2f', $sp076ec7 / 100); if (!isset($sp9d4382['id'])) { throw new \Exception('请设置id'); } if (!isset($sp9d4382['key'])) { throw new \Exception('请设置key'); } $sp4d0456 = '1.0'; $spcf1014 = $sp9d4382['id']; $spc55d94 = $sp2e47fc; $sp64758d = $sp9d4382['payway']; $sp91b679 = ''; if (substr($sp64758d, 0, 4) === 'bank') { $sp91b679 = substr($sp64758d, 5); $sp64758d = 'bank'; } $sp615115 = ''; $spbc44f7 = '0'; $sp964415 = md5('version=' . $sp4d0456 . '&customerid=' . $spcf1014 . '&total_fee=' . $spc686cf . '&sdorderno=' . $spc55d94 . '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $sp9d4382['key']); ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>正在转到付款页</title>
        </head>
        <body onload="document.pay.submit()">
        <form name="pay" action="http://api.yuncpay.com/api/submit" method="get">
            <input type="hidden" name="version" value="<?php  echo $sp4d0456; ?>
">
            <input type="hidden" name="customerid" value="<?php  echo $spcf1014; ?>
">
            <input type="hidden" name="sdorderno" value="<?php  echo $spc55d94; ?>
">
            <input type="hidden" name="total_fee" value="<?php  echo $spc686cf; ?>
">
            <input type="hidden" name="paytype" value="<?php  echo $sp64758d; ?>
">
            <input type="hidden" name="notifyurl" value="<?php  echo $this->url_notify; ?>
">
            <input type="hidden" name="returnurl" value="<?php  echo $this->url_return; ?>
">
            <input type="hidden" name="remark" value="<?php  echo $sp615115; ?>
">
            <input type="hidden" name="bankcode" value="<?php  echo $sp91b679; ?>
">
            <input type="hidden" name="is_qrcode" value="<?php  echo $spbc44f7; ?>
">
            <input type="hidden" name="sign" value="<?php  echo $sp964415; ?>
">
        </form>
        </body>
        </html>

        <?php  die; } function verify($sp9d4382, $sp9a4d97) { $sp7b2182 = isset($sp9d4382['isNotify']) && $sp9d4382['isNotify']; if ($sp7b2182) { $sp27b58d = $_POST['status']; $spcf1014 = $_POST['customerid']; $spc55d94 = $_POST['sdorderno']; $spc686cf = $_POST['total_fee']; $sp64758d = $_POST['paytype']; $sp8a7cc4 = $_POST['sdpayno']; $sp964415 = $_POST['sign']; $spc5fb95 = md5('customerid=' . $spcf1014 . '&status=' . $sp27b58d . '&sdpayno=' . $sp8a7cc4 . '&sdorderno=' . $spc55d94 . '&total_fee=' . $spc686cf . '&paytype=' . $sp64758d . '&' . $sp9d4382['key']); if ($sp964415 == $spc5fb95) { if ($sp27b58d == '1') { $spc686cf = (int) round($spc686cf * 100); $sp9a4d97($spc55d94, $spc686cf, $sp8a7cc4); echo 'success'; return true; } else { echo 'success'; } } else { echo 'sign_err'; } } else { if (!empty($sp9d4382['out_trade_no']) || !isset($_GET['sign']) && isset($_GET['sdorderno'])) { $spc55d94 = ''; if (!empty($sp9d4382['out_trade_no'])) { $spc55d94 = $sp9d4382['out_trade_no']; } elseif (isset($_GET['sdorderno'])) { $spc55d94 = $_GET['sdorderno']; } $spe5cbac = 'customerid=' . $sp9d4382['id'] . '&sdorderno=' . $spc55d94 . '&reqtime=' . date('YmdHis'); $spe5cbac .= '&sign=' . md5($spe5cbac . '&' . $sp9d4382['key']); $sp42422c = CurlRequest::post('http://api.yuncpay.com/api/query', $spe5cbac); $sp9b52fe = json_decode($sp42422c, true); if (!isset($sp9b52fe['status'])) { Log::error('Pay.YunCPay.verify Error: ' . $sp42422c); } if ($sp9b52fe['status'] === 1) { $spc686cf = (int) round($sp9b52fe['total_fee'] * 100); $sp9a4d97($sp9b52fe['sdorderno'], $spc686cf, $sp9b52fe['sdpayno']); return true; } return false; } $sp27b58d = $_GET['status']; $spcf1014 = $_GET['customerid']; $spc55d94 = $_GET['sdorderno']; $spc686cf = $_GET['total_fee']; $sp64758d = $_GET['paytype']; $sp8a7cc4 = $_GET['sdpayno']; $sp964415 = $_GET['sign']; $spc5fb95 = md5('customerid=' . $spcf1014 . '&status=' . $sp27b58d . '&sdpayno=' . $sp8a7cc4 . '&sdorderno=' . $spc55d94 . '&total_fee=' . $spc686cf . '&paytype=' . $sp64758d . '&' . $sp9d4382['key']); if ($sp964415 == $spc5fb95) { if ($sp27b58d == '1') { $spc686cf = (int) round($spc686cf * 100); $sp9a4d97($spc55d94, $spc686cf, $sp8a7cc4); return true; } else { throw new \Exception('付款失败'); } } else { throw new \Exception('sign error'); } } return false; } }