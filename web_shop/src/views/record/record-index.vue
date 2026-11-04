<template>
<div class="content">
  <div class="record-title" style="margin-top: -4px;">订单查询方式：</div>
  <mu-tabs class="record-tabs" @change="handleChangeTab" :value="tab">
    <mu-tab value="cookie">浏览器缓存</mu-tab>
    <mu-tab value="order_no">订单号</mu-tab>
    <mu-tab value="contact">联系方式</mu-tab>
  </mu-tabs>

  <mu-card class="my-card white-background" style="margin-bottom: 24px">
    <mu-card-text v-show="tab === 'cookie'">
      订单提交时浏览器会自动保存您的订单信息，不同浏览器可能显示结果不同。<br>
      <p class="text-center" style="margin-top: 9px;">
        <mu-button @click="handleSearch" class="record-input-btn"
                   :disabled="$captcha.loading">{{$captcha.loading?'加载中':'查询'}}
        </mu-button>
      </p>
      订单查询仅能查询最近购买的数据。
    </mu-card-text>
    <mu-card-text v-show="tab === 'order_no'">
      <mu-text-field name="order_no" type="text" label="订单号" placeholder="请输入订单号" class="record-input"
                     @input="loaded=false" v-model="order_no"/>

      <div class="equal-with-mu">
        <mu-button @click="handleSearch" @change="onOrderNoChange"
                   class="record-input-btn"
                   :disabled="$captcha.loading">{{$captcha.loading?'加载中':'查询'}}
        </mu-button>
      </div>
      订单查询仅能查询最近购买的数据。
    </mu-card-text>
    <mu-card-text v-show="tab === 'contact'">
      <mu-text-field name="contact" type="text" label="联系方式" placeholder="购买时填写的联系方式" class="record-input"
                     @input="loaded=false" v-model="contact"/>

      <div class="equal-with-mu">
        <mu-button @click="handleSearch" @change="onContactChange"
                   class="record-input-btn"
                   :disabled="$captcha.loading">{{$captcha.loading?'加载中':'查询'}}
        </mu-button>
      </div>
      订单查询仅能查询最近购买的数据。
    </mu-card-text>
  </mu-card>
  <mu-card class="my-card white-background" v-show="loading || list.length || loaded">
    <mu-card-text>
      <mu-circular-progress v-if="loading" :size="40" style="width: 100%; text-align: center"/>

      <p v-show="!loading && (!list || list.length === 0)">没有订单记录</p>

      <table v-for="item in list" :key="item.id"
             width="100%" border="1" cellspacing="0" cellpadding="10" class="cx_tab">
        <tbody>
        <tr v-if="item.product_name">
          <th scope="row" width="260">商品名称</th>
          <td>{{item.product_name}}</td>
        </tr>
        <tr>
          <th scope="row" width="260">下单时间</th>
          <td>{{item.created_at}}</td>
        </tr>
        <tr>
          <th scope="row">订单号</th>
          <td>{{item.order_no}}</td>
        </tr>
        <tr>
          <th scope="row">订单数目</th>
          <td>{{item.count}}&nbsp;({{item.paid | moneyFilter | moneyFormatterFilter}})</td>
        </tr>
        <tr>
          <th scope="row">订单信息</th>
          <td class="over-ellipsis">
            <a v-if="item.status === STATUS_SUCCESS || item.status === STATUS_FROZEN"
               class="cursor-pointer" target="_blank" style="line-height: 24px;text-decoration: underline"
               :href="'/pay/result/'+item.order_no">查看卡密
            </a>
            <span v-else-if="item.status === STATUS_UNPAY" style="line-height: 24px">未支付
              <a class="cursor-pointer" target="_blank" :href="'/pay/' + item.order_no">支付</a>
            </span>
            <a v-else-if="item.status === STATUS_PAID" class="cursor-pointer" style="line-height: 24px"
               @click="handleShowPaidTip(item)">{{renderOrderSendStatus(item)}}</a>
            <span v-else-if="item.status === STATUS_REFUND" style="line-height: 24px">已退款</span>
            <span v-else style="line-height: 24px">未知状态</span>
            
          </td>
        </tr>
        <tr v-if="item.contact">
          <th scope="row">联系信息</th>
          <td>{{item.contact}}</td>
        </tr>
        <tr v-if="item.contact_ext">
          <th scope="row">附加信息</th>
          <td>{{item.contact_ext}}</td>
        </tr>
        </tbody>
      </table>
    </mu-card-text>
  </mu-card>
