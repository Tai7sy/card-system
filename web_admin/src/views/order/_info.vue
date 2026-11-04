<template>
<el-dialog :title="title" :visible.sync="orderVisible" :close-on-click-modal="true"
           class="order-info-container" v-loading="loading">
  <div class="order-info-table" v-if="orderInfo">
    <el-collapse v-model="showOrders" ref="orderChain"
                 v-if="orderInfo.retail_chain && orderInfo.retail_chain.length">
      <el-collapse-item v-for="(item,index) in orderInfo.retail_chain" :key="item.order_no"
                        :title="index===0?'供货订单':'分销订单'" :name="item.id">
        <info-item :orderInfo="item" :loading.sync="loading"/>
      </el-collapse-item>
      <el-collapse-item title="分销订单" :name="orderInfo.id">
        <info-item :orderInfo="orderInfo" :loading.sync="loading"/>
      </el-collapse-item>

    </el-collapse>

    <info-item :orderInfo="orderInfo" v-else/>
  </div>
</el-dialog>
</template>

<script>
import { ID_INNER_RETAIL } from '~/api/pay';
import InfoItem from './_into_item';
export default {
  props: {
    from: {
      type: String,
      default: ''
    },
    info: {
      type: Object
    }
  },
  components: {
    InfoItem
  },
  data() {
    return {
      loading: false,
      loadingRetail: false,
      ID_INNER_RETAIL,
      orderInfo: null,
      orderVisible: true,
      title: '',
      showOrders: []
    };
  },
  mounted() {
    this.showOrder(this.info);
  },
  watch: {
    orderVisible(val) {
      if (!val) {
        this.$emit('close');
      }
    }
  },
  methods: {
    showOrder(order) {
      this.orderInfo = order;
      if (this.from === 'complaint' && this.orderInfo.retail_chain && this.orderInfo.retail_chain.length) {
        // 供货商的投诉, 显示自己的订单(第一个)
        this.showOrders = [this.orderInfo.retail_chain[0].id];
      } else {
        //  显示初始订单
        this.showOrders = [this.info.show_order_id || this.info.id];
      }
      const isApiOrder = this.orderInfo.product_id === -1001;
      this.title = isApiOrder ? 'API订单信息' : '订单信息';
    }
  }
};
</script>

<style lang="scss">
  .order-info-container {
    .el-dialog {
      width: 100% !important;
      max-width: 560px !important;
    }

    .el-dialog__body {
      padding-top: 12px;
    }

    .el-collapse-item__header {
      font-weight: bold;
    }
  }

</style>
