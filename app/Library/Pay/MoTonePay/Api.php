<?php
namespace App\Library\Pay\MoTonePay; use App\Library\Pay\ApiInterface; class Api implements ApiInterface { private $url_notify = ''; private $url_return = ''; public function __construct($sp53f8aa) { $this->url_notify = SYS_URL_API . '/pay/notify/' . $sp53f8aa; $this->url_return = SYS_URL . '/pay/return/' . $sp53f8aa; } function goPay($spbe80b7, $spa3e681, $sp45f07e, $sp873488, $sp5213ee) { $sp9624ba = sprintf('%.2f', $sp5213ee / 100); $sp631694 = '1.0'; $spa216ff = $spbe80b7['payway']; $spcd67c1 = '0'; $spa109d2 = md5('version=' . $sp631694 . '&customerid=' . $spbe80b7['id'] . '&total_fee=' . $sp9624ba . '&sdorderno=' . $spa3e681 . '&notifyurl=' . $this->url_notify . '&returnurl=' . $this->url_return . '&' . $spbe80b7['key']); ?>
        <!doctype html>
        <html>
        <head>
            <meta charset="utf8">
            <title>正在转到付款页</title>
        </head>
        <body onLoad="document.pay.submit()">
        <form name="pay" action="http://www.motonepay.com/apisubmit" method="post">
            <input type="hidden" name="version" value="<?php  echo $sp631694; ?>
">
            <input type="hidden" name="customerid" value="<?php  echo $spbe80b7['id']; ?>
">
            <input type="hidden" name="sdorderno" value="<?php  echo $spa3e681; ?>
">
            <input type="hidden" name="total_fee" value="<?php  echo $sp9624ba; ?>
">
            <input type="hidden" name="paytype" value="<?php  echo $spa216ff; ?>
">
            <input type="hidden" name="notifyurl" value="<?php  echo $this->url_notify; ?>
">
            <input type="hidden" name="returnurl" value="<?php  echo $this->url_return; ?>
">
            <input type="hidden" name="sign" value="<?php  echo $spa109d2; ?>
">
            <input type="hidden" name="get_code" value="<?php  echo $spcd67c1; ?>
">
        </form>
        </body>
        </html>
        <?php  die; } function verify($spbe80b7, $sp04f0f8) { $sp3bce01 = isset($spbe80b7['isNotify']) && $spbe80b7['isNotify']; if ($sp3bce01) { $sp7a9982 = $_POST['status']; $sp26d3c8 = $_POST['customerid']; $sp51f37b = $_POST['sdorderno']; $sp9624ba = $_POST['total_fee']; $spa216ff = $_POST['paytype']; $spefbfe7 = $_POST['sdpayno']; $spa109d2 = $_POST['sign']; $spa0547c = md5('customerid=' . $sp26d3c8 . '&status=' . $sp7a9982 . '&sdpayno=' . $spefbfe7 . '&sdorderno=' . $sp51f37b . '&total_fee=' . $sp9624ba . '&paytype=' . $spa216ff . '&' . $spbe80b7['key']); if ($spa109d2 == $spa0547c) { if ($sp7a9982 == '1') { $sp9624ba = (int) round($sp9624ba * 100); $sp04f0f8($sp51f37b, $sp9624ba, $spefbfe7); echo 'success'; return true; } else { echo 'success'; } } else { echo 'sign_err'; } } else { if (!empty($spbe80b7['out_trade_no'])) { return false; } $sp7a9982 = $_GET['status']; $sp26d3c8 = $_GET['customerid']; $sp51f37b = $_GET['sdorderno']; $sp9624ba = $_GET['total_fee']; $spa216ff = $_GET['paytype']; $spefbfe7 = $_GET['sdpayno']; $spa109d2 = $_GET['sign']; $spa0547c = md5('customerid=' . $sp26d3c8 . '&status=' . $sp7a9982 . '&sdpayno=' . $spefbfe7 . '&sdorderno=' . $sp51f37b . '&total_fee=' . $sp9624ba . '&paytype=' . $spa216ff . '&' . $spbe80b7['key']); if ($spa109d2 == $spa0547c) { if ($sp7a9982 == '1') { $sp9624ba = (int) round($sp9624ba * 100); $sp04f0f8($sp51f37b, $sp9624ba, $spefbfe7); return true; } else { throw new \Exception('付款失败'); } } else { throw new \Exception('sign error'); } } return false; } }