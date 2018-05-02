<template>
  <div class="content-wrapper">

    <mu-dialog :open="!!process" title="提示">
      <mu-circular-progress :size="40" style="vertical-align:middle;"/>
      <span v-html="process"
            style="line-height: 40px;display:inline-block;margin-left: 24px"></span>
    </mu-dialog>

    <div class="table-title">
      <mu-float-button icon="refresh" mini class="float-right" @click="getList" style="margin-right: 8px"/>
    </div>

    <div class="table-container">
      <mu-table ref="table" :showCheckbox="false" :selectable="false">
        <mu-thead>
          <mu-tr>
            <mu-th style="width: 70px">ID</mu-th>
            <mu-th style="width: 170px">单号</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 120px">商品名称</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 250px">发货邮箱</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 160px">支付方式</mu-th>
            <mu-th style="width: 250px">外部单号</mu-th>
            <mu-th></mu-th>
            <mu-th style="width: 80px">价格</mu-th>
            <mu-th style="width: 80px">状态</mu-th>
            <mu-th style="width: 80px">卡密</mu-th>
            <mu-th style="width: 80px">操作</mu-th>
          </mu-tr>
        </mu-thead>
        <mu-tbody>
          <mu-tr v-for="item in list.data" :key="item.id">
            <mu-td>{{item.id}}</mu-td>
            <mu-td class="over-ellipsis">{{item.order_no}}</mu-td>
            <mu-td></mu-td>
            <mu-td class="over-ellipsis">{{item.good?item.good.name:('未知商品: '+item.good_id+'(可能已删除)')}}</mu-td>
            <mu-td></mu-td>

            <mu-td class="over-ellipsis" :class="{'amber-text':item.paid && !item.email_sent}">
              {{item.email}}&nbsp;{{item.email_sent?'(已发送)':item.paid?'&nbsp;(未发送)':''}}
            </mu-td>
            <mu-td></mu-td>

            <mu-td class="over-ellipsis">{{item.pay?item.pay.name:''}}</mu-td>
            <mu-td class="over-ellipsis">{{item.pay_trade_no}}</mu-td>
            <mu-td></mu-td>

            <mu-td>{{item.amount | moneyFilter | moneyFormatterFilter}}</mu-td>
            <mu-td>{{item.paid?'已支付':'未支付'}}</mu-td>
            <mu-td class="over-ellipsis">
              <a class="cursor-pointer" @click="showCardDetail(item.cards)">
                {{item.cards&&item.cards.length?item.cards.length+'张':'无'}}
              </a>
            </mu-td>

            <mu-td>
              <a class="cursor-pointer" @click="handleDelete(item)">删除</a>&nbsp;
            </mu-td>
          </mu-tr>
        </mu-tbody>
      </mu-table>
      <mu-pagination :total="list.total" :current="list.current_page" :pageSize="list.per_page"
                     :showSizeChanger='true' @pageSizeChange="handlePageSizeChange"
                     @pageChange="handlePageChange" style="float: right; margin-top: 12px">
      </mu-pagination>
    </div>


  </div>
</template>

<script>
  import myFunc from '../../../utils/myfunc.js'

  export default {
    data () {
      return {
        process: false,
        list: {
          current_page: 1,
          per_page: 10,
          total: 0,
          data: []
        }
      }
    },
    mounted () {
      this.checkLogin();
      this.getList();
    },
    methods: {
      checkLogin () {
        if (!myFunc.cookie.get('login')) {
          this.$router.push('/admin/login');
        }
      },
      getList () {
        this.process = '加载中...'
        this.$http.post('admin/order/get', {
          page: this.list.current_page,
          size: this.list.per_page
        }).then(e => {
          e.data.per_page = parseInt(e.data.per_page);
          this.list = e.data
          this.process = false
        }).catch(() => {
          this.process = false
        });
      },
      handlePageChange (v) {
        this.list.current_page = v;
        this.getList()
      },
      handlePageSizeChange (v) {
        this.list.per_page = v;
        this.getList()
      },
      handleDelete (item) {
        if (confirm('是否删除?')) {
          this.$http.post('admin/order/delete', {
            id: item.id
          }).then(() => {
            this.getList()
          })
        }
      },
      showCardDetail (cards) {
        if (cards && cards.length) this.$alert(cards.map(e => e.card).join('<br>'), '卡密详情');
      }
    }
  }
</script>
