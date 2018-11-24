<!DOCTYPE html><html><head><meta charset=utf-8><meta name=viewport content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0"><title>{{ $name }}</title><meta name=description content="{{ $description }}"><meta name=keywords content="{{ $keywords }}"><link href=/dist/css/app.47f059bc.css rel=stylesheet></head><body><div id=progress><div id=progress-bar><div id=progress-inner></div><div style="position: absolute;width: 100%;z-index: 10">加载中，请稍后...</div></div><div id=progress-timeout style="display: none; margin-top: 10vh"><p>如果您长时间停留在此页面，请确认使用的是最新版浏览器</p><p>推荐浏览器：<a href=https://www.google.cn/intl/zh-CN_ALL/chrome/ target=_blank>Chrome</a>&nbsp;&nbsp; <a href=https://www.mozilla.org/zh-CN/firefox/ target=_blank>FireFox</a>&nbsp;&nbsp; <a href=http://browser.qq.com/ target=_blank><del>QQ浏览器</del></a></p></div><style type=text/css>#progress-timeout {
      position: fixed;
      width: 100%;
    }

    #progress {
      text-align: center;
      width: 100%;
      height: 100vh;
      position: absolute;
      background: #fff;
      z-index: 99999;
    }

    #progress-bar {
      border: 1px solid #eee;
      position: fixed;
      top: 0;
      right: 0;
      left: 0;
      height: 20px;
      background: #f5f5f5;
      width: 80%;
      margin: 30vh auto 0;
      border-radius: 8px;
    }

    #progress-inner {
      width: 1%;
      background: #56c0d4;
      position: absolute;
      top: 0;
      left: 0;
      bottom: 0;
      border-radius: 8px;
    }</style><script type=text/javascript>var progress_val = 1;
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
    }, 10000);</script></div><div id=bkg></div><div id=app></div><script type=text/javascript>var config = @json($config);</script>{!! $js_tj !!} {!! $js_kf !!}<script type=text/javascript src=/dist/js/manifest.ae2041ba.js></script><script type=text/javascript src=/dist/js/vendor.1dd89d6e.js></script><script type=text/javascript src=/dist/js/app.d0e75a03.js></script></body></html>