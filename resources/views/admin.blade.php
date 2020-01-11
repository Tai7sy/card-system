<!DOCTYPE html><html lang=zh-CN><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0"><meta http-equiv=X-UA-Compatible content="IE=edge,chrome=1"><title>管理后台</title><link href=/dist/css/chunk-0ea253ab.3c69e8a0.css rel=prefetch><link href=/dist/css/chunk-3faba02d.f02ac703.css rel=prefetch><link href=/dist/css/chunk-40bf5937.751dc3c4.css rel=prefetch><link href=/dist/css/chunk-4bd10f7f.10bd47dc.css rel=prefetch><link href=/dist/css/chunk-5558d5d4.9d316f2b.css rel=prefetch><link href=/dist/js/chunk-0ea253ab.09579b78.js rel=prefetch><link href=/dist/js/chunk-2d0da573.cfb12603.js rel=prefetch><link href=/dist/js/chunk-2d0e5357.74f6c71e.js rel=prefetch><link href=/dist/js/chunk-3821d039.fc5352d3.js rel=prefetch><link href=/dist/js/chunk-3faba02d.ca4bb57d.js rel=prefetch><link href=/dist/js/chunk-40bf5937.14305a50.js rel=prefetch><link href=/dist/js/chunk-4bd10f7f.a37d2ea0.js rel=prefetch><link href=/dist/js/chunk-520bbfda.770ea13b.js rel=prefetch><link href=/dist/js/chunk-5558d5d4.542c3ca9.js rel=prefetch><link href=/dist/js/chunk-775f0977.0257ae2b.js rel=prefetch><link href=/dist/js/chunk-cfda387e.36372cdb.js rel=prefetch><link href=/dist/js/chunk-fd8ae5d8.dd192c0e.js rel=prefetch><link href=/dist/css/app.883df8bb.css rel=preload as=style><link href=/dist/css/chunk-vendors.8b2e4be8.css rel=preload as=style><link href=/dist/js/app.917b7c1e.js rel=preload as=script><link href=/dist/js/chunk-vendors.3831e604.js rel=preload as=script><link href=/dist/css/chunk-vendors.8b2e4be8.css rel=stylesheet><link href=/dist/css/app.883df8bb.css rel=stylesheet></head><body><noscript><strong>We're sorry but this page doesn't work properly without JavaScript enabled. Please enable it to continue.</strong></noscript><div id=progress><div id=progress-bar><div id=progress-inner></div><div style="position: absolute;width: 100%;z-index: 10">加载中，请稍后...</div></div><div id=progress-timeout style="display: none; margin-top: 10vh"><p>如果您长时间停留在此页面，请确认使用的是最新版浏览器</p><p>推荐浏览器：<a href=https://www.google.cn/intl/zh-CN_ALL/chrome/ target=_blank>Chrome</a>&nbsp;&nbsp; <a href=https://www.mozilla.org/zh-CN/firefox/ target=_blank>FireFox</a>&nbsp;&nbsp; <a href=http://browser.qq.com/ target=_blank><del>QQ浏览器</del></a></p></div><style type=text/css>#progress-timeout {position: fixed;width: 100%;}
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
    }, 10000);</script></div><div id=app></div><script>var config = @json($config);</script><script src=/dist/js/chunk-vendors.3831e604.js></script><script src=/dist/js/app.917b7c1e.js></script></body></html>