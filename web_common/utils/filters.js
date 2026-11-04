import Vue from 'vue'

/**
 * 合法的分变成元
 * @param val
 * @returns {string}
 */
export function moneyFilter (val) {
  if (val === null || val === undefined) return 0;
  if (parseInt(val) !== val) return val;
  return (val / 100).toFixed(2);
}

/**
 * 给合法的金额加上 ￥
 * @param val
 * @returns {string}
 */
export function moneyFormatterFilter (val) {
  if (val === null || val === undefined) return '￥0.00';
  if ((val + '').indexOf('.') === -1) return val;
  return '￥' + val
}

/**
 * 给金额加上小数点
 * @param {string} val 1000.00
 * @returns {string}
 */
export function moneyDigitalFilter (val) {
  if (val === null || val === undefined) return '0.00';
  if ((val + '').indexOf('.') === -1) return val;
  return val.replace(/\d(?=(?:\d{3})+\b)/g, '$&,');
}

Vue.filter('moneyFilter', moneyFilter);

Vue.filter('moneyFormatterFilter', moneyFormatterFilter);

Vue.filter('moneyDigitalFilter', moneyDigitalFilter);

Vue.filter('formatDate', val => (new Date(val)).format('yyyy-MM-dd'));

