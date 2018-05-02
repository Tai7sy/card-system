<template>
  <div class="content">

    <div style="width: 100%;">
      <mu-text-field name="email" type="email" label="收货邮箱" hintText="请输入收货邮箱" v-model="email"
                     style="width: calc(100% - 100px);margin-right: 6px"/>
      <mu-raised-button label="查询" @click="handleSearch" style="width: 88px"/>
    </div>

    <mu-circular-progress v-if="process" :size="40" style="width: 100%; text-align: center"/>

    <mu-table ref="table" :showCheckbox="false" :selectable="false" v-show="!process && list.length>0">
      <mu-thead>
        <mu-tr>
          <mu-th style="width: 160px">订单</mu-th>
          <mu-th>商品名称</mu-th>
          <mu-th style="width: 100px">数目/金额</mu-th>
          <mu-th style="width: 100px">卡密</mu-th>
        </mu-tr>
      </mu-thead>
      <mu-tbody>
        <mu-tr v-for="item in list" :key="item.id">
          <mu-td>{{item.order_no}}</mu-td>
          <mu-td class="over-ellipsis">{{item.good.name}}</mu-td>
          <mu-td>{{item.count}}&nbsp;({{item.amount | moneyFilter | moneyFormatterFilter}})</mu-td>

          <mu-td class="over-ellipsis" v-if="item.paid">
            <a class="cursor-pointer" @click="showCardDetail(item.cards)">
              {{item.cards&&item.cards.length?item.cards.length+'张':'无'}}
            </a>
          </mu-td>
          <mu-td class="over-ellipsis" v-else>
            未支付
            <a class="cursor-pointer" @click="handlePay(item.order_no)">支付</a>
          </mu-td>
        </mu-tr>
      </mu-tbody>
    </mu-table>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        process: false,
        email: '',
        list: [],
      }
    },
    methods: {
      handleSearch () {
        this.email = this.email.trim()
        this.process = true
        this.$http.post('shop/record/get', { email: this.email }).then(e => {
          this.list = e.data
          this.process = false
        }).catch(() => {
          this.process = false
        });
      },
      showCardDetail (cards) {
        if (cards && cards.length) this.$alert(cards.map(e => e.card).join('<br>'), '卡密详情');
      },
      handlePay (orderId) {
        window.open('/pay/' + orderId)
      }
    }
  }
</script>

<style lang="less" scoped>
  .content {
    margin: 12px 8px 8px;
  }

</style>
