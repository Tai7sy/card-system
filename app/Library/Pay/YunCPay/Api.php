<?php
namespace App\Library\Pay\YunCPay; use App\Library\CurlRequest; use App\Library\Pay\ApiInterface; use Illuminate\Support\Facades\Log; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($sp53f8aa) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp53f8aa; $this->url_return = SYS_URL . '/pay/return/' . $sp53f8aa; } function goPay($spbe80b7, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp9624ba = sprintf('%.2f', $sp5213ee / 100); if (!isset($spbe80b7['id'])) { throw new \Exception('请设置id'); } if (!isset($spbe80b7['key'])) { throw new \Exception('请设置key'); } $sp631694 = '1.0'; $sp26d3c8 = $spbe80b7['id']; $sp51f37b = $spa3e681; $spa216ff = $spbe80b7['payway']; $sp46a95b = ''; if (substr($spa216ff, 0, 4) === 'bank') { $sp46a95b = substr($spa216ff, 5); $spa216ff = 'bank'; } $sp894a06 = ''; $sp3ffa27 = '0'; $spa109d2 = md5('version=' . $sp631694 . '&customerid=' . $sp26d3c8 . '&total_fee=' . $sp9624ba . '&sdorderno=' . $sp51f37b . '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $spbe80b7['key']); ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>正在转到付款页</title>
        </head>
        <body onload="document.pay.submit()">
        <form name="pay" action="http://api.yuncpay.com/api/submit" method="get">
            <input type="hidden" name="version" value="<?php  echo $sp631694; ?>
">
            <input type="hidden" name="customerid" value="<?php  echo $sp26d3c8; ?>
">
            <input type="hidden" name="sdorderno" value="<?php  echo $sp51f37b; ?>
">
            <input type="hidden" name="total_fee" value="<?php  echo $sp9624ba; ?>
">
            <input type="hidden" name="paytype" value="<?php  echo $spa216ff; ?>
">
            <input type="hidden" name="notifyurl" value="<?php  echo $this->url_notify; ?>
">
            <input type="hidden" name="returnurl" value="<?php  echo $this->url_return; ?>
">
            <input type="hidden" name="remark" value="<?php  echo $sp894a06; ?>
">
            <input type="hidden" name="bankcode" value="<?php  echo $sp46a95b; ?>
">
            <input type="hidden" name="is_qrcode" value="<?php  echo $sp3ffa27; ?>
">
            <input type="hidden" name="sign" value="<?php  echo $spa109d2; ?>
">
        </form>
        </body>
        </html>

        <?php  die; } function verify($spbe80b7, $sp04f0f8) { $sp3bce01 = isset($spbe80b7['isNotify']) && $spbe80b7['isNotify']; if ($sp3bce01) { $sp7a9982 = $_POST['status']; $sp26d3c8 = $_POST['customerid']; $sp51f37b = $_POST['sdorderno']; $sp9624ba = $_POST['total_fee']; $spa216ff = $_POST['paytype']; $spefbfe7 = $_POST['sdpayno']; $spa109d2 = $_POST['sign']; $spa0547c = md5('customerid=' . $sp26d3c8 . '&status=' . $sp7a9982 . '&sdpayno=' . $spefbfe7 . '&sdorderno=' . $sp51f37b . '&total_fee=' . $sp9624ba . '&paytype=' . $spa216ff . '&' . $spbe80b7['key']); if ($spa109d2 == $spa0547c) { if ($sp7a9982 == '1') { $sp9624ba = (int) round($sp9624ba * 100); $sp04f0f8($sp51f37b, $sp9624ba, $spefbfe7); echo 'success'; return true; } else { echo 'success'; } } else { echo 'sign_err'; } } else { if (!empty($spbe80b7['out_trade_no']) || !isset($_GET['sign']) && isset($_GET['sdorderno'])) { $sp51f37b = ''; if (!empty($spbe80b7['out_trade_no'])) { $sp51f37b = $spbe80b7['out_trade_no']; } elseif (isset($_GET['sdorderno'])) { $sp51f37b = $_GET['sdorderno']; } $sp8e4af8 = 'customerid=' . $spbe80b7['id'] . '&sdorderno=' . $sp51f37b . '&reqtime=' . date('YmdHis'); $sp8e4af8 .= '&sign=' . md5($sp8e4af8 . '&' . $spbe80b7['key']); $sp00a165 = CurlRequest::post('http://api.yuncpay.com/api/query', $sp8e4af8); $spb9589c = json_decode($sp00a165, true); if (!isset($spb9589c['status'])) { Log::error('Pay.YunCPay.verify Error: ' . $sp00a165); } if ($spb9589c['status'] === 1) { $sp9624ba = (int) round($spb9589c['total_fee'] * 100); $sp04f0f8($spb9589c['sdorderno'], $sp9624ba, $spb9589c['sdpayno']); return true; } return false; } $sp7a9982 = $_GET['status']; $sp26d3c8 = $_GET['customerid']; $sp51f37b = $_GET['sdorderno']; $sp9624ba = $_GET['total_fee']; $spa216ff = $_GET['paytype']; $spefbfe7 = $_GET['sdpayno']; $spa109d2 = $_GET['sign']; $spa0547c = md5('customerid=' . $sp26d3c8 . '&status=' . $sp7a9982 . '&sdpayno=' . $spefbfe7 . '&sdorderno=' . $sp51f37b . '&total_fee=' . $sp9624ba . '&paytype=' . $spa216ff . '&' . $spbe80b7['key']); if ($spa109d2 == $spa0547c) { if ($sp7a9982 == '1') { $sp9624ba = (int) round($sp9624ba * 100); $sp04f0f8($sp51f37b, $sp9624ba, $spefbfe7); return true; } else { throw new \Exception('付款失败'); } } else { throw new \Exception('sign error'); } } return false; } }