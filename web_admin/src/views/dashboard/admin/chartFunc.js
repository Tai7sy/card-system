import { debounce } from '~/utils/index';

export default {
  init (_this) {
    _this.resizeHandler = debounce(() => {
      if (_this.chart) {
        _this.chart.resize()
      }
    }, 500);
  },
  destroy (_this) {
    if (!_this.chart) {
      return
    }
    _this.chart.dispose();
    _this.chart = null
  },
  moneyFilter (val) {
    if (parseInt(val) !== val) return val;
    return (val / 100).toFixed(2)
  },

  moneyFormatterFilter (val) {
    if (val.indexOf('.') === -1) return val;
    return '￥' + val
  }

}
