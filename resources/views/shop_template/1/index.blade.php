<!DOCTYPE html>
<html>
<head>
    <meta charset=utf-8>
    <meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">
    <title>{{ $name }}</title>
    <meta name=description content="{{ $description }}">
    <meta name=keywords content="{{ $keywords }}">
    <link href=/dist/css/app.74aff031052a2fb674fdade6aaac9ca1.css rel=stylesheet>
</head>
<body>
<div id=bkg></div>
<h1>hello</h1>
<div id=app></div>
<script type=text/javascript>var config = @json($config);</script>{!! $js_tj !!} {!! $js_kf !!}
</body>
</html>