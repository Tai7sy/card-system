<template>
  <el-dialog :title="title" :visible.sync="orderVisible" class="order-info-container" v-loading="loading">
    <div class="order-info-table" v-if="orderInfo">
      <table border="0" cellspacing="0">
        <tbody>
        <tr>
          <td style="white-space: nowrap;text-align: right;width: 80px;">　订单号</td>
          <td style="white-space: nowrap;min-width: 160px">{{orderInfo.order_no}}</td>
          <td style="white-space: nowrap;text-align: right;width: 80px;">付款方式</td>
          <td style="white-space: nowrap;min-width: 90px">
            {{orderInfo.pay?orderInfo.pay.name:'未知#'+orderInfo.pay_id}}
            <a style="text-decoration:underline;cursor: pointer;color:#409EFF"
               :onclick="`bus.$emit('${from}:show:pay')`"
               v-if="orderInfo.status > ORDER_STATUS.UN_PAY.id">查看</a>
          </td>
        </tr>
        <tr>
          <td style="text-align: right">付款时间</td>
          <td>{{orderInfo.paid_at}}</td>
          <td style="text-align: right">提交金额</td>
          <td>{{orderInfo.paid | moneyFilter}}元</td>
        </tr>
        <tr>
          <td style="text-align: right" v-if="!isApiOrder">联系方式</td>
          <td style="max-width: 280px;word-wrap: break-word;line-height: 1;" v-if="!isApiOrder">{{orderInfo.email}}</td>
          <td style="text-align: right" v-if="isApiOrder">通知状态</td>
          <td style="max-width: 280px;word-wrap: break-word;line-height: 1;" v-if="isApiOrder">
            <span :class="'el-tag el-tag--' + (isNotified(orderInfo)?'success':'warning')">
              {{isNotified(orderInfo) ? '已通知' : '通知失败'}}
            </span>&nbsp;&nbsp;
            <a style="text-decoration:underline;cursor: pointer;color:#409EFF"
               :onclick="`bus.$emit('${from}:send:notify')`"
               v-if="!isNotified(orderInfo)">重发通知</a>
          </td>
          <td style="text-align: right">商户分成</td>
          <td>{{orderInfo.income | moneyFilter}}元</td>
        </tr>
        <tr>
          <td style="text-align: right">订单状态</td>
          <td>
            <span :class="'el-tag el-tag--' + ORDER_STATUS.get(orderInfo.status).type">
              {{ORDER_STATUS.get(orderInfo.status).name}}
            </span>&nbsp;
            <div style="display: inline" v-if="!isApiOrder">
              <span :class="isShipped(orderInfo) ?'green-text':'amber-text'">
                {{isShipped(orderInfo) ? '(已发货)' : '(未发货)'}}
              </span>
              <a style="text-decoration:underline;cursor: pointer;color:#409EFF"
                 :onclick="`bus.$emit('${from}:send:card')`"
                 v-if="!isShipped(orderInfo) && orderInfo.status !== ORDER_STATUS.UN_PAY.id">发货</a>
              <a style="text-decoration:underline;cursor: pointer;color:#409EFF"
                 :onclick="`bus.$emit('${from}:show:card')`"
                 v-if="isShipped(orderInfo)">查看</a>
            </div>
          </td>
          <td style="text-align: right">购买IP</td>
          <td>{{orderInfo.ip}}</td>
        </tr>
        <tr>
          <td style="text-align: right">商户备注</td>
          <td style="max-width: 280px;word-wrap: break-word;line-height: 1.4;">{{orderInfo.remark}}</td>
          <td style="text-align: right">详细信息</td>
          <td style="max-width: 280px;word-wrap: break-word;line-height: 1;">
            <span v-if="orderInfo.status===ORDER_STATUS.FROZEN.id">
              冻结原因: {{orderInfo.frozen_reason || '无'}}
            </span>
          </td>
        </tr>
        </tbody>
      </table>
    </div>
  </el-dialog>
</template>

