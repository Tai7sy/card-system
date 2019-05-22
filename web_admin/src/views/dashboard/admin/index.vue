<template>
  <div class="app-container" ref="container">

    <el-row type="flex" class="home-row row-reverse">
      <el-col :span="24" v-if="dashboard.complaints_count" class="home-notice-2">
        <el-alert class="my-card todo-overview" title="投诉通知" type="warning">
          <div>
            <span class="info">目前您有&nbsp;<span class="red-text">{{dashboard.complaints_count}}</span>&nbsp;条投诉待处理，&nbsp;</span>
            <span class="info">其中&nbsp;<span class="red-text">{{dashboard.complaints_24hours}}</span>&nbsp;条已超过24小时&nbsp;</span>
            <router-link class="info" to="/m/shop/complaint" style="color: #409EFF;">去处理</router-link>
          </div>
        </el-alert>
      </el-col>
      <el-col :span="24" v-if="dashboard.need_ship_count" class="home-notice-2">
        <el-alert class="my-card todo-overview" title="未发货订单通知" type="warning">
          <div>
            <span class="info">目前您有&nbsp;<span class="red-text">{{dashboard.need_ship_count}}</span>&nbsp;个订单由于库存不足没有发货，&nbsp;</span>
            <router-link class="info" to="/m/order/list?status=paid" style="color: #409EFF;">去处理</router-link>
          </div>
        </el-alert>
      </el-col>
      <el-col :xl="6" :lg="12" class="col-home-right">
        <el-row type="flex" class="home-row">
          <el-col :lg="24" :md="12" :xs="24" class="col-home-account">
            <home-account class="home-account" :userInfo="userInfo" :loginInfo="dashboard.login"/>
          </el-col>
          <el-col :lg="24" :md="12" :xs="24" class="col-home-link">
            <home-link class="home-link"/>
          </el-col>
        </el-row>
      </el-col>
      <el-col :xl="18" :lg="12" class="col-home-left">
        <el-row type="flex" class="home-row">
          <el-col :span="24" v-if="dashboard.complaints_count" class="home-notice-1">
            <el-alert class="my-card todo-overview" title="投诉通知" type="warning">
              <div>
                <span class="info">目前您有&nbsp;<span class="red-text">{{dashboard.complaints_count}}</span>&nbsp;条投诉待处理，&nbsp;</span>
                <span class="info">其中&nbsp;<span class="red-text">{{dashboard.complaints_24hours}}</span>&nbsp;条已超过24小时&nbsp;</span>
                <router-link class="info" to="/m/shop/complaint" style="color: #409EFF;">去处理</router-link>
              </div>
            </el-alert>
          </el-col>
          <el-col :span="24" v-if="dashboard.need_ship_count" class="home-notice-1">
            <el-alert class="my-card todo-overview" title="未发货订单通知" type="warning">
              <div>
                <span class="info">目前您有&nbsp;<span class="red-text">{{dashboard.need_ship_count}}</span>&nbsp;个订单由于库存不足没有发货，&nbsp;</span>
                <router-link class="info" to="/m/order/list?status=paid" style="color: #409EFF;">去处理</router-link>
              </div>
            </el-alert>
          </el-col>

          <el-col :md="12" :xs="24" class="col-home-left-chart">
            <home-order-chart ref="order-chart" :data="orderStat" :userInfo="userInfo" :dashboard="dashboard"
                              style="height: 308px">
              <el-select slot="header" class="order-chart-day"
                         v-model="orderStat.day" placeholder="请选择" value=""
                         @change="getOrderStat">
                <el-option label="7天" :value="7"/>
                <el-option label="15天" :value="15"/>
                <el-option label="1个月" :value="30"/>
              </el-select>
            </home-order-chart>
          </el-col>

          <el-col :span="24">
            <el-card class="my-card card-mobile-padding">
              <div slot="header">
                <span style="line-height: 28px">支付方式</span>&nbsp;
                <el-select v-model="payStat.day" placeholder="请选择" value=""
                           size="mini" style="width:90px" @change="getPayStat">
                  <el-option label="今日" :value="1"/>
                  <el-option label="7天" :value="7"/>
                  <el-option label="15天" :value="15"/>
                  <el-option label="1个月" :value="30"/>
                </el-select>
                <a class="icon-btn" style="line-height: 28px;float: right;" @click="getPayStat">
                  <icon name="light/sync"/>
                </a>
              </div>
              <pay-pie-chart ref="pay-pie-chart" :Statistic="payStat"></pay-pie-chart>
            </el-card>
          </el-col>
        </el-row>
      </el-col>

    </el-row>

  </div>
</template>

<script>
  import NProgress from 'nprogress';
  import { mapGetters } from 'vuex';
  import { dashboard } from '~/api/dashboard'
  import { stat as pay_stat } from '~/api/pay_user'
  import { stat as order_stat } from '~/api/order'

  import payPieChart from '~merchant/views/dashboard/admin/payPieChart'
  import HomeOrderChart from "~merchant/views/dashboard/admin/home-order-chart";

  import HomeAccount from "~merchant/views/dashboard/admin/home-account";
  import HomeLink from "@/views/dashboard/admin/home-link";

  export default {
    components: {
      HomeLink,
      HomeOrderChart,
      HomeAccount,
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
          login: {
            created_at: '加载中',
            ip: '加载中'
          }
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
    beforeDestroy () {
      window.bus.$off('app-resize', this.resizeHandler);
    },
    methods: {
      resizeHandler () {
        ['order-chart','pay-pie-chart'].forEach(e => {
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
            if (this.$refs['order-chart']) this.$refs['order-chart'].updateChart();
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
<style lang="scss">
  .order-chart-day {
    position: absolute;
    top: -1px;
    right: -1px;
    width: 90px;
    z-index: 1;

    input {
      border: none;
      background: transparent;
    }
  }
</style>
<style lang="scss" scoped>

  .home-row {
    display: flex;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;

    &:last-child {
      margin-bottom: 0;
    }

    .my-card {
      margin: 0 0 20px;
    }
  }


  .row-reverse {
    flex-direction: row-reverse;
  }

  .col-home-right {
    width: 324px;
    margin-left: 18px;
  }

  .col-home-left {
    flex-grow: 1;
  }

  .col-home-balance {
    width: 324px;
  }

  .col-home-left-chart {
    flex-grow: 1;
    margin-left: 18px;
  }

  .col-home-account, .col-home-link {
    width: 324px;
  }

  .home-notice-2 {
    .my-card {
      margin: 0 0 20px;
    }
  }

  .home-notice-1 {
    display: flex;
  }

  .home-notice-2 {
    display: none;
  }

  @media only screen and (max-width: 1199px) {
    .home-notice-1 {
      display: none;
    }

    .home-notice-2 {
      display: flex;
    }

    .row-reverse {
      flex-direction: row;
    }
    .col-home-left, .col-home-right {
      margin: 0;
      width: 100%;
      flex: none;
    }

    .col-home-link {
      width: auto;
      flex: 1 1 0;
      margin-left: 18px;
    }
    .home-link {
      height: 282px;
    }

    .col-home-left-chart {
      flex: 1 1 0;
    }
  }

  @media only screen and (max-width: 767px) {

  }

  @media only screen and (max-width: 836px) {
    .col-home-account, .col-home-link, .col-home-balance, .col-home-left-chart {
      width: 100%;
      flex: initial;
      margin: 0;
    }

    .col-home-right {
      margin: 0;
    }
  }


  .todo-overview {
    font-size: 14px;

    .info {
      display: inline-block;
    }
  }
</style>

