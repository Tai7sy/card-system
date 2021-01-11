<?php
if (!isset($result)) {
    $result = [
        'success' => false,
        'msg' => '未知错误, 渲染失败'
    ];
}
if (!isset($result['success'])) {
    $result['success'] = false;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
        html{background:#eee}
        body{background:#fff;color:#333;font-family:"微软雅黑","Microsoft YaHei",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:10px 10px 10px rgba(0,0,0,.13);box-shadow:10px 10px 10px rgba(0,0,0,.13);opacity:.8}
        h1{border-bottom:1px solid #dadada;clear:both;color:#666;font:24px "微软雅黑","Microsoft YaHei",sans-serif;margin:30px 0 0 0;padding:0 0 7px}
        #page{margin-top:50px}
        #page p{font-size:16px;line-height:1.5;margin:25px 0 20px; word-wrap: break-word;}
        #page code{font-family:Consolas,Monaco,monospace}
        ul li{margin-bottom:10px;font-size:9px}
        a{color:#21759b;text-decoration:none;margin-top:-10px}
        a:hover{color:#d54e21}
        .quill-html{margin-top: -12px;margin-bottom: 36px;padding: 0 !important;}
        .quill-html img{max-width:100%}
        .quill-html p{margin:0!important}
        .card-txt{width:100%;border:1px solid #999}
    </style>
    @if(isset($product))
        <link rel="stylesheet" href="/plugins/css/quill.snow.css">
        <script type="text/javascript" src="/plugins/js/quill.min.js"></script>
        <script type="text/javascript" src="/plugins/js/clipboard.min.js"></script>
    @endif
    <title>支付结果</title>
</head>
<body id="page">
<h3>{{ isset($result['title']) ? $result['title'] : ($result['success'] ? '订单已支付' : '支付失败！' ) }}</h3>
<div>
    <p class="status-msg">{!! $result['msg'] !!}</p>
    @if(isset($card_txt))
        <div>
            <textarea id="card-txt" class="card-txt" title="卡密列表" rows="6" readonly>{{ $card_txt }}</textarea>
            <a id="card-copy" href="#" data-clipboard-target="#card-txt">一键复制</a>
            <script type="text/javascript">
                new ClipboardJS('#card-copy');
            </script>
        </div>
    @endif
    <br/>
    @if(isset($product))
        <!-- render instructions -->
        <div class="ql-snow">
            <div class="ql-editor quill-html" id="instructions"></div>
        </div>
        <script type="text/javascript">
            Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('mqqapi');
            function renderDescription(delta){if(!delta){return''}if(typeof delta==='string'){if(delta[0]!=='{'){return delta}try{delta=JSON.parse(delta)}catch(e){return delta}}var for_render=new Quill(document.createElement('div'));for_render.setContents(delta);return for_render.root.innerHTML}
            document.getElementById('instructions').innerHTML = renderDescription({!! $product['instructions'] !!});
        </script>
    @endif
    <a style="float:right" href="javascript:window.close()">关闭</a>
    <br/>
</div>
</body>
</html>
