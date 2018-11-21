<template>
  <div class="app-container" ref="container">
    <p>欢迎回来，{{userInfo.name}}</p>

    <el-card class="my-card home-stats">

      <span class="info">账户余额：{{userInfo.m_balance | moneyFilter | moneyFormatterFilter}}</span>
      <span class="info">总计收入：{{userInfo.m_all | moneyFilter | moneyFormatterFilter}}</span>

      <br>

      <span class="info">今日订单：&nbsp;{{dashboard.today.count}}</span>

      <span class="info">今日收入：<span
        class="black-text">{{dashboard.today.income | moneyFilter | moneyFormatterFilter}}</span>
      </span>

      <span class="info">今日利润：<span
        class="red-text">{{dashboard.today.profit | moneyFilter | moneyFormatterFilter}}</span>
      </span>

      <span class="info">昨日订单：&nbsp;{{dashboard.yesterday.count}}</span>

      <span class="info">昨日收入：<span class="black-text">{{dashboard.yesterday.income | moneyFilter | moneyFormatterFilter}}</span>
      </span>

      <span class="info">昨日利润：<span
        class="red-text">{{dashboard.yesterday.profit | moneyFilter | moneyFormatterFilter}}</span>
      </span>

      <span v-if="false" style="line-height: 28px;float: right;">
        <a class="icon-btn" @click="reloadDashBoard">
          <icon name="refresh"/>
        </a>
      </span>
      <div class="clearfix"></div>
    </el-card>


    <el-alert class="my-card todo-overview" v-if="dashboard.need_ship_count"
              title="未发货订单通知"
              type="warning">
      <div>
        <span class="info">目前您有&nbsp;<span class="red-text">{{dashboard.need_ship_count}}</span>&nbsp;个订单由于库存不足没有发货，&nbsp;</span>
        <router-link class="info" to="/admin/order/list?status=paid" style="color: #409EFF;">去处理
        </router-link>
      </div>
    </el-alert>

    <el-card class="my-card card-mobile-padding">
      <div slot="header">
        <span style="line-height: 28px">订单数目统计</span>&nbsp;
        <el-select v-model="orderStat.day" placeholder="请选择" value=""
                   size="mini" style="width:90px" @change="getOrderStat">
          <el-option label="7天" :value="7"/>
          <el-option label="15天" :value="15"/>
          <el-option label="30天" :value="30"/>
        </el-select>
        <a class="icon-btn" style="line-height: 28px;float: right;" @click="getOrderStat">
          <icon name="refresh"/>
        </a>
      </div>
      <order-count-line-chart ref="order-count-line-chart" :Statistic="orderStat"></order-count-line-chart>
    </el-card>


    <el-card class="my-card card-mobile-padding">
      <div slot="header">
        <span style="line-height: 28px">订单金额统计</span>&nbsp;
        <el-select v-model="orderStat.day" placeholder="请选择" value=""
                   size="mini" style="width:90px" @change="getOrderStat">
          <el-option label="7天" :value="7"/>
          <el-option label="15天" :value="15"/>
          <el-option label="30天" :value="30"/>
        </el-select>
        <a class="icon-btn" style="line-height: 28px;float: right;" @click="getOrderStat">
          <icon name="refresh"/>
        </a>
      </div>
      <order-sum-line-chart ref="order-sum-line-chart" :Statistic="orderStat"></order-sum-line-chart>
    </el-card>

    <el-card class="my-card card-mobile-padding">
      <div slot="header">
        <span style="line-height: 28px">支付方式</span>&nbsp;
        <el-select v-model="payStat.day" placeholder="请选择" value=""
                   size="mini" style="width:90px" @change="getPayStat">
          <el-option label="今日" :value="1"/>
          <el-option label="7天" :value="7"/>
          <el-option label="15天" :value="15"/>
          <el-option label="30天" :value="30"/>
        </el-select>
        <a class="icon-btn" style="line-height: 28px;float: right;" @click="getPayStat">
          <icon name="refresh"/>
        </a>
      </div>
      <pay-pie-chart ref="pay-pie-chart" :Statistic="payStat"></pay-pie-chart>
    </el-card>


  </div>
