Date.prototype.format = function (format = 'yyyy-MM-dd h:m:s') {
  let o = {
    'M+': this.getMonth() + 1, // month
    'd+': this.getDate(), // day
    'h+': this.getHours(), // hour
    'm+': this.getMinutes(), // minute
    's+': this.getSeconds(), // second
    'q+': Math.floor((this.getMonth() + 3) / 3), // quarter
    'S': this.getMilliseconds() // millisecond
  }
  if (/(y+)/.test(format)) {
    format = format.replace(RegExp.$1,
      (this.getFullYear() + '').substr(4 - RegExp.$1.length))
  }
  for (let k in o) {
    if (new RegExp('(' + k + ')').test(format)) {
      format = format.replace(RegExp.$1,
        RegExp.$1.length === 1 ? o[k]
          : ('00' + o[k]).substr(('' + o[k]).length))
    }
  }
  return format
}
String.prototype.strBetween = function (a, b) {
  let start = this.indexOf(a)
  if (start === -1) return ''
  start += a.length
  let end = this.indexOf(b, start)
  if (start === -1 || end === -1) return ''
  return this.substring(start, end)
}
Number.prototype.formatBigNumber = function () {
  let number = this
  if (number < 100000) return number
  return (number / 10000) + '万'
}

let cookie = (function () {
  function setCookie (name, value, expire = undefined) {
    if (!name || !value) return;
    if (expire !== undefined) {
      let expTime = new Date()
      expTime.setTime(expTime.getTime() + expire)
      document.cookie = name + '=' + encodeURI(value) + ';expires=' + expTime.toUTCString()
    } else {
      document.cookie = name + '=' + encodeURI(value)
    }
  }

  function getCookie (name) {
    let arr, reg = new RegExp('(^| )' + name + '=([^;]*)(;|$)')
    if (arr = document.cookie.match(reg)) {
      return decodeURI(arr[2])
    } else {
      return undefined
    }
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

const date = {
  getDate (date) {
    return (date || new Date()).format('yyyy-MM-dd');
  },
  getDateTime (date) {
    return (date || new Date()).format('yyyy-MM-dd hh:mm:ss');
  }
};

export default {
  cookie: cookie,
  isDesktop () {
    return window.innerWidth > 993
  },
  isMobile () {
    return navigator.userAgent.match(/(iPhone|iPod|Android|ios|SymbianOS)/i) != null
  },
  date: date
}
