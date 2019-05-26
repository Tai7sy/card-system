<?php
error_reporting(E_ERROR); require_once 'phpqrcode/phpqrcode.php'; $sp59c732 = urldecode($_GET['data']); QRcode::png($sp59c732);