</template>

<script type="text/babel">
  import NProgress from 'nprogress'; // Progress 进度条
  import { mapGetters } from 'vuex';
  import { dashboard } from 'api/dashboard'
  import { stat as pay_stat } from 'api/pay'
  import { stat as order_stat } from 'api/order'

  import orderCountLineChart from './orderCountLineChart.vue'
  import orderSumLineChart from './orderSumLineChart.vue'
  import payPieChart from './payPieChart.vue'

  export default {
    components: {
      orderCountLineChart,
      orderSumLineChart,
      payPieChart
    },
    data () {
      return {
        processBar: {
          load: 0,
          need: 0
        },
        dashboard: {
          today: {
            count: 0,
            income: 0,
            profit: 0
          },
          yesterday: {
            count: 0,
            income: 0,
            profit: 0
          },
          complaints_count: 0,
          complaints_24hours: 0,
          need_ship_count: 0,
        },
        orderStat: {
          day: 7,
          data: []
        },
        payStat: {
          day: 7,
          data: {} // {way:[count,sum]}
        }
      }
    },
    computed: {
      ...mapGetters([
        'userInfo'
      ])
    },
    watch: {
      processBar: {
        handler (val) {
          if (val.load < val.need) {
            if (NProgress.isStarted()) {
              NProgress.inc();
            } else {
              NProgress.start();
            }
          } else {
            NProgress.done();
          }
        },
        // 深度观察
        deep: true
      }
    },
    mounted () {
      window.bus.$on('app-resize', this.resizeHandler);
      this.initData();
    },
    methods: {
      resizeHandler () {
        ['order-count-line-chart', 'order-sum-line-chart', 'pay-pie-chart'].forEach(e => {
          if (this.$refs[e]) this.$refs[e].resizeHandler();
        });
      },
      initData () {
        this.processBar.need++;
        dashboard().then(res => {
          Object.assign(this.dashboard, res.data);
          this.$nextTick(() => { // wait for the data trans to children
            this.getOrderStat();
            this.getPayStat();
          });
          this.processBar.load++;
        });
      },
      reloadDashBoard () {
        this.processBar.need++;
        dashboard().then(res => {
          Object.assign(this.dashboard, res.data);
          this.processBar.load++;
        });
      },
      getOrderStat () {
        this.processBar.need++;
        order_stat({ day: this.orderStat.day }).then(res => {
          this.orderStat.data = res.data;
          this.$nextTick(() => { // wait for the data trans to children
            if (this.$refs['order-count-line-chart']) this.$refs['order-count-line-chart'].updateChart();
            if (this.$refs['order-sum-line-chart']) this.$refs['order-sum-line-chart'].updateChart();
          });
          this.processBar.load++;
        })
      },
      getPayStat () {
        this.processBar.need++;
        pay_stat({ day: this.payStat.day }).then(res => {
          this.payStat.data = res.data;
          this.$nextTick(() => { // wait for the data trans to children
            if (this.$refs['pay-pie-chart']) this.$refs['pay-pie-chart'].updateChart();
          });
          this.processBar.load++;
        })
      }
    }
  }
</script>

<style lang="scss" scoped>
  p {
    margin: 0 0 10px;
  }

  .home-stats {
    font-size: 14px;
    .info {
      display: inline-block;
      white-space: nowrap;
      width: 140px;
    }
  }

  .todo-overview {
    font-size: 14px;
    .info {
      display: inline-block;
    }
  }

  .user-title {
    line-height: 22px;
    display: inline-block;
    width: 80px;
  }

  .user-val {
    line-height: 22px;
  }
</style>

