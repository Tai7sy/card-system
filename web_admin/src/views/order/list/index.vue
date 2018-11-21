<template>
  <div :class="{'app-container':!userInfo}" ref="container">
    <!--工具条-->
    <el-col :span="24" class="no-bottom-form" style="padding-bottom: 0px;">
      <el-form :inline="true" class="list-pre-table">

        <el-form-item class="mobile-full-width" v-if="!onlyProfit">
          <el-select v-model="where.category_id" size="small" class="mobile-full-width"
                     popper-class="el-select-group-less-padding"
                     @change="changeCategory" no-data-text="无分类数据">
            <el-option-group label="API订单">
              <el-option label="API订单" :value="API_ORDER_ID"/>
            </el-option-group>
            <el-option-group label="卡密订单">
              <el-option label="卡密订单" :value="-1"/>
              <el-option v-for="item in categories"
                         :key="item.id" :label="item.name" :value="item.id"/>
            </el-option-group>
          </el-select>
          <el-select v-if="where.category_id > 0" v-model="where.product_id" size="small" class="mobile-full-width"
                     @change="getList">
            <el-option label="全部" :value="-1"/>
            <el-option v-for="item in products" :value="item.id" :key="item.id" :label="item.name"/>
          </el-select>
        </el-form-item>

        <el-form-item class="mobile-full-width">
          <el-input placeholder="请输入" v-model="search.value" size="small">
            <el-select v-model="search.type" slot="prepend" placeholder="搜索类型" value=""
                       size="small" style="width:90px">
              <el-option label="订单号" value="order_no"/>
              <el-option label="支付渠道单号" value="pay_trade_no"/>
              <el-option v-if="isApiOrder" label="商户订单号(API)" value="api_out_no"/>
              <el-option v-if="isApiOrder" label="附加信息(API)" value="api_info"/>
              <el-option v-if="_project === 'admin'" label="商户ID" value="user_id"/>
              <el-option v-if="!isApiOrder && !onlyProfit" label="买家联系方式" value="email"/>
            </el-select>
          </el-input>
        </el-form-item>

        <el-form-item class="mobile-full-width" v-if="!recentDay">
          <el-date-picker size="small" v-model="date_range" type="daterange" align="right"
                          placeholder="日期范围" :picker-options="pickerOptions">
          </el-date-picker>
        </el-form-item>
        <el-form-item class="fix-height">
          <el-button @click="handleSearch" size="small">搜索</el-button>
        </el-form-item>


        <div class="mobile-full-width"></div>

        <el-form-item class="fix-height" v-if="!onlyProfit">
          <el-switch v-model="where.showUnPay"
                     inactive-text="未支付订单"
                     :active-value="1"
                     :inactive-value="0"
                     @change="handleShowUnPay">
          </el-switch>
        </el-form-item>

        <el-form-item class="fix-height" v-if="!onlyProfit && !isApiOrder">
          <el-switch v-model="where.showUnSend"
                     inactive-text="待发货订单"
                     :active-value="1"
                     :inactive-value="0"
                     @change="handleShowUnSend">
          </el-switch>
        </el-form-item>

        <!--el-form-item style="float: right;margin-right: 0;">
          <a class="icon-btn" @click="handleApply">
            <icon name="jpy"/>&nbsp;申请提现</a>
        </el-form-item-->
      </el-form>
    </el-col>
    <el-table v-if="!destroyForLoad" :data="list.data" v-loading="loading" stripe border class="list-table"
              @filter-change="handleFilterChange"
              @selection-change="val => checkedIds = val.map(v=>v.id).join(',')">
      <el-table-column type="selection" width="40" v-if="_project === 'admin'"/>
      <el-table-column prop="user_id" label="商户ID" width="70" v-if="_project === 'admin' && !userInfo"/>

      <el-table-column prop="order_no" label="订单号(点击查看)" width="170">
        <template slot-scope="scope">
          <a class="line-btn" @click="showOrder(scope.row)">{{scope.row.order_no}}</a>
        </template>
      </el-table-column>


      <el-table-column v-if="!onlyProfit" prop="status" label="状态" width="80"
                       column-key="status"
                       :filters="statusFilter"
                       :filtered-value="statusFilterValue"
                       filter-placement="bottom">
        <template slot-scope="scope">
          <el-tag :type="orderStatus.get(scope.row.status).type" close-transition>
            {{orderStatus.get(scope.row.status).name}}
          </el-tag>
        </template>
      </el-table-column>


      <el-table-column prop="api_out_no" label="通知" width="80" v-if="isApiOrder">
        <template slot-scope="scope">
          <a class="line-btn" @click="showOrder(scope.row)">
            <el-tag :type="scope.row.api_info && scope.row.api_info.notify_ret==='success' ? 'success' : 'warning'"
                    close-transition>
              {{scope.row.api_info && scope.row.api_info.notify_ret || '未通知'}}
            </el-tag>
          </a>
        </template>
      </el-table-column>


      <el-table-column prop="paid_at" label="支付时间" width="160"/>


      <el-table-column prop="api_out_no" label="商户订单号" width="170" show-overflow-tooltip v-if="isApiOrder"/>

      <el-table-column prop="product" label="商品名称" min-width="150" show-overflow-tooltip v-if="!isApiOrder && !onlyProfit">
        <template slot-scope="scope">
          <span class="red-text">{{scope.row.count}}</span>&nbsp;x&nbsp;
          <span>{{scope.row.product?scope.row.product.name:'未知#'+scope.row.product_id}}</span>
        </template>
      </el-table-column>

      <el-table-column prop="product" label="卡密" width="60" v-if="!isApiOrder && !onlyProfit">
        <template slot-scope="scope">
          <a class="line-btn" @click="showCards(scope.row)" style="color: #409eff">
            {{scope.row.card_orders&&scope.row.card_orders.length?scope.row.card_orders.length+'张':'无'}}
          </a>
        </template>
      </el-table-column>

      <el-table-column prop="product" label="支付方式" min-width="80" show-overflow-tooltip>
        <template slot-scope="scope">
          {{scope.row.pay?scope.row.pay.name:'未知#'+scope.row.pay_id}}
        </template>
      </el-table-column>

      <el-table-column v-if="onlyProfit" prop="cost" label="成本" min-width="80">
        <template slot-scope="scope">
          {{scope.row.cost | moneyFilter}}
        </template>
      </el-table-column>

      <el-table-column v-if="onlyProfit && !isApiOrder" prop="paid" label="售价" min-width="80">
        <template slot-scope="scope">
          {{scope.row.paid | moneyFilter}}
        </template>
      </el-table-column>

      <el-table-column v-if="!onlyProfit" prop="paid" label="金额" min-width="80">
        <template slot-scope="scope">
          <el-tooltip v-if="scope.row.discount > 0" effect="dark" placement="top-start"
                      :content="'商品原价：'+(scope.row.price/ 100).toFixed(2)
                      +'　优惠金额：' + (scope.row.discount/ 100).toFixed(2)">
            <span>{{scope.row.paid | moneyFilter}}</span>
          </el-tooltip>
          <span v-else>
               {{scope.row.paid | moneyFilter}}
          </span>
        </template>
      </el-table-column>

      <!--商户的分成-->
      <el-table-column prop="income" label="分成" min-width="80" v-if="_project === 'merchant' || userInfo">
        <template slot-scope="scope">
          <el-tooltip effect="dark" placement="top-start"
                      :content="'手续费: ' + (scope.row.fee/ 100).toFixed(2)">
            <span>{{scope.row.income | moneyFilter}}</span>
          </el-tooltip>
        </template>
      </el-table-column>

      <!--平台的分成-->
      <el-table-column prop="fee-system_fee" label="平台分成" min-width="80" v-else>
        <template slot-scope="scope">
          <el-tooltip effect="dark" placement="top-start"
                      :content="'通道手续费: ' + (scope.row.system_fee/ 100).toFixed(2)">
            <span>{{(scope.row.fee -  scope.row.system_fee) | moneyFilter}}</span>
          </el-tooltip>
        </template>
      </el-table-column>


      <el-table-column v-if="onlyProfit" prop="profit" label="利润" min-width="80">
        <template slot-scope="scope">
          {{(scope.row.income - scope.row.cost) | moneyFilter}}
        </template>
      </el-table-column>


      <el-table-column v-if="!onlyProfit && !isApiOrder" prop="email" label="买家联系方式" min-width="160"
                       show-overflow-tooltip>
        <template slot-scope="scope">
          <span>{{scope.row.email}}{{scope.row.email_sent?'':''}}</span>
        </template>
      </el-table-column>

      <el-table-column v-if="!onlyProfit && isApiOrder" prop="api_info.attach" label="附加" min-width="160"
                       show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="line-btn" @click="showRemark(scope.row)">
            {{scope.row.api_info ? scope.row.api_info.attach : '暂无'}}
          </a>
        </template>
      </el-table-column>


      <el-table-column v-if="!onlyProfit" prop="remark" label="备注" min-width="50" show-overflow-tooltip>
        <template slot-scope="scope">
          <a class="line-btn" @click="showRemark(scope.row)">{{scope.row.remark || '添加'}}</a>
        </template>
      </el-table-column>


      <el-table-column label="操作" :width="_project === 'admin' ? '80' : '50'" fixed="right">
        <template slot-scope="scope">
          <a class="icon-btn" @click="showOrder(scope.row)" style="font-size: 14px">
            查看
          </a>
          <a class="icon-btn" @click="handleFreeze(scope.row)" style="font-size: 14px"
             v-if="_project === 'admin' && scope.row.status===orderStatus.SUCCESS.id">
            冻结
          </a>
          <a class="icon-btn" @click="handleUnfreeze(scope.row)" style="font-size: 14px"
             v-if="_project === 'admin' && scope.row.status===orderStatus.FROZEN.id">
            解冻
          </a>
          <a class="icon-btn" @click="handleSetPaid(scope.row)" style="font-size: 14px"
             v-if="_project === 'admin' && scope.row.status===orderStatus.UN_PAY.id">
            补单
          </a>
        </template>
      </el-table-column>

    </el-table>


    <el-col :span="24" class='list-after-action' style="padding-top: 8px;text-align: left;" v-if="_project === 'admin'">
      <el-form :inline="true" class="no-bottom-form">
        <el-form-item label="选中">
          <el-button size="small" @click="checkFreeze">冻结</el-button>
          <el-button size="small" @click="checkUnfreeze">解冻</el-button>
        </el-form-item>
      </el-form>
    </el-col>


    <el-col :span="24" class='list-after-action' style="padding-top: 12px;text-align: right;">
      <el-pagination
        v-if='list.total>0'
        class='pagination'
        :page-size="20"
        :page-sizes="[10, 20, 30, 40, 50, 100]"
        :total="list.total"
        :current-page.sync='list.current_page'
        layout="total, sizes, prev, pager, next, jumper"
        @current-change='onChangePage'
        @size-change='onChangePageSize'>
      </el-pagination>
    </el-col>


    <el-col v-if="onlyProfit" :span="24" class='list-after-action' style="padding-top: 12px;">
      <el-card class="my-card">
        <div style="font-size: 14px">
          总收入:&nbsp;{{parseInt(list.profit_sum.income) | moneyFilter | moneyFormatterFilter}}，
          总利润：&nbsp;{{parseInt(list.profit_sum.profit) | moneyFilter | moneyFormatterFilter}}
        </div>
      </el-card>
    </el-col>

    <order-info ref="orderInfo" from="order"/>

  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import { editRemark, freeze, getList as getOrderList, orderStatus, setPaid, unfreeze } from 'api/order'
  import { getList as getCategoryList } from 'api/category'
  import { getList as getProductList } from 'api/product'
  import orderInfo from '../_info'

  const API_ORDER_ID = -1001;

  export default {
    props: {
      userInfo: {
        type: Object,
        default: null
      },
      recentDay: {
        default: false
      },
      onlyProfit: {
        default: false
      },
      _project: {
        default: process.env.PROJECT
      }
    },
    components: { orderInfo },
    data () {
      return {
        isMobile: this.$store.state.app.isMobile,
        loading: false,
        orderStatus,
        API_ORDER_ID,
        search: {
          type: 'order_no',
          value: ''
        },
        date_range: '',
        where: {
          category_id: -1,
          product_id: -1,
          showUnPay: 0,
          showUnSend: 0,
        },
        categories: [{ id: -2, name: '加载中' }],
        products: [{ id: -2, name: '加载中' }],
        list: {
          current_page: 1,
          data: [],
          from: 1,
          last_page: 1,
          next_page_url: null,
          per_page: 20,
          prev_page_url: null,
          to: 0,
          total: 0,
          profit_sum: { income: 0, profit: 0 }
        },
        checkedIds: '',
        statusFilter: (() => {
          const filtered = [];
          for (const key in orderStatus) {
            if (orderStatus.hasOwnProperty(key) && typeof orderStatus[key] === 'object') {
              filtered.push({
                text: orderStatus[key].name,
                value: orderStatus[key].id,
                type: orderStatus[key].type
              })
            }
          }
          return filtered;
        })(),
        statusFilterValue: [],
        pickerOptions: {
          shortcuts: [{
            text: '最近一周',
            onClick (picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近一个月',
            onClick (picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
              picker.$emit('pick', [start, end]);
            }
          }, {
            text: '最近三个月',
            onClick (picker) {
              const end = new Date();
              const start = new Date();
              start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
              picker.$emit('pick', [start, end]);
            }
          }]
        },
        destroyForLoad: false,
        isApiOrder: false,
      }
    },
    watch: {
      loading (val, old) {
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      },
      isApiOrder () {
        this.destroyForLoad = true;
        this.$nextTick(() => {
          this.destroyForLoad = false;
        });
      }
    },
    mounted () {
      if (this.$route.query.type === 'api') {
        this.where.category_id = API_ORDER_ID;
        this.isApiOrder = true;
      }
      if (this._project === 'admin') {
        this.categories = [];
        this._loadOrders();
      } else {
        this.getCategories();
      }
    },
    methods: {
      getCategories () {
        this.loading = true;
        const where = { search: 'simple', val: 'list', };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getCategoryList(where).then(ret => {
          this.categories = ret.data;
          this._loadOrders();
        })
      },
      /**
       * 加载完分类后拉取订单
       * @private
       */
      _loadOrders () {
        if (this.$route.query.status === 'paid') {
          this.where.showUnSend = 1;
          this.handleShowUnSend(1);
        } else if (this.$route.query.status === 'un_pay') {
          this.where.showUnPay = 1;
          this.handleShowUnPay(1);
        } else {
          this.handleShowWithoutUnpay();
        }
      },
      changeCategory (category_id) {
        if (this.isApiOrder) {
          // 现在已经是只显示API订单了
          if (category_id !== API_ORDER_ID) {
            this.isApiOrder = false;
            this.getList();
          } else {
            this.getList();
          }
        } else {
          // 现在是普通模式
          if (category_id === API_ORDER_ID) {
            this.isApiOrder = true;
            this.getList();
          } else {
            this.getProducts(category_id);
          }
        }
      },
      getProducts (category_id) {
        this.getList();
        this.where.product_id = -1;
        this.products = [];
        const where = { search: 'simple', val: category_id };
        if (this.userInfo) {
          where.user_id = this.userInfo.id;
        }
        getProductList(where).then(ret => {
          this.products = ret.data;
        });
      },
      getList () {
        this.loading = true;
        this.where.current_page = this.list.current_page;
        this.where.per_page = this.list.per_page;
        this.where.recent = this.recentDay;
        this.where.profit = this.onlyProfit ? 1 : 0;
        if (this.userInfo) {
          this.where.user_id = this.userInfo.id;
        }
        getOrderList(this.where).then(res => {
          this.list = res.data;
          this.loading = false;
        });
      },
      handleSearch () {
        const where = { search: this.search.type, val: this.search.value };
        if (this.date_range && this.date_range[0] && this.date_range[1]) {
          where.start_at = this.date_range[0].format('yyyy-MM-dd');
          where.end_at = this.date_range[1].format('yyyy-MM-dd');
        } else {
          where.start_at = null;
          where.end_at = null;
        }
        Object.assign(this.where, where);
        this.getList();
      },
      handleShowUnPay (val) {
        if (val) {
          this.where.showUnSend = 0;
          this.statusFilterValue.splice(0, this.statusFilterValue.length);
          this.statusFilterValue.push(orderStatus.UN_PAY.id);
        } else {
          this.handleShowWithoutUnpay();
        }
        this.where.status = this.statusFilterValue.join(',');
        this.handleSearch();
      },
      handleShowUnSend (val) {
        if (val) {
          this.where.showUnPay = 0;
          this.statusFilterValue.splice(0, this.statusFilterValue.length);
          this.statusFilterValue.push(orderStatus.PAID.id);
        } else {
          this.handleShowWithoutUnpay();
        }
        this.where.status = this.statusFilterValue.join(',');
        this.handleSearch();
      },
      handleShowWithoutUnpay () {
        this.statusFilterValue.splice(0, this.statusFilterValue.length, ...[
          orderStatus.FROZEN.id,
          orderStatus.SUCCESS.id,
          orderStatus.PAID.id,
          orderStatus.REFUND.id
        ]);
        // 后端默认就是除去 unpaid 这里不加了
        this.where.status = '';
        this.handleSearch();
      },
      handleFilterChange (obj) {
        const where = {};
        if (obj['status']) {
          where.status = obj['status'].join(',');
        } else {
          where.status = null;
        }
        Object.assign(this.where, where);
        this.getList();
      },
      onChangePage () {
        this.getList();
      },
      onChangePageSize (per_page) {
        this.list.per_page = per_page;
        this.getList();
      },
      showCards (row) {
        if (row.card_orders && row.card_orders.length) {
          this.$msgbox({
            title: '卡密详情',
            message: row.card_orders.map(e => e.card ? e.card.card : '未知卡密#' + e.card_id).join('<br>'),
            dangerouslyUseHTMLString: true
          });
        }
      },
      showRemark (row) {
        // this.$alert(row.remark, '订单备注');
        this.$prompt('可以在这里修改备注', '提示', {
          confirmButtonText: '保存',
          cancelButtonText: '取消',
          inputValue: row.remark,
          inputPattern: /^[\s\S]{0,100}$/,
          inputErrorMessage: '备注需100字符以内',
          inputType: 'textarea'
        }).then(({ value }) => {
          this.loading = true;
          editRemark(row.id, value).then(() => {
            this.loading = false;
            row.remark = value;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      showOrder (row) {
        this.$refs['orderInfo'].showOrder(row);
      },
      handleFreeze (row) {
        this.$prompt(`冻结订单 ${row.order_no} ? <br>请输入与冻结原因`, '提示', {
          dangerouslyUseHTMLString: true,
          confirmButtonText: '保存',
          cancelButtonText: '取消',
          inputValue: row.frozen_reason,
          inputPattern: /^[\s\S]{2,100}$/,
          inputErrorMessage: '冻结原因需2-100字符以内',
          inputType: 'textarea'
        }).then(({ value }) => {
          this.loading = true;
          freeze(row.id, value).then(res => {
            this.loading = false;
            // freeze count
            if (res.data[0] !== 1) {
              return this.$msgbox({ title: '操作失败', message: '只能冻结已成功订单', type: 'error' });
            }
            // successfully unfreeze count
            if (res.data[1] !== 1) {
              return this.$msgbox({ title: '操作失败', message: '冻结订单失败, 内部错误', type: 'error' });
            }
            this.$notify({ title: '操作完成', message: row.order_no + ' 已冻结', type: 'info' });
            row.status = orderStatus.FROZEN.id;
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleUnfreeze (row) {
        this.$confirm('解冻订单 ' + row.order_no + ' ? ', '提示', { type: 'warning' }).then(() => {
          this.loading = true;
          unfreeze(row.id).then(res => {
            this.loading = false;
            // freeze count
            if (res.data[0] !== 1) {
              return this.$msgbox({ title: '操作失败', message: '只能解冻已冻结订单', type: 'error' });
            }
            // successfully unfreeze count
            if (res.data[1] !== 1) {
              return this.$msgbox({ title: '操作失败', message: '解冻订单失败, 内部错误', type: 'error' });
            }
            this.$notify({ title: '操作完成', message: row.order_no + ' 已解冻', type: 'success' });
            row.status = res.data[2];
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      checkFreeze () {
        if (!this.checkedIds.length) return;
        this.$prompt('批量冻结选中订单? (只冻结交易成功订单) <br>请输入与冻结原因', '提示', {
          dangerouslyUseHTMLString: true,
          confirmButtonText: '确认',
          cancelButtonText: '取消',
          inputPattern: /^[\s\S]{2,100}$/,
          inputErrorMessage: '冻结原因需2-100字符以内',
          inputType: 'textarea'
        }).then(({ value }) => {
          this.loading = true;
          freeze(this.checkedIds, value).then(res => {
            this.$notify({ title: '操作完成', message: `总订单: ${res.data[0]} 笔，冻结成功 ${res.data[1]} 笔`, type: 'info' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      checkUnfreeze () {
        if (!this.checkedIds.length) return;
        this.$confirm('批量解冻选中订单? (只处理已冻结订单)', '提示', { type: 'warning' }).then(() => {
          unfreeze(this.checkedIds).then(res => {
            this.$notify({ title: '操作完成', message: `总订单: ${res.data[0]} 笔，解冻成功 ${res.data[1]} 笔`, type: 'success' });
            this.getList();
          }).catch(() => {
            this.loading = false;
          });
        });
      },
      handleSetPaid (row) {
        const payName = row.pay ? row.pay.name : '支付方式#' + row.pay_id;
        this.$prompt(`请仔细确认已经收到款项，<br>确认将订单 ${row.order_no} 设置为已付款并发货? <br>请输入${payName}内单号(流水号)`, '提示', {
          dangerouslyUseHTMLString: true,
          confirmButtonText: '确认',
          cancelButtonText: '取消',
          inputPattern: /^[\s\S]{2,100}$/,
          inputErrorMessage: '单号需2-100字符以内',
          inputType: 'text'
        }).then(({ value }) => {
          this.loading = true;
          setPaid(row.id, value).then(res => {
            const ret = res.data;
            this.loading = false;
            Object.assign(row, ret.order);
            if (ret.code !== 0) {
              return this.$msgbox({ title: '操作失败', message: ret.msg, type: 'error' });
            }
            this.$notify({ title: '操作完成', message: row.order_no + ' 已补单成功', type: 'success' });
          }).catch(() => {
            this.loading = false;
          });
        });
      }
    }
  }
</script>

<style lang="scss">
  .el-select-group__title {
    padding-left: 12px !important;
    line-height: 24px !important;
  }
</style>


