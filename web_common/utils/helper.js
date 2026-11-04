import { QuillDeltaToHtmlConverter } from 'quill-delta-to-html';
import device from './device'

window.nullfunc = console.log;

Date.prototype.format = function (format) {
  if (!format) format = 'yyyy-MM-dd hh:mm:ss';
  const o = {
    'M+': this.getMonth() + 1, // month
    'd+': this.getDate(), // day
    'h+': this.getHours(), // hour
    'm+': this.getMinutes(), // minute
    's+': this.getSeconds(), // second
    'q+': Math.floor((this.getMonth() + 3) / 3), // quarter
    S: this.getMilliseconds() // millisecond
  };
  if (/(y+)/.test(format)) {
    format = format.replace(RegExp.$1,
      (this.getFullYear() + '').substr(4 - RegExp.$1.length));
  }
  for (const k in o) {
    if (new RegExp('(' + k + ')').test(format)) {
      format = format.replace(RegExp.$1,
        RegExp.$1.length === 1 ? o[k] :
          ('00' + o[k]).substr(('' + o[k]).length));
    }
  }
  return format;
};
const date = {
  getMonthDay (date) {
    return (date || new Date()).format('MM-dd');
  },
  getDateTime (date) {
    return (date || new Date()).format('yyyy-MM-dd hh:mm:ss');
  }
};

const cookie = (function () {
  function setCookie (name, value, expire = undefined) {
    if (!name || !value) return;
    if (expire !== undefined) {
      const expTime = new Date()
      expTime.setTime(expTime.getTime() + expire)
      document.cookie = name + '=' + encodeURI(value) + '; expires=' + expTime.toUTCString() + '; path=/'
    } else {
      document.cookie = name + '=' + encodeURI(value) + '; path=/'
    }
  }

  function getCookie (name) {
    const parts = ('; ' + document.cookie).split('; ' + name + '=');
    if (parts.length >= 2) return parts.pop().split(';').shift();
  }

  function delCookie (name) {
    setCookie(name, 'null', -1)
  }

  return {
    set: setCookie,
    get: getCookie,
    del: delCookie
  }
}())

export { date, device, cookie };

export function randomString (len) {
  len = len || 16;
  const $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';
  /** **默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
  const maxPos = $chars.length;
  let pwd = '';
  for (let i = 0; i < len; i++) {
    pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
  }
  return pwd;
}

String.prototype.random = randomString;

/**
 * 渲染Quill delta 节点, 返回HTML
 * @param {string} delta
 * @param {boolean} checkEmpty 如果此参数为真, 则delta为空时将返回false
 * @returns {*}
 */
export function renderDelta (delta, checkEmpty = false) {
  if (!delta || delta[0] !== '{') {
    return delta;
  }
  let body_json = null;
  try {
    body_json = JSON.parse(delta)
  } catch (e) {
    return delta
  }
  if (checkEmpty) {
    // {"ops":[{"insert":"\n"}]}
    if (!body_json.ops || !body_json.ops.length) {
      return false;
    }
    if (body_json.ops.length === 1 && body_json.ops[0].insert === '\n') {
      return false;
    }
  }
  if (1) { // mini convertor
    return `<div class="ql-snow"><div class="ql-editor quill-html">${
      new QuillDeltaToHtmlConverter(body_json.ops, {
        urlSanitizer(str){
          let val = str;
          val = val.replace(/^\s*/gm, '')
          let whiteList = /^((https?|s?ftp|blob|mailto|tel|mqqapi):|#|\/|data:image\/)/;
          if (whiteList.test(val)) {
            return val;
          }
          return 'unsafe:' + val;
        }
      }).convert()
    }</div></div>`;
  } else {
    // Quill.imports['formats/link'].PROTOCOL_WHITELIST.push('mqqapi');
    
    if (!this.for_render) {
      this.for_render = new Quill(document.createElement('div'));
    }
    this.for_render.setContents(body_json);
    return `<div class="ql-snow"><div class="ql-editor quill-html">${this.for_render.root.innerHTML}</div></div>`;
  }

}


export function htmlspecialchars (str) {
  const div = document.createElement('div');
  const text = document.createTextNode(str);
  div.appendChild(text);
  return div.innerHTML
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

export function getParameterByName (name, url) {
  if (!url) url = window.location.href;
  name = name.replace(/[\[\]]/g, '\\$&');
  const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
    results = regex.exec(url);
  if (!results) return null;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
}

/**
 * Simple param, (cannot handle array)
 * @param data
 * @returns {string}
 */
export function param (data = {}, parent = false) {
  const arr = [];
  for (let key in data) {
    if (!Object.prototype.hasOwnProperty.call(data, key)) continue;

    if (data[key] && typeof data[key] === 'object') {
      if (parent === false) {
        arr.push(param(data[key], encodeURIComponent(key)));
      } else {
        arr.push(param(data[key], parent + '[' + encodeURIComponent(key) + ']'));
      }
    } else {
      const value = encodeURIComponent((data[key] === null || data[key] === undefined) ? '' : data[key]);
      if (parent === false) {
        key = encodeURIComponent(key)
      } else {
        key = parent + '[' + encodeURIComponent(key) + ']'
      }
      arr.push(key + '=' + value)
    }
  }
  return arr.join('&');
}
