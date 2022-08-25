<?php
$version = '2.3';
?>
@if(@preg_match('/(iPhone|iPod|Android|ios|SymbianOS|Windows Phone)/i', $_SERVER['HTTP_USER_AGENT']))
    @include('shop_theme.ACG.mobile')
@else
    @include('shop_theme.ACG.pc')
@endif
