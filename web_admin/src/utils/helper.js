window.nullfunc = e => {
  console.log(e);
};

import device from './device'

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
  getDate (date) {
    return (date || new Date()).format('yyyy-MM-dd');
  },
  getDateTime (date) {
    return (date || new Date()).format('yyyy-MM-dd hh:mm:ss');
  }
};

export { date };

export { device };

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
