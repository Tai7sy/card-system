<?php
namespace App\Library\Pay\YunCPay; use App\Library\CurlRequest; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($spe00284) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $spe00284; $this->url_return = SYS_URL . '/pay/return/' . $spe00284; } function goPay($spc27de0, $spba04f6, $sp9f49de, $sp224c81, $sp6956b3) { $sp36f78e = sprintf('%.2f', $sp6956b3 / 100); if (!isset($spc27de0['id'])) { throw new \Exception('请设置id'); } if (!isset($spc27de0['key'])) { throw new \Exception('请设置key'); } $sp48d343 = '1.0'; $sp66357d = $spc27de0['id']; $spbd7295 = $spba04f6; $spf14b2e = $spc27de0['payway']; $spd6b4e4 = ''; if (substr($spf14b2e, 0, 4) === 'bank') { $spd6b4e4 = substr($spf14b2e, 5); $spf14b2e = 'bank'; } $spf420e3 = ''; $sp29c10f = '0'; $sp75e4cc = md5('version=' . $sp48d343 . '&customerid=' . $sp66357d . '&total_fee=' . $sp36f78e . '&sdorderno=' . $spbd7295 . '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $spc27de0['key']); ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>正在转到付款页</title>
        </head>
        <body onload="document.pay.submit()">
        <form name="pay" action="http://api.yuncpay.com/api/submit" method="get">
            <input type="hidden" name="version" value="<?php  echo $sp48d343; ?>
">
            <input type="hidden" name="customerid" value="<?php  echo $sp66357d; ?>
">
            <input type="hidden" name="sdorderno" value="<?php  echo $spbd7295; ?>
">
            <input type="hidden" name="total_fee" value="<?php  echo $sp36f78e; ?>
">
            <input type="hidden" name="paytype" value="<?php  echo $spf14b2e; ?>
">
            <input type="hidden" name="notifyurl" value="<?php  echo $this->url_notify; ?>
">
            <input type="hidden" name="returnurl" value="<?php  echo $this->url_return; ?>
">
            <input type="hidden" name="remark" value="<?php  echo $spf420e3; ?>
">
            <input type="hidden" name="bankcode" value="<?php  echo $spd6b4e4; ?>
">
            <input type="hidden" name="is_qrcode" value="<?php  echo $sp29c10f; ?>
">
            <input type="hidden" name="sign" value="<?php  echo $sp75e4cc; ?>
">
        </form>
        </body>
        </html>

        <?php  die; } function verify($spc27de0, $sp4294a3) { $spb2acff = isset($spc27de0['isNotify']) && $spc27de0['isNotify']; if ($spb2acff) { $spc3ee59 = $_POST['status']; $sp66357d = $_POST['customerid']; $spbd7295 = $_POST['sdorderno']; $sp36f78e = $_POST['total_fee']; $spf14b2e = $_POST['paytype']; $spb9a60b = $_POST['sdpayno']; $sp75e4cc = $_POST['sign']; $sp036b38 = md5('customerid=' . $sp66357d . '&status=' . $spc3ee59 . '&sdpayno=' . $spb9a60b . '&sdorderno=' . $spbd7295 . '&total_fee=' . $sp36f78e . '&paytype=' . $spf14b2e . '&' . $spc27de0['key']); if ($sp75e4cc == $sp036b38) { if ($spc3ee59 == '1') { $sp36f78e = (int) round($sp36f78e * 100); $sp4294a3($spbd7295, $sp36f78e, $spb9a60b); echo 'success'; return true; } else { echo 'success'; } } else { echo 'sign_err'; } } else { if (!empty($spc27de0['out_trade_no']) || !isset($_GET['sign']) && isset($_GET['sdorderno'])) { $spbd7295 = ''; if (!empty($spc27de0['out_trade_no'])) { $spbd7295 = $spc27de0['out_trade_no']; } elseif (isset($_GET['sdorderno'])) { $spbd7295 = $_GET['sdorderno']; } $sp33b002 = 'customerid=' . $spc27de0['id'] . '&sdorderno=' . $spbd7295 . '&reqtime=' . date('YmdHis'); $sp33b002 .= '&sign=' . md5($sp33b002 . '&' . $spc27de0['key']); $sp4187a7 = CurlRequest::post('http://api.yuncpay.com/api/query', $sp33b002); $sp29a775 = json_decode($sp4187a7, true); if (!isset($sp29a775['status'])) { Log::error('Pay.YunCPay.verify Error: ' . $sp4187a7); } if ($sp29a775['status'] === 1) { $sp36f78e = (int) round($sp29a775['total_fee'] * 100); $sp4294a3($sp29a775['sdorderno'], $sp36f78e, $sp29a775['sdpayno']); return true; } return false; } $spc3ee59 = $_GET['status']; $sp66357d = $_GET['customerid']; $spbd7295 = $_GET['sdorderno']; $sp36f78e = $_GET['total_fee']; $spf14b2e = $_GET['paytype']; $spb9a60b = $_GET['sdpayno']; $sp75e4cc = $_GET['sign']; $sp036b38 = md5('customerid=' . $sp66357d . '&status=' . $spc3ee59 . '&sdpayno=' . $spb9a60b . '&sdorderno=' . $spbd7295 . '&total_fee=' . $sp36f78e . '&paytype=' . $spf14b2e . '&' . $spc27de0['key']); if ($sp75e4cc == $sp036b38) { if ($spc3ee59 == '1') { $sp36f78e = (int) round($sp36f78e * 100); $sp4294a3($spbd7295, $sp36f78e, $spb9a60b); return true; } else { throw new \Exception('付款失败'); } } else { throw new \Exception('sign error'); } } return false; } }