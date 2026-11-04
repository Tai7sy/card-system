<template>
  <table border="0" cellspacing="0">
    <tbody>
    <tr>
      <td class="title">订单号</td>
      <td class="content-full" colspan="3">{{orderInfo.order_no}}</td>
    </tr>
    <tr>
      <td class="title">商品名称</td>
      <td class="content-full" colspan="3">{{ isApiOrder ? orderInfo.api_info.subject : (orderInfo.product_name ||
        (orderInfo.product?orderInfo.product.name:'未知#'+orderInfo.product_id))}}
        <el-tag class="el-tag-label" v-if="orderInfo.product && orderInfo.product.retail_from_id">分销商品</el-tag>
      </td>
    </tr>
    <tr>
      <td class="title">数量</td>
      <td class="content-half">{{orderInfo.count}}</td>
      <td class="title">商品售价</td>
      <td class="content-half">{{orderInfo.price | moneyFilter}}元</td>
    </tr>
    <tr>
      <td class="title">创建时间</td>
      <td class="content-half">{{orderInfo.created_at}}</td>
      <td class="title">优惠金额</td>
      <td class="content-half">{{orderInfo.discount | moneyFilter}}元</td>
    </tr>
    <tr>
      <td class="title">创建IP</td>
      <td class="content-half">{{orderInfo.ip}}</td>
      <td class="title">手续费</td>
      <td class="content-half">{{orderInfo.fee | moneyFilter}}元&nbsp;
        <span v-if="orderInfo.fee_type === FEE_TYPE_CUSTOMER" style="font-size: 12px">(买家承担)</span>
      </td>
    </tr>
    <tr>
      <td class="title">付款方式</td>
      <td class="content-half">
        {{orderInfo.pay?orderInfo.pay.name:(orderInfo.pay_id === ID_INNER_RETAIL ? '代理进货' :'未知#'+orderInfo.pay_id)}}
        <a class="a-btn"
           :onclick="`bus.$emit('${identifier}:show:pay')`"
           v-if="orderInfo.pay_id > 0 && orderInfo.status > ORDER_STATUS.UN_PAY.id">查看</a>
      </td>
      <td class="title">{{orderInfo.status > ORDER_STATUS.UN_PAY.id ? '支付': '提交'}}金额</td>
      <td class="content-half">{{orderInfo.paid | moneyFilter}}元&nbsp;
        <span v-if="orderInfo.sms_price" style="font-size: 12px">(短信费￥{{(orderInfo.sms_price/100).toFixed(2)}})</span>
      </td>
    </tr>
    <tr>
      <td class="title">付款时间</td>
      <td class="content-half">{{orderInfo.paid_at}}</td>
      <td class="title">{{ (orderInfo.product && orderInfo.product.retail_from_id) ? '进货成本' : '成本' }}</td>
      <td class="content-half">{{orderInfo.cost | moneyFilter}}元</td>
    </tr>
    <tr>
      <td class="title" v-if="!isApiOrder">联系方式</td>
      <td class="content-half" style="word-wrap: break-word;line-height: 1;" v-if="!isApiOrder">
        {{orderInfo.contact}}
      </td>
      <td class="title" v-if="isApiOrder">通知状态</td>
      <td class="content-half" style="word-wrap: break-word;line-height: 1;" v-if="isApiOrder">
        <div v-if="orderInfo.status !== ORDER_STATUS.UN_PAY.id">
           <span :class="'el-tag el-tag--' + (isNotified(orderInfo)?'success':'warning')">
              {{isNotified(orderInfo) ? '已通知' : '通知失败'}}
            </span>&nbsp;&nbsp;
          <a class="a-btn"
             :onclick="`bus.$emit('${identifier}:send:notify')`"
             v-if="!isNotified(orderInfo)">重发通知</a>
        </div>
        <div v-else>
          <span></span>
        </div>
      </td>
      <td class="title">入账</td>
      <td class="content-half">{{orderInfo.income | moneyFilter}}元</td>
    </tr>
    <tr>
      <td class="title">订单状态</td>
      <td class="content-half">
        <span :class="'el-tag el-tag--' + ORDER_STATUS.get(orderInfo.status).type">
          {{ORDER_STATUS.get(orderInfo.status).name}}
        </span>&nbsp;
        <div style="display: inline" v-if="!isApiOrder">
          <span :class="isShipped(orderInfo) ?'green-text':'amber-text'">
            {{isShipped(orderInfo) ? '(已发货)' : `${renderOrderSendStatus(orderInfo)}`}}
          </span>
          <a class="a-btn"
             :onclick="`bus.$emit('${identifier}:send:card')`"
             v-if="!isShipped(orderInfo) && orderInfo.status !== ORDER_STATUS.UN_PAY.id">发货</a>
          <a class="a-btn"
             :onclick="`bus.$emit('${identifier}:show:card')`"
             v-if="isShipped(orderInfo)">查看</a>
        </div>
      </td>
      <td class="title">净利润</td>
      <td class="content-half">{{orderInfo.paid - orderInfo.sms_price - orderInfo.fee - orderInfo.cost |
        moneyFilter}}元
      </td>
    </tr>
    <tr v-if="orderInfo.pay_id !== ID_INNER_RETAIL && orderInfo.status === ORDER_STATUS.REFUND.id">
      <td class="title">退款提交</td>
      <td :class="orderInfo.refund_at ? 'content-half': 'content-full'">
        {{orderInfo.refund_at || '无提交记录'}}
        <a class="a-btn" v-if="!orderInfo.refund_at"
           :onclick="`bus.$emit('${identifier}:refund:submit')`">提交</a>
      </td>
      <td class="title" v-if="orderInfo.refund_at">退款金额</td>
      <td class="content-half" v-if="orderInfo.refund_at">{{orderInfo.refund | moneyFilter}}元</td>
    </tr>
    <tr v-if="isApiOrder">
      <td class="title">通知地址</td>
      <td class="content-full" colspan="3">{{orderInfo.api_info.notify_url}}</td>
    </tr>
    <tr v-if="isApiOrder">
      <td class="title">返回地址</td>
      <td class="content-full" colspan="3">{{orderInfo.api_info.return_url}}</td>
    </tr>
    <tr>
      <td class="title">备注</td>
      <td class="content-full" colspan="3">{{orderInfo.remark}}</td>
    </tr>
    <tr>
      <td class="title">附加信息</td>
      <td class="content-full" colspan="3">
        <div v-if="orderInfo.contact_ext && orderInfo.contact_ext !== '{}'">
          {{orderInfo.contact_ext}}
        </div>
      </td>
    </tr>
    <tr v-if="orderInfo.status===ORDER_STATUS.FROZEN.id">
      <td class="title">冻结原因</td>
      <td class="content-full" colspan="3">
        {{orderInfo.frozen_reason || '无'}}
      </td>
    </tr>
    </tbody>
  </table>
