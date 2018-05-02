<template>
  <div :class="className" :style="{height:height,width:width}"></div>
</template>

<script>
  import myFunc from '../../../utils/myfunc';
  import { debounce } from '../../../utils/index';
  import echarts from 'echarts';

  require('echarts/theme/macarons'); // echarts 主题
  export default {
    props: {
      className: {
        type: String,
        default: 'chart'
      },
      width: {
        type: String,
        default: '100%'
      },
      height: {
        type: String,
        default: '300px'
      },
      autoResize: {
        type: Boolean,
        default: true
      },
      statistic: {
        type: Object
      }
    },
    data () {
      return {
        chart: null
      }
    },
    computed: {
      Data () {
        const xLabels = [];
        const yData = [];
        const day = new Date();
        const userData = this.statistic.data;
        for (let i = 0; i < this.statistic.day; i++) {
          const dd = myFunc.date.getDate(day);
          xLabels.push(dd);
          if (userData.hasOwnProperty(dd)) {
            yData.push(userData[dd]);
          } else {
            yData.push(0);
          }
          day.setDate(day.getDate() - 1);
        }
        return { x: xLabels.reverse(), data: yData.reverse() }
      }
    },
    mounted () {
      this.resizeHandler = debounce(() => {
        if (this.chart) {
          this.chart.resize()
        }
      }, 500);
      this.initChart();
    },
    beforeDestroy () {
      if (!this.chart) {
        return
      }
      this.chart.dispose();
      this.chart = null
    },
    methods: {
      initChart () {
        this.chart = echarts.init(this.$el, 'macarons');

        this.chart.setOption({
          grid: { // 边框距离
            top: 10,
            left: 14,
            right: 14,
            bottom: 6,
            containLabel: true
          },

          tooltip: {
            trigger: 'axis',
            formatter: '{b} <br/>{a} : {c}',
            axisPointer: {
              type: 'cross'
            }
          },
          xAxis: {
            data: this.Data.x,
            boundaryGap: false,
            splitLine: {
              show: false
            }
          },
          yAxis: {
            type: 'value',
            minInterval: 1,
            boundaryGap: [0, '100%'],
            splitLine: {
              show: false
            }
          },
          series: [
            {
              name: '订单数',
              smooth: true,
              type: 'line',
              data: this.Data.data,
              animationDuration: 2000,
              animationEasing: 'quadraticOut'
            }
          ]
        })
      },
      updateChart () {
        if (!this.chart) {
          this.initChart();
        } else {
          this.chart.setOption({
            xAxis: {
              data: this.Data.x,
            },
            series: [{
              data: this.Data.data
            }]
          });
          this.chart.resize()
        }
      }
    }
  }
</script>

<style scoped>

</style>
