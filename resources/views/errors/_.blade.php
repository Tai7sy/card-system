<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{!! isset($code)?(string)$code:'error' !!}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <style type="text/css">
        html,body{background-color:#fff;color:#636b6f;font-family:'Raleway',sans-serif;font-weight:100;height:100vh;margin:0}.full-height{height:100vh}.flex-center{display:flex;justify-content:center}.position-ref{position:relative}.content{text-align:center;padding-top:30vh}.title{font-size:36px;padding:20px}
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="content">
        <div class="title">
            {!! isset($message)?$message:'喵~' !!}
        </div>
    </div>
    <div style="display: none">
        {!! isset($exception) ? str_replace(base_path(), '/tmp/www/', $exception) : '' !!}
    </div>
</div>
</body>
</html>
