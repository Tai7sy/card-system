<template>
<div :class="{'app-container':!userInfo}" ref="container">
  <!--工具条-->
  <el-col :span="24" class="no-bottom-form">
    <el-form :inline="true" class="list-pre-table">

      <el-form-item class="mobile-full-width" v-if="!onlyProfit">
        <el-select v-model="where.category_id" filterable size="small" class="mobile-full-width"
                   popper-class="el-select-group-less-padding"
                   @change="changeCategory" no-data-text="无分类数据">
          
          <el-option-group label="卡密订单">
            <el-option label="所有订单" :value="-1"/>
            <el-option v-for="item in categories"
                       :key="item.id" :label="item.name" :value="item.id"/>
          </el-option-group>
        </el-select>
        <el-select v-if="where.category_id > 0" v-model="where.product_id" filterable
                   size="small" class="mobile-full-width" @change="getList">
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
            
            <el-option v-if="!isApiOrder && !onlyProfit" label="买家联系方式" value="contact"/>
          </el-select>
        </el-input>
      </el-form-item>

      <el-form-item class="mobile-full-width" v-if="!recentDay">
        <el-date-picker size="small" v-model="date_range" type="daterange" align="right"
                        placeholder="日期范围" :picker-options="pickerOptions">
        </el-date-picker>
      </el-form-item>
      <el-form-item>
        <el-button @click="handleSearch" size="small">搜索</el-button>
      </el-form-item>


      <div class="mobile-full-width"></div>

      <el-form-item v-if="!onlyProfit">
        <el-switch v-model="where.showUnPay"
                   inactive-text="未支付订单"
                   :active-value="1"
                   :inactive-value="0"
                   @change="handleShowUnPay">
        </el-switch>
      </el-form-item>

      <el-form-item v-if="!onlyProfit && !isApiOrder">
        <el-switch v-model="where.showUnSend"
                   inactive-text="待发货订单"
                   :active-value="1"
                   :inactive-value="0"
                   @change="handleShowUnSend">
        </el-switch>
      </el-form-item>

      
      <el-form-item class="right-toolbox">
        <el-link @click="handleExport()">
          <icon name="light/file-alt"/>&nbsp;{{'导出('+list.total+')'}}
        </el-link>
      </el-form-item>
    </el-form>
  </el-col>

  <el-col v-if="onlyProfit" :span="24" class='list-after-action'>
    <el-card class="my-card" style="box-shadow: none" v-loading="loading">
      <div style="font-size: 14px">
        总收入:&nbsp;{{parseInt(list.profit_sum.income) | moneyFilter | moneyFormatterFilter}}，
        总利润：&nbsp;{{parseInt(list.profit_sum.profit) | moneyFilter | moneyFormatterFilter}}
      </div>
    </el-card>
  </el-col>

  <el-table v-if="!destroyForLoad" :data="list.data" v-loading="loading" stripe border class="list-table"
            @filter-change="onFilterChange"
            @selection-change="val => checkedVal = val">
    
    

    <el-table-column prop="order_no" label="订单号(点击查看)" width="180">
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
        <el-tag :type="ORDER_STATUS.get(scope.row.status).type">
          {{ORDER_STATUS.get(scope.row.status).name}}
        </el-tag>
      </template>
    </el-table-column>

    <el-table-column v-if="!onlyProfit && PRODUCT_MANUAL" prop="status" label="发货状态" width="100"
                     column-key="status"
                     filter-placement="bottom">
      <template slot-scope="scope">
        <el-select v-if="scope.row.send_status===SEND_STATUS.CARD_UN.id ||
        scope.row.send_status===SEND_STATUS.CARD_PROCESSING.id ||
        scope.row.send_status===SEND_STATUS.CARD_FAILED.id"
                   class="send_status-select"
                   :value="scope.row.send_status"
                   @change="v=>handleSetSendStatus(scope.row, v)"
                   size="mini">
          <el-option label="待发货" :value="SEND_STATUS.CARD_UN.id"/>
          <el-option label="正在处理" :value="SEND_STATUS.CARD_PROCESSING.id"/>
          <el-option label="处理失败" :value="SEND_STATUS.CARD_FAILED.id"/>
          <el-option label="已发货" :value="SEND_STATUS.CARD_SUCCESS.id"/>
        </el-select>
        <span v-else>
          {{SEND_STATUS.get(scope.row.send_status).name}}
        </span>
      </template>
    </el-table-column>

    


    <el-table-column prop="api_out_no" label="通知" width="80" v-if="isApiOrder">
      <template slot-scope="scope">
        <a class="line-btn" @click="showOrder(scope.row)">
          <el-tag :type="scope.row.api_info && scope.row.api_info.notify_ret==='success' ? 'success' : 'warning'"
          >
            {{scope.row.api_info && scope.row.api_info.notify_ret || '未通知'}}
          </el-tag>
        </a>
      </template>
    </el-table-column>


    <el-table-column prop="paid_at" label="支付时间" width="160"/>


    <el-table-column prop="api_out_no" label="商户订单号" width="170" show-overflow-tooltip v-if="isApiOrder"/>
    <el-table-column prop="api_info.subject" label="商品名称" width="120" show-overflow-tooltip v-if="isApiOrder"/>

    <el-table-column prop="product" label="商品名称" min-width="150" show-overflow-tooltip
                     v-if="!isApiOrder && !onlyProfit">
      <template slot-scope="scope">
        <span class="red-text">{{scope.row.count}}</span><span>&nbsp;x&nbsp;</span>
        <el-tooltip effect="dark" :content="'当前商品名称:'+scope.row.product.name" placement="bottom-start"
                    v-if="scope.row.product && scope.row.product_name && scope.row.product_name!==scope.row.product.name">
          <span class="orange-text">{{ scope.row.product_name }}</span>
        </el-tooltip>
        <span v-else>{{ scope.row.product_name || (scope.row.product?scope.row.product.name:'未知#'+scope.row.product_id) }}</span>
      </template>
    </el-table-column>

    <el-table-column prop="card" label="卡密" width="60" v-if="!isApiOrder && !onlyProfit">
      <template slot-scope="scope">
        <a class="line-btn" @click="showCards(scope.row)" style="color: #409eff">
          {{scope.row.card_orders_count ? (scope.row.card_orders_count+'张'):'无'}}
        </a>
      </template>
    </el-table-column>

    <el-table-column prop="pay" label="支付方式" min-width="80" show-overflow-tooltip>
      <template slot-scope="scope">
        {{scope.row.pay?scope.row.pay.name:(scope.row.pay_id === ID_INNER_RETAIL ? '代理进货' :'未知#'+scope.row.pay_id)}}
      </template>
    </el-table-column>

    <el-table-column v-if="onlyProfit" prop="cost" label="成本" width="80">
      <template slot-scope="scope">
        {{scope.row.cost | moneyFilter}}
      </template>
    </el-table-column>

    <el-table-column v-if="onlyProfit && !isApiOrder" prop="paid" label="售价" width="80">
      <template slot-scope="scope">
        {{scope.row.paid | moneyFilter}}
      </template>
    </el-table-column>

    <el-table-column v-if="!onlyProfit" prop="price" label="金额" width="80">
      <template slot-scope="scope">
        <el-tooltip v-if="scope.row.discount > 0 || scope.row.sms_price > 0" effect="dark" placement="top-start"
                    :content="'商品总价：'+(scope.row.price/ 100).toFixed(2)
                    +(scope.row.discount  ? (' - 优惠金额：' + (scope.row.discount / 100).toFixed(2)): '')
                    +(scope.row.sms_price ? (' + 短信费用：' + (scope.row.sms_price/ 100).toFixed(2)) : '')">
          <span>{{scope.row.paid | moneyFilter}}</span>
        </el-tooltip>
        <span v-else>
             {{scope.row.paid | moneyFilter}}
        </span>
      </template>
    </el-table-column>

    <!--商户的入账-->
    <el-table-column prop="income" label="入账" width="80" v-if="userInfo">
      <template slot-scope="scope">
        <el-tooltip effect="dark" placement="top-start"
                    :content="'手续费: ' + (scope.row.fee/ 100).toFixed(2)">
          <span>{{scope.row.income | moneyFilter}}</span>
        </el-tooltip>
      </template>
    </el-table-column>

    <!--平台的利润-->
    


    <el-table-column v-if="onlyProfit" prop="profit" label="利润" width="80">
      <template slot-scope="scope">
        {{(scope.row.paid - scope.row.sms_price - scope.row.cost - scope.row.fee) | moneyFilter}}
      </template>
    </el-table-column>


    <el-table-column v-if="!onlyProfit && !isApiOrder" prop="contact" label="买家联系方式" min-width="160"
                     show-overflow-tooltip>
      <template slot-scope="scope">
        <span>{{scope.row.contact}}</span>
      </template>
    </el-table-column>

    <el-table-column v-if="!onlyProfit && isApiOrder" prop="api_info.attach" label="附加" min-width="160"
                     show-overflow-tooltip>
      <template slot-scope="scope">
        <a class="line-btn">
          {{scope.row.api_info ? (scope.row.api_info.attach || '无') : '无'}}
        </a>
      </template>
    </el-table-column>


    <el-table-column v-if="!onlyProfit" prop="remark" label="备注" min-width="50" show-overflow-tooltip>
      <template slot-scope="scope">
        <a class="line-btn" @click="showRemark(scope.row)">{{scope.row.remark || '添加'}}</a>
      </template>
    </el-table-column>


    <el-table-column label="操作" :width="'80'"
                     fixed="right">
      <template slot-scope="scope">
        <a class="icon-btn" @click="showOrder(scope.row)" style="font-size: 14px">
          查看
        </a>
        <a class="icon-btn" @click="handleFreeze(scope.row)" style="font-size: 14px"
           :style="{color:scope.row.pay_id === ID_INNER_RETAIL?'gray':''}"
           v-if="false">
          冻结
        </a>
        <a class="icon-btn" @click="handleUnfreeze(scope.row)" style="font-size: 14px"
           :style="{color:scope.row.pay_id === ID_INNER_RETAIL?'gray':''}"
           v-if="false">
          解冻
        </a>
        <a class="icon-btn" @click="handleSetPaid(scope.row)" style="font-size: 14px"
           :style="{color:scope.row.pay_id === ID_INNER_RETAIL?'gray':''}"
           v-if="scope.row.status === ORDER_STATUS.UN_PAY.id">
          补单
        </a>
        <!-- 分销任何一层都可以对未发货订单重新发货 -->
        <a class="icon-btn" @click="handleSendCard(scope.row)" style="font-size: 14px"
           v-if="scope.row.status===ORDER_STATUS.PAID.id">
          发货
        </a>
      </template>
    </el-table-column>

  </el-table>


  


  <el-col :span="24" class='list-after-action' style="padding-top: 12px;text-align: right;">
    <el-pagination
        v-if='list.total>0'
        class='pagination'
        :page-size="list.per_page"
        :page-sizes="[10, 20, 30, 40, 50, 100]"
        :total="list.total"
        :current-page.sync='list.current_page'
        layout="total, sizes, prev, pager, next, jumper"
        @current-change='onChangePage'
        @size-change='onChangePageSize'>
    </el-pagination>
  </el-col>

  <div v-if="loadingInfo" style="z-index: 11010" class="v-modal"></div>
  <div v-if="loadingInfo" style="z-index: 11011" class="el-message-box__wrapper">
    <div style="position: relative;display: inline-block;width: 250px;
  background-color: #fff;border-radius: 4px;border: 1px solid #ebeef5;">
      <p>加载中...</p>
    </div>
  </div>

  <order-info ref="orderInfo" from="order" v-if="orderInfo" :info="orderInfo" @close="orderInfo=null"/>

  <export-dialog v-if="PRODUCT_MANUAL && exportDialogVisible"
                 @close="exportDialogVisible=false" @export="handleExport"/>

