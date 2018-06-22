<template>
  <div :class="className" :style="{height:height,'max-width':width}"></div>
</template>

<script>
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
        default: '360px'
      },
      height: {
        type: String,
        default: '120px'
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
          if (Data.hasOwnProperty(way)) {
            xLabels.push(way);
            countData.push({ name: way, value: Data[way][0] });
            sumData.push({ name: way, value: this.moneyFilter(Data[way][1]) });
          }
          if (++i >= limit) break;
        }
        return { xLabels, sumData, countData }
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
      moneyFilter (val) {
        if (parseInt(val) !== val) return val;
        return (val / 100).toFixed(2)
      },
      initChart () {
        this.chart = echarts.init(this.$el, 'macarons');

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

        // console.log(this.pieData);
      }
    }
  }
</script>
