<template>
  <div :class="className" :style="{height:height,width:width}"></div>
</template>

<script>
  import echarts from 'echarts';
  import { date } from 'utils/helper'

  require('echarts/theme/macarons'); // echarts 主题
  import chartFunc from './chartFunc'
  import { debounce } from 'utils';

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
        default: '240px'
      },
      autoResize: {
        type: Boolean,
        default: true
      },
      Statistic: {
        type: Object
      }
    },
    data () {
      return {
        chart: null
      };
    },
    computed: {
      ChartData () {
        const xLabels = [];
        const yData = [];
        const day = new Date();
        const userData = this.Statistic.data;
        for (let i = 0; i < this.Statistic.day; i++) {
          const dd = date.getDate(day);
          xLabels.push(dd);
          if (userData.hasOwnProperty(dd)) {
            yData.push(userData[dd][0]);
          } else {
            yData.push(0);
          }
          day.setDate(day.getDate() - 1);
        }
        return { x: xLabels.reverse(), data: yData.reverse() }
      }
    },
    mounted () {
      chartFunc.init(this);
    },
    beforeDestroy () {
      chartFunc.destroy(this);
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
            data: this.ChartData.x,
            boundaryGap: false,
            splitLine: {
              show: false
            }
          },
          yAxis: {
            type: 'value',
            minInterval: 1,
            boundaryGap: [0, '10%'],
            splitLine: {
              show: false
            },
            axisPointer: {
              snap: true
            }
          },
          series: [
            {
              name: '订单数',
              smooth: true,
              type: 'line',
              itemStyle: {
                normal: {
                  color: 'rgba(2, 197, 233, 0.2)',
                  lineStyle: {
                    color: 'rgba(2, 197, 233, 0.2)'
                  },
                  areaStyle: {
                    color: 'rgba(99,194,255, 0.6)'
                  }
                }
              },
              data: this.ChartData.data,
              animationDuration: 1000,
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
              data: this.ChartData.x,
            },
            series: [{
              data: this.ChartData.data
            }]
          });
          this.chart.resize()
        }
      }
    }
  }
</script>
