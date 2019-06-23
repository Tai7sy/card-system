<!DOCTYPE html><html lang=zh-CN><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0"><title>管理后台</title><link href=/dist/css/chunk-27a8ea72.5fdf1b82.css rel=prefetch><link href=/dist/css/chunk-3f9945b0.9833ec72.css rel=prefetch><link href=/dist/css/chunk-538673b6.f457901a.css rel=prefetch><link href=/dist/css/chunk-5ac83a38.9f4ecd88.css rel=prefetch><link href=/dist/css/chunk-9b8ac54e.8868b1fa.css rel=prefetch><link href=/dist/js/chunk-27a8ea72.36d4a319.js rel=prefetch><link href=/dist/js/chunk-2d0da573.cc6c63c1.js rel=prefetch><link href=/dist/js/chunk-2d0e5357.33e738ed.js rel=prefetch><link href=/dist/js/chunk-3821d039.b6252f95.js rel=prefetch><link href=/dist/js/chunk-3f9945b0.58a78226.js rel=prefetch><link href=/dist/js/chunk-520bbfda.24caa307.js rel=prefetch><link href=/dist/js/chunk-538673b6.0b064a14.js rel=prefetch><link href=/dist/js/chunk-5ac83a38.a2346768.js rel=prefetch><link href=/dist/js/chunk-775f0977.80b1e344.js rel=prefetch><link href=/dist/js/chunk-9b8ac54e.bc46eb2a.js rel=prefetch><link href=/dist/js/chunk-cfda387e.459ab644.js rel=prefetch><link href=/dist/js/chunk-fd8ae5d8.992e3373.js rel=prefetch><link href=/dist/css/app.d4f0743e.css rel=preload as=style><link href=/dist/css/chunk-vendors.d313cf4b.css rel=preload as=style><link href=/dist/js/app.740bcfa4.js rel=preload as=script><link href=/dist/js/chunk-vendors.87be6e22.js rel=preload as=script><link href=/dist/css/chunk-vendors.d313cf4b.css rel=stylesheet><link href=/dist/css/app.d4f0743e.css rel=stylesheet></head><body><noscript><strong>We're sorry but this page doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id=progress><div id=progress-bar><div id=progress-inner></div><div style="position: absolute;width: 100%;z-index: 10">加载中，请稍后...</div></div><div id=progress-timeout style="display: none; margin-top: 10vh"><p>如果您长时间停留在此页面，请确认使用的是最新版浏览器</p><p>推荐浏览器：<a href=https://www.google.cn/intl/zh-CN_ALL/chrome/ target=_blank>Chrome</a>&nbsp;&nbsp; <a href=https://www.mozilla.org/zh-CN/firefox/ target=_blank>FireFox</a>&nbsp;&nbsp; <a href=http://browser.qq.com/ target=_blank><del>QQ浏览器</del></a></p></div><style type=text/css>#progress-timeout {position: fixed;width: 100%;}
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
    }, 10000);</script></div><div id=app></div><script>var config = @json($config);</script><script src=/dist/js/chunk-vendors.87be6e22.js></script><script src=/dist/js/app.740bcfa4.js></script></body></html>