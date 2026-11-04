<template>
  <div :class="className" :style="{height:height,width:width}"></div>
</template>

<script>
  // 引入 ECharts 主模块
  import echarts from 'echarts/lib/echarts';
  // 引入 饼图
  import 'echarts/lib/chart/pie';
  // 引入提示框和标题组件
  import 'echarts/lib/component/tooltip';
  import 'echarts/lib/component/title';
  import 'echarts/lib/component/legend';

  import chartFunc from './chartFunc'

  export default {
    name: 'PayPieChart',
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
      Statistic: {}
    },
    data () {
      return {
        chart: null
      };
    },
    computed: {
      // 支付金额
      pieData () {
        const xLabels = [];
        const sumData = [];
        const countData = [];
        const Data = this.Statistic.data;
        const limit = this.Statistic.limit ? this.Statistic.limit : 6;
        let i = 0;
        for (const way in Data) {
          if (Object.prototype.hasOwnProperty.call(Data, way)) {
            xLabels.push(way);
            countData.push({ name: way, value: Data[way][0] });
            sumData.push({ name: way, value: chartFunc.moneyFilter(Data[way][1]) });
          }
          if (++i >= limit) break;
        }
        return { xLabels, sumData, countData }
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
        this.chart = echarts.init(this.$el, 'light');

        this.chart.setOption({
          tooltip: {
            trigger: 'item',
            formatter: '{a} <br/>{b} : {c} ({d}%)'
          },
          legend: {
            orient: 'vertical',
            x: 'left',
            data: this.pieData.xLabels
          },
          calculable: true,
          series: [
            {
              name: '支付金额',
              type: 'pie',
              radius: [0, '40%'],
              label: { normal: { show: false }, emphasis: { show: false } },
              data: this.pieData.sumData
            },
            {
              name: '支付笔数',
              type: 'pie',
              radius: ['70%', '90%'],
              label: { normal: { show: false }, emphasis: { show: false } },
              data: this.pieData.countData
            }
          ]
        })
      },
      updateChart () {
        if (!this.chart) {
          this.initChart();
        } else {
          this.chart.setOption({
            legend: {
              data: this.pieData.xLabels
            },
            series: [
              {
                data: this.pieData.sumData
              },
              {
                data: this.pieData.countData
              }
            ]
          });
          this.chart.resize()
        }

        
      }
    }
  }
</script>
