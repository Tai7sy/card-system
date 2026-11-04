<template>
<div class="app-container">
  <el-tabs v-model="tab">
    <el-tab-pane label="支付渠道" name="list">
      <pay-list v-if="initialized.list"/>
    </el-tab-pane>
    <el-tab-pane label="前台支付" name="shop">
      <pay-way :inbox="true" :type="PAYWAY_TYPE.SHOP" v-if="initialized.shop"/>
    </el-tab-pane>
    
  </el-tabs>
</div>
</template>

<script>
import PayList from "./list/pay-list-index";
import PayWay from "./payway/payway-index";
import { TYPE as PAYWAY_TYPE } from "~/api/payway";
export default {
  props: {},
  components: {
    PayList,
    PayWay
  },
  data() {
    return {
      tab: '',
      PAYWAY_TYPE,
      initialized: {
        list: false,
        shop: false
      }
    };
  },
  watch: {
    tab(val) {
      if (val) {
        this.$route.query.tab = val;
        this.initialized[val] = true;
      }
    }
  },
  beforeMount() {
    if (this.$route.query.tab) {
      this.tab = this.$route.query.tab;
    } else {
      this.tab = 'list';
    }
  }
};
</script>

<style lang="scss" scoped>

</style>