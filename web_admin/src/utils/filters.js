import Vue from 'vue'


Vue.filter('moneyFilter', val => {
  if (val === null || val === undefined) return 0;
  if (parseInt(val) !== val) return val;
  return (val / 100).toFixed(2);
});

Vue.filter('moneyFormatterFilter', val => {
  if (val === null || val === undefined) return '￥0';
  if ((val + '').indexOf('.') === -1) return val;
  return '￥' + val
});

Vue.filter('formatDate', val => (new Date(val)).format('yyyy-MM-dd'));

