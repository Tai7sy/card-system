<template>
  <div :class="className" :style="{height:height}"></div>
</template>

<script>
  // 引入 ECharts 主模块
  import echarts from 'echarts/lib/echarts';
  // 引入 line图
  import 'echarts/lib/chart/line';
  // 引入提示框和标题组件
  import 'echarts/lib/component/tooltip';
  import 'echarts/lib/component/title';
  import 'echarts/lib/component/legend';

  import chartFunc from './chartFunc'

  export default {
    name: 'OrderLineChart',
    props: {
      className: {
        type: String,
        default: 'chart'
      },
      height: {
        type: String,
        default: '240px'
      },
      autoResize: {
        type: Boolean,
        default: true
      },
      data: {
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
        const number = [];
        const paid = [];
        const profit = [];
        const stat = this.data.data;

        let start = new Date();
        if (this.data.day === 30) {
          start.setMonth(start.getMonth() - 1);
        } else {
          start.setDate(start.getDate() - this.data.day + 1);
        }

        for (; start <= Date.now(); start.setDate(start.getDate() + 1)) {
          const index = start.format('yyyy-MM-dd');
          xLabels.push(start.format('MM-dd'));
          if (stat && Object.prototype.hasOwnProperty.call(stat, index)) {
            number.push(stat[index][0]);
            paid.push(chartFunc.moneyFilter(stat[index][1]));
            profit.push(chartFunc.moneyFilter(stat[index][2]));
          } else {
            number.push(0);
            paid.push(chartFunc.moneyFilter(0));
            profit.push(chartFunc.moneyFilter(0));
          }
        }
        return { x: xLabels, number, paid, profit }
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
          title: {
            text: '最近' + (this.data.day === 30 ? '1个月' : this.data.day + '日') + '趋势',
            textStyle: {
              fontSize: 12,
              fontWeight: 'light'
            }
          },
          grid: { // 边框距离
            top: 0,
            left: 0,
            right: 16,
            bottom: 0,
            containLabel: true
          },
          legend: {
            left: 88,
            data: [
              {
                name: '成交笔数 (笔)',
                icon: 'rect',
                textStyle: {
                  fontSize: 12,
                  fontWeight: 'light'
                }
              },
              {
                name: '成交金额 (元)',
                icon: 'rect',
                textStyle: {
                  fontSize: 12,
                  fontWeight: 'light'
                }
              },
              {
                name: '利润金额 (元)',
                icon: 'rect',
                textStyle: {
                  fontSize: 12,
                  fontWeight: 'light'
                }
              }
            ],
            selectedMode: 'single'
          },
          tooltip: {
            trigger: 'axis',
            backgroundColor: 'hsla(0,0%,100%,.9)',
            extraCssText: 'box-shadow: 0 4px 16px 0 rgba(97,131,155,.15);padding: 10px 10px 6px;',
            formatter: '<span class="charts-tooltip-name">{a}</span><br/><span class="charts-tooltip-value">{c}</span><br/><span class="charts-tooltip-title">{b}</span>',
            axisPointer: {
              type: 'line'
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
            show: false,
            splitLine: {
              show: true
            }
          },
          series: [
            {
              name: '成交笔数 (笔)',
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
              data: this.ChartData.number,
              animationDuration: 1000,
              animationEasing: 'quadraticOut'
            },
            {
              name: '成交金额 (元)',
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
              data: this.ChartData.paid,
              animationDuration: 1000,
              animationEasing: 'quadraticOut'
            },
            {
              name: '利润金额 (元)',
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
              data: this.ChartData.profit,
              animationDuration: 1000,
              animationEasing: 'quadraticOut'
            }
          ]
        }, true)
      },
      updateChart () {
        if (!this.chart) {
          this.initChart();
        } else {
          this.chart.setOption({
            title: {
              text: '最近' + (this.data.day === 30 ? '1个月' : this.data.day + '日') + '趋势',
            },
            xAxis: {
              data: this.ChartData.x,
            },
            series: [
              {
                data: this.ChartData.number
              }, {
                data: this.ChartData.paid
              }, {
                data: this.ChartData.profit
              }
            ]
          });
          this.chart.resize()
        }
      }
    }
  }
</script>

<style lang="scss">
  .charts-tooltip-name {
    font-size: 14px;
    color: #8b93a7;
  }

  .charts-tooltip-value {
    color: #333;
    font-size: 18px;
    line-height: 30px;
    vertical-align: text-bottom;
  }

  .charts-tooltip-title {
    color: #c8d0dc;
    font-size: 12px;
  }
</style>
