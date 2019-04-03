<?php
error_reporting(E_ERROR); require_once 'phpqrcode/phpqrcode.php'; $sp81923e = urldecode($_GET['data']); QRcode::png($sp81923e);