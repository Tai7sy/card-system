<!DOCTYPE html><html lang=zh-CN><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0"><title>管理后台</title><link href=/dist/css/chunk-3825e858.8a06cdd6.css rel=prefetch><link href=/dist/css/chunk-5ac83a38.9f4ecd88.css rel=prefetch><link href=/dist/css/chunk-aa38f030.b90aa78c.css rel=prefetch><link href=/dist/css/chunk-b3925668.fca89832.css rel=prefetch><link href=/dist/css/chunk-d813bb8a.1fb6be7a.css rel=prefetch><link href=/dist/js/chunk-2d0da573.e3407dae.js rel=prefetch><link href=/dist/js/chunk-2d0e5357.7aac7b01.js rel=prefetch><link href=/dist/js/chunk-3821d039.3170adfa.js rel=prefetch><link href=/dist/js/chunk-3825e858.48ee9c7d.js rel=prefetch><link href=/dist/js/chunk-520bbfda.0af1f0eb.js rel=prefetch><link href=/dist/js/chunk-5ac83a38.7c4fa9c9.js rel=prefetch><link href=/dist/js/chunk-775f0977.a2ca61e7.js rel=prefetch><link href=/dist/js/chunk-aa38f030.8e5492d7.js rel=prefetch><link href=/dist/js/chunk-b3925668.7497b569.js rel=prefetch><link href=/dist/js/chunk-cfda387e.0f699527.js rel=prefetch><link href=/dist/js/chunk-d813bb8a.97f5a10c.js rel=prefetch><link href=/dist/js/chunk-fd8ae5d8.0b17a361.js rel=prefetch><link href=/dist/css/app.0a99135f.css rel=preload as=style><link href=/dist/css/chunk-vendors.75047081.css rel=preload as=style><link href=/dist/js/app.2555bc07.js rel=preload as=script><link href=/dist/js/chunk-vendors.144fefc2.js rel=preload as=script><link href=/dist/css/chunk-vendors.75047081.css rel=stylesheet><link href=/dist/css/app.0a99135f.css rel=stylesheet></head><body><noscript><strong>We're sorry but this page doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id=progress><div id=progress-bar><div id=progress-inner></div><div style="position: absolute;width: 100%;z-index: 10">加载中，请稍后...</div></div><div id=progress-timeout style="display: none; margin-top: 10vh"><p>如果您长时间停留在此页面，请确认使用的是最新版浏览器</p><p>推荐浏览器：<a href=https://www.google.cn/intl/zh-CN_ALL/chrome/ target=_blank>Chrome</a>&nbsp;&nbsp; <a href=https://www.mozilla.org/zh-CN/firefox/ target=_blank>FireFox</a>&nbsp;&nbsp; <a href=http://browser.qq.com/ target=_blank><del>QQ浏览器</del></a></p></div><style type=text/css>#progress-timeout {position: fixed;width: 100%;}
    #progress {text-align: center;width: 100%;height: 100vh;position: absolute;background: #fff;z-index: 99999;}
    #progress-bar {border: 1px solid #eee;position: fixed;top: 0;right: 0;left: 0;height: 20px;background: #f5f5f5;width: 80%;margin: 30vh auto 0;border-radius: 8px;}
    #progress-inner {width: 1%;background: #56c0d4;position: absolute;top: 0;left: 0;bottom: 0;border-radius: 8px;}</style><script>var progress_val = 1;
    var progress_interval;

    function progress_add () {
      progress_val += parseInt(Math.random() * 2 + 5 * progress_val / 100);
      if (progress_val >= 90) {
        progress_val = 90;
        clearInterval(progress_interval);
      }
      var element = document.getElementById('progress-inner');
      if (element) element.style.width = progress_val + '%';
      else clearInterval(progress_interval);
    }
    progress_interval = setInterval(progress_add, 100);

    setTimeout(function () {
      var element = document.getElementById('progress-timeout');
      if (element) element.style.display = 'block';
    }, 10000);</script></div><div id=app></div><script>var config = @json($config);</script><script src=/dist/js/chunk-vendors.144fefc2.js></script><script src=/dist/js/app.2555bc07.js></script></body></html>