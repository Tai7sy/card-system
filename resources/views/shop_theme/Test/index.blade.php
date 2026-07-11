<?php
$version = '1.0';
?>
@if(@preg_match('/(iPhone|iPod|Android|ios|SymbianOS|Windows Phone)/i', $_SERVER['HTTP_USER_AGENT']))
    @include('shop_theme.Test.mobile')
@else
    @include('shop_theme.Test.pc')
@endif
