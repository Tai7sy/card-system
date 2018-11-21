// http://ww1.sinaimg.cn/large/8a01d75dly1fobfoo2z2yj21120kowgj.jpg
(function copyright () {
  /* const hello = `
console.log('%c', 'background: #fff url("http://qqadapt.qpic.cn/mqq_photo_svr/Q3auHgzwzM5p5Rx6ibAXjia2pNiaxbxtTMTHJWDIiaHgcxaG5m7Vkspj1g/") center center / contain no-repeat; color: transparent; padding: 64px; line-height: 148px');
console.log('%c'+'C'+'o'+'d'+'e'+' '+'&'+' '+'T'+'h'+'e'+'m'+'e'+' '+'B'+'y'+' '+'风'+'铃','color:red;font-size:13px');
console.log('%c'+'Q'+'Q'+':'+'1'+'9'+'0'+'6'+'0'+' '+'©'+'2'+'0'+'1'+'8','color:red;font-size:13px');
  `;*/
  /**
   * 解密函数
   * @param str 待解密字符串
   * @returns {string}
   */
  const str_decrypt = str => {
    str = decodeURIComponent(str);
    let c = String.fromCharCode(str.charCodeAt(0) - str.length);

    for (let i = 1; i < str.length; i++) {
      c += String.fromCharCode(str.charCodeAt(i) - c.charCodeAt(i - 1));
    }
    return c;
  }


  /*
  * 1. 加密 http://tool.lu/js/
  * 2. 混淆 http://www.moralsoft.com/jso-online/hdojso.htm
  * */
  const hello = '%C7%91m%C3%92%C3%9D%C3%A1%C3%A2%C3%9B%C3%91%C2%93%C2%9A%C3%9B%C3%96%C2%8FOL%C2%88%C2%8ASLG%C2%89%C3%83%C3%84%C3%8E%C3%92%C3%99%C3%A1%C3%A4%C3%A3%C3%92%C2%9EZC%C2%89%C3%8C%C3%8C%C2%86%C2%95%C3%A7%C3%9E%C2%94J%C2%8A%C3%9C%C3%A8%C3%A4%C2%AAi%5E%C2%A0%C3%A2%C3%92%C3%85%C3%85%C3%91%C3%A4%C2%A2%C2%9F%C3%A1%C3%99%C3%8C%C2%91%C2%91%C3%91%C2%9D%C2%9C%C3%9E%C3%A2%C3%90%C3%8F%C3%98%C3%97%C3%A3%C3%A3%C3%8E%C3%92%C3%A9%C3%A8%C2%A1%C2%80%C2%84%C2%94%C3%96%C2%BD%C2%AF%C3%A1%C3%B1%C3%B1%C3%87%C2%82%C2%A5%C2%A5%C2%87%C3%8A%C2%AE%C2%9F%C3%8B%C2%A3%C2%99%C3%82%C3%93%C3%8A%C2%93%C2%A2%C2%BE%C2%B7%C3%8A%C3%99%C3%9A%C3%9A%C3%AC%C3%88%C2%A1%C2%A1%C2%9C%C2%92%C2%A1%C2%9B%C2%8D%C2%B2%C3%8A%C2%A9%C2%AF%C3%8A%C3%9B%C3%99%C2%A8%7C%C2%A2%C2%A4%C2%8D%C3%81%C3%9E%C3%A3%C3%9A%C2%9B%C2%98%C2%96QKI%C2%83%C3%88%C3%93%C3%A2%C3%99%C3%97%C2%92%C2%83%C3%88%C3%93%C3%A2%C3%99%C3%97%C2%92OO%C2%83%C3%92%C3%9D%C3%A2%C3%95%C3%8A%C3%97%C2%8E%C2%8E%C3%9D%C2%9C%C2%9F%C3%97%C3%95%C3%95%C3%86%C3%95%C2%AF%5B%C2%83%C3%92%C3%9B%C3%9B%C3%A1%C2%ACZ%C2%94%C3%A6%C3%93%C3%8F%C3%A1%C3%A3%C3%91%C3%93%C3%97%C3%93%C3%A2%C2%AF%5B%C2%90%C3%91%C3%85%C3%88%C3%8D%C3%97%C3%95%C2%A1ZVj%C2%A4%C3%A8%C2%B3%5B%C2%8C%C3%95%C3%97%C3%93%C2%92%C2%95%C3%8D%C3%8E%C3%90%C3%8F%C3%9C%C2%AEZQel%C2%A8%C3%A8%C2%9FPdEm%C3%92%C3%9D%C3%A1%C3%A2%C3%9B%C3%91%C2%93%C2%9A%C3%9B%C3%96%C2%8FOL%C2%88%C2%8ARRjjRR%C2%96%C2%96RR%C2%8B%C2%8BRR%C2%8C%C2%8CRRGGRRMMRRGGRR%7B%7BRR%C2%8F%C2%8FRR%C2%8C%C2%8CRR%C2%94%C2%94RR%C2%8C%C2%8CRRGGRRiiRR%C2%A0%C2%A0RRGGRR%E9%A3%B5%E9%A3%B5RR%E9%93%AA%E9%93%AASS%C2%8A%C3%92%C3%9B%C3%9B%C3%A1%C2%AC%C2%AC%C3%97%C3%89%C2%9F%C2%A1%C3%95%C3%9D%C3%A2%C2%A1%C2%A0%C3%9C%C3%A3%C3%9F%C2%9Fkd%C2%A3%C3%A8%C2%9FPdEm%C3%92%C3%9D%C3%A1%C3%A2%C3%9B%C3%91%C2%93%C2%9A%C3%9B%C3%96%C2%8FOL%C2%88%C2%8ARRxxRRxxRRaaRRXXRR%60%60RRWWRR%5D%5DRRWWRRGGRR%C3%90%C3%90RRYYRRWWRRXXRR__SS%C2%8A%C3%92%C3%9B%C3%9B%C3%A1%C2%AC%C2%AC%C3%97%C3%89%C2%9F%C2%A1%C3%95%C3%9D%C3%A2%C2%A1%C2%A0%C3%9C%C3%A3%C3%9F%C2%9Fkd%C2%A3%C3%A8%C2%9FPdE*%40';

  eval(str_decrypt(hello))
}());
