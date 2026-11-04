

export default {
  isDesktop () {
    return window.innerWidth > 993;
  },
  detectOs () {
    let result = null;
    if (/NT 5./i.test(navigator.userAgent)) {
      result = /wow64|win64|ia64|x64/i.test(navigator.userAgent) ? 'xp-64' : 'xp-32';
    } else if (/NT 6.|NT 7.|NT 8.|NT 9.|NT 10./i.test(navigator.userAgent)) {
      result = /wow64|win64|ia64|x64/i.test(navigator.userAgent) ? 'vista-64' : 'vista-32';
    } else if (/iPhone|iPad|iPod/i.test(navigator.userAgent)) {
      result = 'ios';
    } else if (/Android/i.test(navigator.userAgent)) {
      result = 'android';
    } else if (/Macintosh/i.test(navigator.userAgent)) {
      result = 'osx';
    }
    return result;
  },
  isMobile () {
    return navigator.userAgent.match(/(iPhone|iPod|Android|ios|SymbianOS)/i) !== null
  },
  isQQ () {
    return / QQ\//i.test(navigator.userAgent);
  },
  isWechat () {
    return /MicroMessenger/i.test(navigator.userAgent);
  }
}
