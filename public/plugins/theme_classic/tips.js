/*
*text:内容  
*delay:延迟时间  
*/
;(function ($) {
  function Tip(config) {
    this.config = {
      text: '出错了',
      delay: 3000,
      bgColor: '#fff0f0'//background:#fff0f0;
    };
    //默认参数扩展
    if (config && $.isPlainObject(config)) {
      $.extend(this.config, config);
    }
    ;
    this.wrap = $('<div id="tips_div" style="z-index:99999999999;position:fixed;left:0;top:0;width:100%;text-align:center;line-height: 3em;font-size: 1.3em;background:' + this.config.bgColor + ';-webkit-transform:translateY(-100%);-ms-transform:translateY(-100%);transform:translateY(-100%);-webkit-transition:all .2s ease 0s;transition:all .2s ease 0s"></div>');
    this.init();
  };
  Tip.prototype.init = function () {
    var _this = this;
    try {
      $('#tips_div').remove();
    } catch (e) {
    }
    $('body').append(_this.wrap.html(_this.config.text));
    _this.show();

  };
  Tip.prototype.show = function () {
    var _this = this;
    setTimeout(function () {
      _this.wrap.css({
        '-webkit-transform': 'translateY(0)',
        'transform': 'translateY(0)'
      });
    }, 100);
    _this.hide();
  };
  Tip.prototype.hide = function () {
    var _this = this;
    setTimeout(function () {
      _this.wrap.css({
        '-webkit-transform': 'translateY(-100%)',
        'transform': 'translateY(-100%)'
      });
    }, _this.config.delay);
    setTimeout(function () {
      _this.wrap.remove();
    }, _this.config.delay + 250);
  };
  window.Tip = Tip;
  $.tip = function (config) {
    return new Tip(config);
  }
})(window.jQuery || $);


/**
 * 消息通知
 * @constructor
 */
function YsToast() {}
YsToast.ok = function(text) {
  $.tip({
    text: text,
    delay: 2500,
    bgColor: '#c9e2b3'
  });
};
YsToast.error = function(text) {
  $.tip({
    text: text,
    delay: 3500,
    bgColor: '#e4b9c0'
  });
};
YsToast.warn = function(text) {
  $.tip({
    text: text,
    delay: 3000,
    bgColor: '#f7e1b5'
  });
};
YsToast.info = function(text) {
  $.tip({
    text: text,
    delay: 3000,
    bgColor: '#a6e1ec'
  });
};