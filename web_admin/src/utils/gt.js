/* initGeetest 1.0.0
 * 用于加载id对应的验证码库，并支持宕机模式
 * 暴露 initGeetest 进行验证码的初始化
 * 一般不需要用户进行修改
 */
(function (global, factory) {
  'use strict';

  if (typeof module === 'object' && typeof module.exports === 'object') {
    // CommonJS
    module.exports = global.document ?
      factory(global, true) :
      function (w) {
        if (!w.document) {
          throw new Error('Geetest requires a window with a document');
        }
        return factory(w);
      };
  } else {
    factory(global);
  }
}(typeof window !== 'undefined' ? window : this, (window, noGlobal) => {
  'use strict';

  if (typeof window === 'undefined') {
    throw new Error('Geetest requires browser environment');
  }
  const document = window.document;
  const Math = window.Math;
  const head = document.getElementsByTagName('head')[0];

  function _Object (obj) {
    this._obj = obj;
  }

  _Object.prototype = {
    _each (process) {
      const _obj = this._obj;
      for (const k in _obj) {
        if (_obj.hasOwnProperty(k)) {
          process(k, _obj[k]);
        }
      }
      return this;
    }
  };

  function Config (config) {
    const self = this;
    new _Object(config)._each((key, value) => {
      self[key] = value;
    });
  }
  const isNumber = function (value) {
    return typeof value === 'number';
  };
  const isString = function (value) {
    return typeof value === 'string';
  };
  const isBoolean = function (value) {
    return typeof value === 'boolean';
  };
  const isObject = function (value) {
    return typeof value === 'object' && value !== null;
  };
  const isFunction = function (value) {
    return typeof value === 'function';
  };
  Config.prototype = {
    api_server: 'api.geetest.com',
    protocol: 'http://',
    type_path: '/gettype.php',
    fallback_config: {
      slide: {
        static_servers: ['static.geetest.com', 'dn-staticdown.qbox.me'],
        type: 'slide',
        slide: '/static/js/geetest.0.0.0.js'
      },
      fullpage: {
        static_servers: ['static.geetest.com', 'dn-staticdown.qbox.me'],
        type: 'fullpage',
        fullpage: '/static/js/fullpage.0.0.0.js'
      }
    },
    _get_fallback_config () {
      const self = this;
      if (isString(self.type)) {
        return self.fallback_config[self.type];
      } else if (self.new_captcha) {
        return self.fallback_config.fullpage;
      } else {
        return self.fallback_config.slide;
      }
    },
    _extend (obj) {
      const self = this;
      new _Object(obj)._each((key, value) => {
        self[key] = value;
      })
    }
  };
  const callbacks = {};
  const status = {};
  const random = function () {
    return parseInt(Math.random() * 10000) + (new Date()).valueOf();
  };
  const loadScript = function (url, cb) {
    const script = document.createElement('script');
    script.charset = 'UTF-8';
    script.async = true;
    script.onerror = function () {
      cb(true);
    };
    let loaded = false;
    script.onload = script.onreadystatechange = function () {
      if (!loaded &&
        (!script.readyState ||
          script.readyState === 'loaded' ||
          script.readyState === 'complete')) {
        loaded = true;
        setTimeout(() => {
          cb(false);
        }, 0);
      }
    };
    script.src = url;
    head.appendChild(script);
  };
  const normalizeDomain = function (domain) {
    return domain.replace(/^https?:\/\/|\/$/g, '');
  };
  const normalizePath = function (path) {
    path = path.replace(/\/+/g, '/');
    if (path.indexOf('/') !== 0) {
      path = '/' + path;
    }
    return path;
  };
  const normalizeQuery = function (query) {
    if (!query) {
      return '';
    }
    let q = '?';
    new _Object(query)._each((key, value) => {
      if (isString(value) || isNumber(value) || isBoolean(value)) {
        q = q + encodeURIComponent(key) + '=' + encodeURIComponent(value) + '&';
      }
    });
    if (q === '?') {
      q = '';
    }
    return q.replace(/&$/, '');
  };
  const makeURL = function (protocol, domain, path, query) {
    domain = normalizeDomain(domain);

    let url = normalizePath(path) + normalizeQuery(query);
    if (domain) {
      url = protocol + domain + url;
    }

    return url;
  };
  const load = function (protocol, domains, path, query, cb) {
    const tryRequest = function (at) {
      const url = makeURL(protocol, domains[at], path, query);
      loadScript(url, err => {
        if (err) {
          if (at >= domains.length - 1) {
            cb(true);
          } else {
            tryRequest(at + 1);
          }
        } else {
          cb(false);
        }
      });
    };
    tryRequest(0);
  };
  const jsonp = function (domains, path, config, callback) {
    if (isObject(config.getLib)) {
      config._extend(config.getLib);
      callback(config);
      return;
    }
    if (config.offline) {
      callback(config._get_fallback_config());
      return;
    }
    const cb = 'geetest_' + random();
    window[cb] = function (data) {
      if (data.status === 'success') {
        callback(data.data);
      } else if (!data.status) {
        callback(data);
      } else {
        callback(config._get_fallback_config());
      }
      window[cb] = undefined;
      try {
        delete window[cb];
      } catch (e) {
        console.log(e);
      }
    };
    load(config.protocol, domains, path, {
      gt: config.gt,
      callback: cb
    }, err => {
      if (err) {
        callback(config._get_fallback_config());
      }
    });
  };
  const throwError = function (errorType, config) {
    const errors = {
      networkError: '网络错误'
    };
    if (typeof config.onError === 'function') {
      config.onError(errors[errorType]);
    } else {
      throw new Error(errors[errorType]);
    }
  };
  const detect = function () {
    return !!window.Geetest;
  };
  if (detect()) {
    status.slide = 'loaded';
  }
  const initGeetest = function (userConfig, callback) {
    const config = new Config(userConfig);
    if (userConfig.https) {
      config.protocol = 'https://';
    } else if (!userConfig.protocol) {
      config.protocol = window.location.protocol + '//';
    }
    jsonp([config.api_server || config.apiserver], config.type_path, config, newConfig => {
      const type = newConfig.type;
      const init = function () {
        config._extend(newConfig);
        callback(new window.Geetest(config));
      };
      callbacks[type] = callbacks[type] || [];
      const s = status[type] || 'init';
      if (s === 'init') {
        status[type] = 'loading';
        callbacks[type].push(init);
        load(config.protocol, newConfig.static_servers || newConfig.domains, newConfig[type] || newConfig.path, null, err => {
          if (err) {
            status[type] = 'fail';
            throwError('networkError', config);
          } else {
            status[type] = 'loaded';
            const cbs = callbacks[type];
            for (let i = 0, len = cbs.length; i < len; i = i + 1) {
              const cb = cbs[i];
              if (isFunction(cb)) {
                cb();
              }
            }
            callbacks[type] = [];
          }
        });
      } else if (s === 'loaded') {
        init();
      } else if (s === 'fail') {
        throwError('networkError', config);
      } else if (s === 'loading') {
        callbacks[type].push(init);
      }
    });
  };
  window.initGeetest = initGeetest;
  return initGeetest;
}));