</template>

<script>
  import { notify, ORDER_STATUS, SEND_STATUS, ship, refundSubmit } from '~/api/order'
  import { DELIVERY } from '~/api/product'
  import { ID_INNER_RETAIL } from '~/api/pay'
  import { htmlspecialchars } from '~/utils/helper'

  /** @var int 卖家承担 */
  const FEE_TYPE_MERCHANT = 0;
  /** @var int 买家承担 */
  const FEE_TYPE_CUSTOMER = 1;

  export default {
    props: {
      orderInfo: {}
    },
    data () {
      const isApiOrder = this.orderInfo.product_id === -1001;
      return {
        loading: false,
        ORDER_STATUS,
        ID_INNER_RETAIL,
        FEE_TYPE_MERCHANT,
        FEE_TYPE_CUSTOMER,
        cardInfo: null,
        isApiOrder,
        identifier: 'orderInfo_' + Math.random()
      }
    },
    watch: {
      loading (val) {
        this.$emit('update:loading', val);
      }
    },
    mounted () {
      this._on_show_card = () => {
        if (this.orderInfo) {
          this.$msgbox({
            title: '订单卡密信息',
            message: '<textarea rows="6" style="width: 100%" readonly>'
              + htmlspecialchars(this.orderInfo.card_orders.map(e => e.card ? e.card.card : '未知卡密#' + e.card_id).join('\n')) + '</textarea>',
            dangerouslyUseHTMLString: true
          });
        }
      };
      window.bus.$on(this.identifier + ':show:card', this._on_show_card);

      this._on_show_pay = () => {
        if (this.orderInfo) {
          const payName = this.orderInfo.pay ? this.orderInfo.pay.name : '支付方式#' + this.orderInfo.pay_id;
          const payInfo = '支付方式: ' + payName
            + '<br>支付时间: ' + this.orderInfo.paid_at + '<br>'
            + payName + '单号(流水号): ' + htmlspecialchars(this.orderInfo.pay_trade_no);
          this.$msgbox({
            title: '支付信息',
            message: payInfo,
            dangerouslyUseHTMLString: true
          });
        }
      };
      window.bus.$on(this.identifier + ':show:pay', this._on_show_pay);

      // 分销任何一层都可以对未发货订单重新发货
      this._on_send_card = () => {
        if (this.orderInfo) {
          if (!this.orderInfo.product) {
            return this.$msgbox('订单对应商品不存在, 无法发货');
          }
          let promise = null;
          if (this.orderInfo.product && this.orderInfo.product.delivery === DELIVERY.AUTO) {
            promise = this.$confirm(`当前订单状态为<${ORDER_STATUS.get(this.orderInfo.status).name}>, 是否确认发货?`, '询问');
          } else {
            promise = this.$prompt(`当前订单状态为<${ORDER_STATUS.get(this.orderInfo.status).name}>, 是否确认发货?<br>请输入发货卡密`, '提示', {
              dangerouslyUseHTMLString: true,
              confirmButtonText: '确认',
              cancelButtonText: '取消',
              inputPattern: /^[\s\S]{1,255}$/,
              inputErrorMessage: '需1-255字符以内',
              inputType: 'text',
              closeOnClickModal: false
            });
          }
          promise.then(({ value }) => {
            this.loading = true;
            ship(this.orderInfo.id, value).then(res => {
              Object.assign(this.orderInfo, res.data);
              this.$notify({ title: '发货成功', message: '当前订单已发货成功', type: 'success', duration: 3000 });
            }).finally(() => {
              this.loading = false;
            });
          });
        }
      };
      window.bus.$on(this.identifier + ':send:card', this._on_send_card); // 发货

      this._on_send_notify = () => {
        if (this.orderInfo) {
          this.loading = true;
          notify(this.orderInfo.id).then(res => {
            Object.assign(this.orderInfo.api_info, res.data.api_info);
            this.$msgbox({
              title: '重发结果',
              message: res.data.notify_html,
              dangerouslyUseHTMLString: false
            });
          }).finally(() => {
            this.loading = false;
          });
        }
      };
      window.bus.$on(this.identifier + ':send:notify', this._on_send_notify); // 重发通知(API)


      this._on_refund_submit = () => {
        if (this.orderInfo) {
          this.$confirm('是否将退款提交至对应的支付渠道', '询问', { type: 'warning' }).then(() => {
            this.loading = true;
            refundSubmit(this.orderInfo.id).then(res => {
              Object.assign(this.orderInfo, res.data);
              this.$msgbox({
                title: '成功',
                message: '退款请求已经提交到支付渠道'
              });
            }).finally(() => {
              this.loading = false;
            });
          });
        }
      };
      window.bus.$on(this.identifier + ':refund:submit', this._on_refund_submit); // 提交退款
    },
    beforeDestroy () {
      window.bus.$off(this.identifier + ':show:card', this._on_show_card);
      window.bus.$off(this.identifier + ':show:pay', this._on_show_pay);
      window.bus.$off(this.identifier + ':send:card', this._on_send_card);
      window.bus.$off(this.identifier + ':send:notify', this._on_send_notify);
      window.bus.$off(this.identifier + ':refund:submit', this._on_send_notify);
    },
    methods: {
      isShipped (info) {
        if (info.status === ORDER_STATUS.SUCCESS.id) {
          
          return true;
        } else {
          return info.card_orders && info.card_orders.length
        }
      },
      isNotified (info) {
        return info.api_info && info.api_info.notify_ret === 'success';
      },
      renderOrderSendStatus (order) {
        if (order.status === ORDER_STATUS.UN_PAY.id) {
          // 未支付的什么都不显示
          return '';
        }
        if (order.status === ORDER_STATUS.PAID.id) {
          // 已支付未发货的渲染状态
          if (order.send_status === SEND_STATUS.CARD_UN.id)
            return '待发货';
          if (order.send_status === SEND_STATUS.CARD_PROCESSING.id)
            return '正在处理';
          if (order.send_status === SEND_STATUS.CARD_SUCCESS.id)
            return '已发货';
          if (order.send_status === SEND_STATUS.CARD_FAILED.id)
            return '发货失败';

          return '未发货';
        }
        return 'UNKNOWN';
      }
    }
  }
</script>

<style lang="scss">
  .order-info-table {
    overflow: auto;
    font-size: 14px;
    color: #606266;

    table {
      width: 100%;

      th, td {
        padding: 0 4px;
        height: 24px;
      }

      td.title {
        white-space: nowrap;
        text-align: right;
        width: 80px;
      }

      td.content-half {
        white-space: nowrap;
        min-width: 90px;
        max-width: 280px
      }

      td.content-full {
        min-width: 160px;
        word-wrap: break-word;
        line-height: 1;
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

    .a-btn {
      text-decoration: underline;
      white-space: nowrap;
      cursor: pointer;
      color: #409EFF
    }
  }
</style>
