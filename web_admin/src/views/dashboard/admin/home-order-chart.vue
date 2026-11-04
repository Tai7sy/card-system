<template>
  <el-card class="my-card home-report">
    <slot name="header"/>
    <el-tabs type="border-card" v-model="activeName">
      <el-tab-pane name="order">
        <span slot="label"><i class="el-icon-s-data"></i> 今日交易</span>
        <div class="body-amount">
          <div class="body-balance" style="flex: 0.7;">
            <div class="amount-title">成交 (笔)</div>
            <div class="balance-money">{{dashboard.today.count}}</div>
          </div>
          <div class="body-balance">
            <div class="amount-title">成交 (元)</div>
            <div class="balance-money">{{dashboard.today.paid | moneyFilter}}</div>
          </div>
          <div class="body-balance">
            <div class="amount-title">利润 (元)</div>
            <div class="balance-money">{{dashboard.today.profit | moneyFilter}}</div>
          </div>
        </div>
      </el-tab-pane>
      <el-tab-pane name="income" label="昨日">
        <div class="body-amount">
          <div class="body-balance" style="flex: 0.7;">
            <div class="amount-title">成交 (笔)</div>
            <div class="balance-money">{{dashboard.yesterday.count}}</div>
          </div>
          <div class="body-balance">
            <div class="amount-title">成交 (元)</div>
            <div class="balance-money">{{dashboard.yesterday.paid | moneyFilter}}</div>
          </div>
          <div class="body-balance">
            <div class="amount-title">利润 (元)</div>
            <div class="balance-money">{{dashboard.yesterday.profit | moneyFilter}}</div>
          </div>
        </div>
      </el-tab-pane>
    </el-tabs>
    <div style="margin: 0 8px 8px 0" >
      <order-line-chart ref="chart" :data="data" height="170px"/>
    </div>
  </el-card>
</template>

<script>
  import OrderLineChart from "./orderLineChart";

  export default {
    name: 'HomeOrderChart',
    components: { OrderLineChart },
    props: {
      userInfo: {},
      data: {},
      dashboard: {},
    },
    data () {
      return {
        activeName: 'order'
      }
    },
    mounted () {
    },
    methods: {
      resizeHandler () {
        this.$refs.chart.resizeHandler();
      },
      updateChart () {
        this.$refs.chart.updateChart();
      }
    }
  }
</script>

<style lang="scss">
  .home-report {
    position: relative;

    .el-card__body {
      padding: 0 !important;
    }

    .el-tabs {
      box-shadow: none !important;
      border: none !important;
    }

    .body-amount {
      display: flex;
      -webkit-box-pack: justify;
      -ms-flex-pack: justify;
      justify-content: space-between;
      padding-bottom: 4px;

      & > .body-balance {
        flex: 1;

        & > .amount-title {
          font-size: 14px;
          color: #8b93a7;
          text-align: left;
          height: 20px;
          line-height: 20px;
          display: -webkit-box;
          display: -webkit-flex;
          display: -ms-flexbox;
          display: flex;
          align-items: center;
        }

        & > .balance-money {
          color: rgb(51, 51, 51);
          font-size: 24px;
          font-family: Microsoft Yahei UI Light, sans-serif;
          font-weight: bold;
        }
      }
    }
  }
</style>
