<?php
$version = '1.8';
?>
@if(@preg_match('/(iPhone|iPod|Android|ios|SymbianOS|Windows Phone)/i', $_SERVER['HTTP_USER_AGENT']))
    @include('shop_theme.Classic.mobile')
@else
    @include('shop_theme.Classic.pc')
@endif