<script>
  import { notify, ORDER_STATUS, ship } from '~/api/order'

  export default {
    props: {
      from: {
        default: 'order'
      }
    },
    data () {
      return {
        loading: false,
        ORDER_STATUS,
        orderVisible: false,
        orderInfo: null,
        cardInfo: null,
        isApiOrder: false,
        title: '订单信息'
      }
    },
    mounted () {
      this._on_show_card = () => {
        if (this.orderInfo) {
          const cardInfo = this.orderInfo.card_orders.map(e => e.card ? e.card.card : '未知卡密#' + e.card_id).join('<br>');
          this.$msgbox({
            title: '订单卡密信息',
            message: cardInfo,
            dangerouslyUseHTMLString: true
          });
        }
      };
      window.bus.$on(this.from + ':show:card', this._on_show_card);
      this._on_show_pay = () => {
        if (this.orderInfo) {
          const payName = this.orderInfo.pay ? this.orderInfo.pay.name : '支付方式#' + this.orderInfo.pay_id;
          const payInfo = '支付方式: ' + payName + '<br>支付时间: '
            + this.orderInfo.paid_at + '<br>'
            + payName + '单号(流水号): ' + this.orderInfo.pay_trade_no;
          this.$msgbox({
            title: '支付信息',
            message: payInfo,
            dangerouslyUseHTMLString: true
          });
        }
      };
      window.bus.$on(this.from + ':show:pay', this._on_show_pay);
      this._on_send_card = () => {
        if (this.orderInfo) {
          this.$confirm('当前订单状态为<' + ORDER_STATUS.get(this.orderInfo.status).name + '>, 是否确认发货?').then(() => {
            this.loading = true;
            ship(this.orderInfo.id).then(res => {
              this.loading = false;
              Object.assign(this.orderInfo, res.data);
              this.$notify({ title: '发货成功', message: '当前订单已发货成功', type: 'success', duration: 3000 });
            }).catch(() => {
              this.loading = false;
            });
          });
        }
      };
      window.bus.$on(this.from + ':send:card', this._on_send_card);
      this._on_send_notify = () => {
        if (this.orderInfo) {
          this.loading = true;
          notify(this.orderInfo.id).then(res => {
            this.loading = false;
            Object.assign(this.orderInfo.api_info, res.data.api_info);
            this.$msgbox({
              title: '重发结果',
              message: res.data.notify_html,
              dangerouslyUseHTMLString: false
            });
          }).catch(() => {
            this.loading = false;
          });
        }
      };
      window.bus.$on(this.from + ':send:notify', this._on_send_notify);
    },
    beforeDestroy () {
      window.bus.$off(this.from + ':show:card', this._on_show_card);
      window.bus.$off(this.from + ':show:pay', this._on_show_pay);
      window.bus.$off(this.from + ':send:card', this._on_send_card);
      window.bus.$off(this.from + ':send:notify', this._on_send_notify);
    },
    methods: {
      showOrder (info) {
        this.orderInfo = info;
        this.isApiOrder = info.product_id === -1001;
        this.title = this.isApiOrder ? '订单信息(API订单)' : '订单信息(卡密订单)';
        this.orderVisible = true;
      },
      isShipped (info) {
        if (info.status === ORDER_STATUS.SUCCESS.id) {
          // if (info.card_orders && info.card_orders.length)
          return true;
        } else {
          return info.card_orders && info.card_orders.length
        }
      },
      isNotified (info) {
        return info.api_info && info.api_info.notify_ret === 'success';
      }
    }
  }
</script>

<style lang="scss">
  .order-info-container {
    .el-dialog {
      width: 100% !important;
      max-width: 560px !important;
    }
  }

  .order-info-table {
    overflow: auto;
    font-size: 14px;
    color: #606266;
    table {
      width: 100%;
      th, td {
        padding: 0 4px;
      }
    }
    table, table tr th, table tr td {
      border: 1px solid #ebeef5;
      border-collapse: collapse;
    }
    .el-tag {
      line-height: 22px !important;
      height: 22px !important;
      padding: 0 3px !important;
    }
  }

</style>
