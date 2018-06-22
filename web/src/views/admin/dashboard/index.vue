<template>
  <div class="content-wrapper">
    <div>
      <h3 style="margin-top: 0">金额统计</h3>
      <div v-if="dashboard.income">
        <span class="text-nowrap">今日收入: <span
          class="green-text"><b>{{dashboard.income.today | moneyFilter | moneyFormatterFilter}}</b></span></span>&nbsp;&nbsp;
        <span class="text-nowrap">本月收入: <span
          class="red-text"><b>{{dashboard.income.month | moneyFilter | moneyFormatterFilter}}</b></span></span>&nbsp;&nbsp;
        <span class="text-nowrap">今年收入: <span
          class="black-text"><b>{{dashboard.income.year | moneyFilter | moneyFormatterFilter}}</b></span></span>
      </div>
    </div>


    <div style="margin-top: 48px">
      <h3>订单趋势</h3>
    </div>
    <mu-dialog :open="!!process" title="提示">
      <mu-circular-progress :size="40" style="vertical-align:middle;"/>
      <span v-html="process"
            style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
    </mu-dialog>
    <order-chart v-if="dashboard.order" ref="order-chart" :statistic="dashboard.order"></order-chart>
    <div style="margin-top: 48px">
      <h3>支付方式</h3>
    </div>
    <pay-chart v-if="dashboard.pay" ref="pay-chart" :statistic="dashboard.pay"></pay-chart>
  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'
  import orderChart from './order-linechart'
  import payChart from './pay-piechart'

  export default {
    components: { orderChart, payChart },
    data () {
      return {
        process: false,
        dashboard: {}
      }
    },
    mounted () {
      this.checkLogin();
      this.load();
      window.addEventListener('resize', this.resizeHandler);
    },
    beforeDestroy () {
      window.removeEventListener('resize', this.resizeHandler);
      if (!this.chart) {
        return
      }
      this.chart.dispose();
      this.chart = null
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login');
        }
      },
      resizeHandler () {
        if (this.$refs['order-chart'] && this.$refs['order-chart'].resizeHandler) {
          this.$refs['order-chart'].resizeHandler();
          this.$refs['pay-chart'].resizeHandler();
        }
      },
      load () {
        this.process = '加载中...'
        this.$http.post('admin/dashboard', {
          day: 30
        }).then(e => {
          this.dashboard = e.data
          this.updateView();
          this.process = false
        }).catch(() => {
          this.process = false
        });
      },
      updateView () {
        if (this.$refs['order-chart'] && this.$refs['order-chart'].updateChart) {
          this.$refs['order-chart'].updateChart();
        }
        if (this.$refs['pay-chart'] && this.$refs['pay-chart'].updateChart) {
          this.$refs['pay-chart'].updateChart();
        }
      }
    }
  }
</script>

<style lang="less" scoped>
  .text-nowrap {
    white-space: nowrap
  }
</style>