</div>
</template>

<script>
import { htmlspecialchars, randomString, param } from '~/utils/helper';
const STATUS_UNPAY = 0;
const STATUS_PAID = 1; // 已付款未发货
const STATUS_SUCCESS = 2; // 已发货成功
const STATUS_FROZEN = 3;
const STATUS_REFUND = 4;
const SEND_STATUS_CARD_UN = 100;
const SEND_STATUS_CARD_PROCESSING = 101;
const SEND_STATUS_CARD_SUCCESS = 102;
const SEND_STATUS_CARD_FAILED = 103;

// 是否开启查询密码
const ORDER_QUERY_PASSWORD = window.config.functions && window.config.functions.includes('order_query_password');
export default {
  data() {
    return {
      STATUS_UNPAY,
      STATUS_PAID,
      STATUS_SUCCESS,
      STATUS_FROZEN,
      STATUS_REFUND,
      loading: false,
      loaded: false,
      columns: [{
        title: '时间',
        width: 140,
        name: 'created_at',
        align: 'center'
      }, {
        title: '订单',
        width: 160,
        name: 'order_no',
        align: 'center'
      }, {
        title: '数目/金额',
        width: 100,
        name: 'count'
      }, {
        title: '订单信息',
        width: 120,
        name: 'iron'
      }],
      order_no: '',
      contact: '',
      query_password: '',
      list: [],
      list_bak: {
        cookie: [],
        order_no: [],
        contact: []
      },
      tab: ''
    };
  },
  async mounted() {
    if (window.$router.query.tab) {
      this.tab = window.$router.query.tab;
    } else {
      this.tab = 'contact';
    }
    window.vm.$on('goRecordCookie', () => {
      this.tab = 'cookie';
      this.handleSearch();
    });
  },
  beforeDestroy() {
    window.vm.$off('goRecordCookie');
  },
  methods: {
    handleChangeTab(val) {
      if (this.tab === 'cookie') {
        this.list_bak.cookie = this.list;
      } else if (this.tab === 'order_no') {
        this.list_bak.order_no = this.list;
      } else if (this.tab === 'contact') {
        this.list_bak.contact = this.list;
      }
      this.tab = val;
      window.$router.push(window.$router.nav, {
        tab: val
      });
      this.loaded = false;
      this.loading = false;
      if (this.tab === 'cookie') {
        this.list = this.list_bak.cookie;
      } else if (this.tab === 'order_no') {
        this.list = this.list_bak.order_no;
      } else if (this.tab === 'contact') {
        this.list = this.list_bak.contact;
      }
    },
    onOrderNoChange(val) {
      this.order_no = val.target ? val.target.value : val;
    },
    onContactChange(val) {
      this.contact = val.target ? val.target.value : val;
    },
    async handleSearch(e) {
      this.order_no = this.order_no.trim();
      this.contact = this.contact.trim();
      const tab = this.tab;
      if (tab === 'order_no') {
        if (this.order_no.length !== 19) return this.$alert('请输入19位订单号进行查询');
        this.list_bak.order_no = [];
      }
      if (tab === 'contact') {
        if (this.contact.length < 6) return this.$alert('输入的联系方式格式不正确');
        if (ORDER_QUERY_PASSWORD) {
          await this.$nextTick(); // 等待 this.$alert 注入到组件
          try {
            const {
              input
            } = await this.$alert({
              message: '请输入购买时填写的订单查询密码',
              title: '查询密码',
              cancelBtnTitle: '取消',
              needInput: true,
              inputErrorText: '订单查询密码'
            });
            this.query_password = input;
          } catch (e) {
            this.$alert('您没有输入订单查询密码，无法搜索订单<br>若您忘记密码，请及时联系客服');
            return;
          }
        }
        this.list_bak.contact = [];
      }
      if (window.config.captcha.scene.shop.search) {
        try {
          await this.$captcha.refresh();
          await this.$captcha.verify();
          console.log('this.$captcha.verify successfully', this.$captcha.value);
        } catch (e) {
          console.log('this.$captcha.verify failed', e);
          if (e.message !== 'cancel') {
            this.$alert(e.message || '请重新验证', '验证码验证失败').finally(() => {
              this.$captcha.focus();
            });
          }
          return;
        }
      }
      this.list = [];
      this.loading = true;
      this.$http.post('record/get', {
        type: tab,
        order_no: this.order_no,
        contact: this.contact,
        query_password: this.query_password,
        captcha: this.$captcha.value
      }).then(e => {
        if (tab !== this.tab) {
          return;
        }
        if (e.data.list) {
          this.list = e.data.list;
        }
        this.loading = false;
        this.loaded = true;
        if (e.data.msg) {
          this.$alert({
            message: e.data.msg,
            submitBtnTitle: '我已知晓'
          });
        }
      }).catch(() => {
        this.loading = false;
        this.loaded = false;
      });
    },
    /**
     * 查看卡密, 已经弃用, 使用购买结束的单页显示 /result/xxx
     * @deprecated
     */
    showCardDetail(card_orders) {
      if (card_orders && card_orders.length) {
        this.$alert({
          message: '<div><textarea title="卡号列表" rows="6" style="width: 100%" readonly>' + htmlspecialchars(card_orders.map(e => e.card ? e.card.card : '未知卡密#' + e.card_id).join('\r\n')) + '</textarea><span>由于安全原因订单商品详情不予显示</span></div>',
          title: '卡密详情',
          scrollable: true
        });
        // renderDelta()
      } else {
        this.$alert('该订单无卡密信息 或 卡密信息已隐藏');
      }
    },
    renderOrderSendStatus(order) {
      if (order.status === STATUS_PAID) {
        if (order.send_status === SEND_STATUS_CARD_UN) return '未发货';
        if (order.send_status === SEND_STATUS_CARD_PROCESSING) return '正在处理';
        if (order.send_status === SEND_STATUS_CARD_SUCCESS) return '已发货';
        if (order.send_status === SEND_STATUS_CARD_FAILED) return '发货失败';
        return '未发货';
      }
      return 'UNKNOWN';
    },
    handleShowPaidTip(order) {
      if (order.send_status === SEND_STATUS_CARD_UN) return this.$alert('此订单暂未发货，请耐心等待');
      if (order.send_status === SEND_STATUS_CARD_PROCESSING) return this.$alert('此订单正在处理中，请耐心等待');
      if (order.send_status === SEND_STATUS_CARD_SUCCESS) return this.$alert('此订单已成功发货，有疑问请联系店铺客服');
      if (order.send_status === SEND_STATUS_CARD_FAILED) return this.$alert('此订单发货失败，请联系店铺客服');
      this.$alert('此商品库存不足，请联系店铺客服补货');
    }
    /**
     * 投诉订单
     * @param row
     */

    /**
     * 查看投诉信息
     * @param row
     */
  }
};
</script>

<style lang="scss" scoped>
  .record-tabs {
    background-color: transparent;
    margin-bottom: 16px;
  }

  .record-input {
    width: calc(100% - 100px);
    margin-right: 6px
  }

  .record-input-btn {
    vertical-align: super;
    width: 88px
  }

  .mu-tab {
    flex: 1;
    color: rgba(0, 0, 0, 0.87);
    max-width: initial;
  }

  .mu-tab-active {
    color: #03a9f4;
  }

  .mu-chip {
    float: right;
    font-size: 12px;
    line-height: 24px;
  }

  .cx_tab {
    border: 1px solid #eee;
    text-align: left;
    border-collapse: collapse;
    margin-bottom: 10px;
    font-size: 14px;

    th {
      font-weight: inherit;
      background-color: rgba(248, 248, 248, 0.2);
    }
  }
</style>