</div>
</template>

<script>
import NProgress from 'nprogress';
import { getList as getOrderList, ORDER_STATUS, SEND_STATUS, PAY_SUB_PROFIT_STATUS, editRemark, setPaid, freeze, unfreeze, refund, ship, info, set_send_status, pay_sub_profit_share } from '~/api/order';
import { getList as getCategoryList } from '~/api/category';
import { getList as getProductList, DELIVERY } from '~/api/product';
import { ID_INNER_RETAIL } from '~/api/pay_user';
import { param, randomString, htmlspecialchars } from '~/utils/helper';
import OrderInfo from '../_info';
import ExportDialog from "./exportDialog";
const API_ORDER_ID = -1001;

// 是否开启手动发货功能
const PRODUCT_MANUAL = window.config.functions && window.config.functions.includes('product_manual');
const DIRECT_PAY_OPEN = !!window.config.direct_pay_open;
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
    }
  },
  components: {
    ExportDialog,
    OrderInfo
  },
  data() {
    return {
      isMobile: this.$store.state.app.isMobile,
      loading: false,
      ORDER_STATUS,
      SEND_STATUS,
      PAY_SUB_PROFIT_STATUS,
      API_ORDER_ID,
      ID_INNER_RETAIL,
      PRODUCT_MANUAL,
      DIRECT_PAY_OPEN,
      search: {
        type: 'order_no',
        value: ''
      },
      date_range: '',
      where: {
        category_id: -1,
        product_id: -1,
        showUnPay: 0,
        showUnSend: 0
      },
      categories: [{
        id: -2,
        name: '加载中'
      }],
      products: [{
        id: -2,
        name: '加载中'
      }],
      list: {
        current_page: 1,
        data: [],
        from: 1,
        last_page: 1,
        next_page_url: null,
        per_page: +localStorage.getItem('pageSize') || 10,
        prev_page_url: null,
        to: 0,
        total: 0,
        profit_sum: {
          income: 0,
          profit: 0
        }
      },
      checkedVal: [],
      statusFilter: (() => {
        const filtered = [];
        for (const key in ORDER_STATUS) {
          if (Object.prototype.hasOwnProperty.call(ORDER_STATUS, key) && typeof ORDER_STATUS[key] === 'object') {
            filtered.push({
              text: ORDER_STATUS[key].name,
              value: ORDER_STATUS[key].id,
              type: ORDER_STATUS[key].type
            });
          }
        }
        return filtered;
      })(),
      statusFilterValue: [],
      pay_sub_profit_statusFilter: (() => {
        const filtered = [];
        for (const key in PAY_SUB_PROFIT_STATUS) {
          if (Object.prototype.hasOwnProperty.call(PAY_SUB_PROFIT_STATUS, key) && typeof PAY_SUB_PROFIT_STATUS[key] === 'object') {
            filtered.push({
              text: PAY_SUB_PROFIT_STATUS[key].name,
              value: PAY_SUB_PROFIT_STATUS[key].id,
              type: PAY_SUB_PROFIT_STATUS[key].type
            });
          }
        }
        return filtered;
      })(),
      pay_sub_profit_statusFilterValue: [],
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 604800000);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 2592000000);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 7776000000);
            picker.$emit('pick', [start, end]);
          }
        }]
      },
      exportDialogVisible: false,
      destroyForLoad: false,
      isApiOrder: false,
      orderInfo: null,
      loadingInfo: false
    };
  },
  watch: {
    loading(val, old) {
      if (val) {
        if (!old && !NProgress.isStarted()) NProgress.start();
      } else {
        NProgress.done();
      }
    },
    isApiOrder() {
      this.destroyForLoad = true;
      this.$nextTick(() => {
        this.destroyForLoad = false;
      });
    }
  },
  mounted() {
    if (this.$route.query.type === 'api') {
      this.where.category_id = API_ORDER_ID;
      this.isApiOrder = true;
    }
    this.getCategories();
  },
  methods: {
    getCategories() {
      this.loading = true;
      const where = {
        search: 'simple',
        val: 'list'
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getCategoryList(where).then(ret => {
        this.categories = ret.data;
        this._loadOrders();
      });
    },
    /**
     * 加载完分类后拉取订单
     * @private
     */
    _loadOrders() {
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
    changeCategory(category_id) {
      this.list.current_page = 1;
      this.list.from = 1;
      this.list.last_page = 1;
      this.list.to = 1;
      this.list.total = 0;
      this.list.data = [];
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
    getProducts(category_id) {
      this.getList();
      this.where.product_id = -1;
      this.products = [];
      const where = {
        search: 'simple',
        val: category_id
      };
      if (this.userInfo) {
        where.user_id = this.userInfo.id;
      }
      getProductList(where).then(ret => {
        this.products = ret.data;
      });
    },
    getList() {
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
    handleSearch() {
      const where = {
        search: this.search.type,
        val: this.search.value
      };
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
    handleExport(exportUpdate = false) {
      const filter = Object.assign({}, this.where);
      if (PRODUCT_MANUAL) {
        if (exportUpdate === false) {
          this.exportDialogVisible = true;
          return;
        } else {
          filter.export_update_status = exportUpdate.status;
          filter.export_update_card = exportUpdate.card;
        }
      }
      window.open(window.API_URL + 'order/export?' + param(filter) + '&token=' + encodeURIComponent(this.$store.state.user.token), '_blank');
    },
    handleShowUnPay(val) {
      if (val) {
        this.where.showUnSend = 0;
        this.statusFilterValue.splice(0, this.statusFilterValue.length);
        this.statusFilterValue.push(ORDER_STATUS.UN_PAY.id);
      } else {
        this.handleShowWithoutUnpay();
      }
      this.where.status = this.statusFilterValue.join(',');
      this.handleSearch();
    },
    handleShowUnSend(val) {
      if (val) {
        this.where.showUnPay = 0;
        this.statusFilterValue.splice(0, this.statusFilterValue.length);
        this.statusFilterValue.push(ORDER_STATUS.PAID.id);
      } else {
        this.handleShowWithoutUnpay();
      }
      this.where.status = this.statusFilterValue.join(',');
      this.handleSearch();
    },
    handleShowWithoutUnpay() {
      this.statusFilterValue.splice(0, this.statusFilterValue.length, ...[ORDER_STATUS.FROZEN.id, ORDER_STATUS.SUCCESS.id, ORDER_STATUS.PAID.id, ORDER_STATUS.REFUND.id]);
      // 后端默认就是除去 unpaid 这里不加了
      this.where.status = '';
      this.handleSearch();
    },
    onFilterChange(obj) {
      const where = {};
      if (obj['status']) {
        where.status = obj['status'].join(',');
      } else {
        where.status = null;
      }
      if (DIRECT_PAY_OPEN) {
        if (obj['pay_sub_profit_status']) {
          where.pay_sub_profit_status = obj['pay_sub_profit_status'].join(',');
        } else {
          where.pay_sub_profit_status = null;
        }
      }
      Object.assign(this.where, where);
      this.getList();
    },
    onChangePage() {
      this.getList();
    },
    onChangePageSize(per_page) {
      localStorage.setItem('pageSize', per_page);
      this.list.per_page = per_page;
      this.getList();
    },
    async showCards(row) {
      if (!row.card_orders) {
        this.loadingInfo = true;
        try {
          Object.assign(row, (await info(row.id)).data);
        } catch (e) {
          return;
        } finally {
          this.loadingInfo = false;
        }
      }
      this.$msgbox({
        title: '卡密详情',
        message: '<textarea rows="6" style="width: 100%" readonly>' + htmlspecialchars(row.card_orders.map(e => e.card ? e.card.card : '未知卡密#' + e.card_id).join('\n')) + '</textarea>',
        dangerouslyUseHTMLString: true
      });
    },
    showRemark(row) {
      
      this.$prompt('可以在这里修改备注', '提示', {
        confirmButtonText: '保存',
        cancelButtonText: '取消',
        inputValue: row.remark,
        inputPattern: /^[\s\S]{0,100}$/,
        inputErrorMessage: '备注需100字符以内',
        inputType: 'textarea',
        closeOnClickModal: false
      }).then(({
        value
      }) => {
        this.loading = true;
        editRemark(row.id, value).then(() => {
          row.remark = value;
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    async showOrder(row) {
      let order = row;
      this.loadingInfo = true;
      try {
        order = (await info(row.id)).data;
        order.show_order_id = row.id;
      } catch (e) {
        return;
      } finally {
        this.loadingInfo = false;
      }
      this.orderInfo = order;
    },
    handleFreeze(row) {
      if (row.pay_id === ID_INNER_RETAIL) {
        return this.$msgbox({
          title: '提示',
          message: '此订单为分销商品自动生成的订单,  请操作原始订单',
          type: 'error'
        });
      }
      this.$prompt(`冻结订单 ${row.order_no} ? <br>请输入与冻结原因`, '提示', {
        dangerouslyUseHTMLString: true,
        confirmButtonText: '保存',
        cancelButtonText: '取消',
        inputValue: row.frozen_reason,
        inputPattern: /^[\s\S]{2,100}$/,
        inputErrorMessage: '冻结原因需2-100字符以内',
        inputType: 'textarea',
        closeOnClickModal: false
      }).then(({
        value
      }) => {
        this.loading = true;
        freeze(row.id, value).then(res => {
          // available count
          if (res.data[0] !== 1) {
            return this.$msgbox({
              title: '操作失败',
              message: '未知错误',
              type: 'error'
            });
          }
          // successfully count
          if (res.data[1] !== 1) {
            return this.$msgbox({
              title: '操作失败',
              message: '冻结订单失败, 内部错误',
              type: 'error'
            });
          }
          this.$notify({
            title: '操作完成',
            message: row.order_no + ' 已冻结',
            type: 'success'
          });
          row.status = ORDER_STATUS.FROZEN.id;
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    handleUnfreeze(row) {
      if (row.pay_id === ID_INNER_RETAIL) {
        return this.$msgbox({
          title: '提示',
          message: '此订单为分销商品自动生成的订单,  请操作原始订单',
          type: 'error'
        });
      }
      this.$confirm('解冻订单 ' + row.order_no + ' ? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        unfreeze(row.id).then(res => {
          // available count
          if (res.data[0] !== 1) {
            return this.$msgbox({
              title: '操作失败',
              message: '未知错误',
              type: 'error'
            });
          }
          // successfully count
          if (res.data[1] !== 1) {
            return this.$msgbox({
              title: '操作失败',
              message: '解冻订单失败, 内部错误',
              type: 'error'
            });
          }
          this.$notify({
            title: '操作完成',
            message: row.order_no + ' 已解冻',
            type: 'success'
          });
          row.status = res.data[2];
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    handleRefund(row) {
      if (row.pay_id === ID_INNER_RETAIL) {
        return this.$msgbox({
          title: '提示',
          message: '此订单为分销商品自动生成的订单,  请操作原始订单',
          type: 'error'
        });
      }
      let isRefundApi = false;
      const h = this.$createElement;
      this.$confirm(h('div', {
        key: randomString()
      }, [h('p', null, '退款订单' + row.order_no + '（支付金额 ￥' + (row.paid / 100).toFixed(2) + ') '), h('p', null, '此操作不可恢复'), h('ElCheckbox', {
        props: {
          checked: isRefundApi
        },
        on: {
          change(val) {
            isRefundApi = val;
          }
        }
      }, '自动请求支付渠道发起退款')]), '询问', {
        type: 'warning',
        dangerouslyUseHTMLString: true
      }).then(() => {
        this.loading = true;
        refund(row.id, isRefundApi).then(res => {
          // available count
          if (res.data[0] !== 1) {
            return this.$msgbox({
              title: '操作失败',
              message: '未知错误',
              type: 'error'
            });
          }
          // successfully count
          if (res.data[1] !== 1) {
            return this.$msgbox({
              title: '操作失败',
              message: '退款订单失败, 内部错误',
              type: 'error'
            });
          }
          this.$notify({
            title: '操作完成',
            message: row.order_no + ' 已退款',
            type: 'success'
          });
          row.status = ORDER_STATUS.REFUND.id;
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    checkFreeze() {
      if (!this.checkedVal.length) return;
      if (this.checkedVal.length === 1) return this.handleFreeze(this.checkedVal[0]);
      const checkedIds = this.checkedVal.map(v => v.id).join(',');
      this.$prompt('批量冻结选中订单? (只冻结交易成功订单) <br>请输入与冻结原因', '提示', {
        dangerouslyUseHTMLString: true,
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        inputPattern: /^[\s\S]{2,100}$/,
        inputErrorMessage: '冻结原因需2-100字符以内',
        inputType: 'textarea',
        closeOnClickModal: false
      }).then(({
        value
      }) => {
        this.loading = true;
        freeze(checkedIds, value).then(res => {
          this.$notify({
            title: '操作完成',
            message: `总订单: ${res.data[0]} 笔，冻结成功 ${res.data[1]} 笔`,
            type: res.data[0] && res.data[0] === res.data[1] ? 'success' : 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkUnfreeze() {
      if (!this.checkedVal.length) return;
      if (this.checkedVal.length === 1) return this.handleUnfreeze(this.checkedVal[0]);
      const checkedIds = this.checkedVal.map(v => v.id).join(',');
      this.$confirm('批量解冻选中订单? (只处理已冻结订单)', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        unfreeze(checkedIds).then(res => {
          this.$notify({
            title: '操作完成',
            message: `总订单: ${res.data[0]} 笔，解冻成功 ${res.data[1]} 笔`,
            type: res.data[0] && res.data[0] === res.data[1] ? 'success' : 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkRefund() {
      if (!this.checkedVal.length) return;
      if (this.checkedVal.length === 1) return this.handleRefund(this.checkedVal[0]);
      const checkedIds = this.checkedVal.map(v => v.id).join(',');
      this.$confirm('批量退款选中订单? (请手动联系客户退款)', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        refund(checkedIds).then(res => {
          this.$notify({
            title: '操作完成',
            message: `总订单: ${res.data[0]} 笔，退款成功 ${res.data[1]} 笔`,
            type: res.data[0] && res.data[0] === res.data[1] ? 'success' : 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    checkPaySubProfitShare() {
      if (!this.checkedVal.length) return;
      

      const checkedIds = this.checkedVal.map(v => v.id).join(',');
      this.$confirm('批量分润选中订单? ', '提示', {
        type: 'warning'
      }).then(() => {
        this.loading = true;
        pay_sub_profit_share(checkedIds).then(res => {
          this.$notify({
            title: '操作完成',
            message: `总订单: ${res.data[0]} 笔，分润成功 ${res.data[1]} 笔`,
            type: res.data[0] && res.data[0] === res.data[1] ? 'success' : 'info'
          });
          this.getList();
        }).catch(() => {
          this.loading = false;
        });
      });
    },
    handleSetPaid(row) {
      if (row.pay_id === ID_INNER_RETAIL) {
        return this.$msgbox({
          title: '提示',
          message: '此订单为分销商品自动生成的订单,  请操作原始订单',
          type: 'error'
        });
      }
      const payName = row.pay ? row.pay.name : '支付方式#' + row.pay_id;
      this.$prompt(`请仔细确认已经收到款项，<br>确认将订单 ${row.order_no} 设置为已付款并发货? <br>请输入${payName}内单号(流水号)`, '提示', {
        dangerouslyUseHTMLString: true,
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        inputPattern: /^[\s\S]{2,100}$/,
        inputErrorMessage: '单号需2-100字符以内',
        inputType: 'text',
        closeOnClickModal: false
      }).then(({
        value
      }) => {
        this.loading = true;
        setPaid(row.id, value).then(res => {
          const ret = res.data;
          Object.assign(row, ret.order);
          if (ret.code !== 0) {
            return this.$msgbox({
              title: '提示',
              message: ret.msg,
              type: 'error',
              dangerouslyUseHTMLString: true
            });
          }
          this.$notify({
            title: '操作完成',
            message: row.order_no + ' 已补单成功',
            type: 'success'
          });
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    handleSendCard(row) {
      if (!row.product) {
        return this.$msgbox('订单对应商品不存在, 无法发货');
      }
      let promise = null;
      if (row.product.delivery === DELIVERY.AUTO) {
        promise = this.$confirm(`当前订单状态为<${ORDER_STATUS.get(row.status).name}>, 是否确认发货?`, '询问');
      } else {
        promise = this.$prompt(`当前订单状态为<${ORDER_STATUS.get(row.status).name}>, 是否确认发货?<br>请输入发货卡密`, '提示', {
          dangerouslyUseHTMLString: true,
          confirmButtonText: '确认',
          cancelButtonText: '取消',
          inputValue: '已充值成功',
          inputPattern: /^[\s\S]{1,255}$/,
          inputErrorMessage: '需1-255字符以内',
          inputType: 'text',
          closeOnClickModal: false
        });
      }
      promise.then(({
        value
      }) => {
        this.loading = true;
        ship(row.id, value).then(res => {
          Object.assign(row, res.data);
          this.$notify({
            title: '发货成功',
            message: '当前订单已发货成功',
            type: 'success',
            duration: 3000
          });
        }).finally(() => {
          this.loading = false;
        });
      });
    },
    handleSetSendStatus(row, send_status) {
      if (send_status === SEND_STATUS.CARD_SUCCESS.id) {
        return this.handleSendCard(row);
      }
      const old_status = row.send_status;
      row.send_status = send_status;
      set_send_status(row.id, send_status).catch(() => {
        row.send_status = old_status;
      });
    }
  }
};
</script>

<style lang="scss">
  .el-select-group__title {
    padding-left: 12px !important;
    line-height: 24px !important;
  }

  .send_status-select {
    .el-input__inner {
      padding: 0 4px !important;
    }
  }
</style>


