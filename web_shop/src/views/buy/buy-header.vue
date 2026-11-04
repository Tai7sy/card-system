<template>
<div>
  
  <mu-card class="my-card white-background"
           v-if="renderDelta(shop.ann, true)">
    <mu-card-text class="shop-info">
      <div><span class="title">{{ '公告：' }}</span></div>
      <div v-html="renderDelta(shop.ann)"></div>
    </mu-card-text>
  </mu-card>
</div>
</template>

<script>
import { renderDelta } from '~/utils/helper';
export default {
  name: "buy-header",
  props: {
    shop: {}
  },
  data() {
    return {};
  },
  mounted() {
    this.$nextTick(() => {
      // waiting for $alert
      if (this.shop.ann_pop && this.shop.ann_pop.length) {
        const msgHtml = this.renderDelta(this.shop.ann_pop, true);
        if (msgHtml) this.$alert({
          message: msgHtml,
          title: '店铺公告',
          scrollable: true
        });
      }
    });
  },
  methods: {
    renderDelta,
    handleHelp() {
      window.open('http://wpa.qq.com/msgrd?v=1&uin=' + this.shop.qq + '&site=fakaxitong.com&menu=yes', '_blank');
    }
  }
};
</script>

<style lang="scss" scoped>
  .mu-card {
    margin-bottom: 12px;
  }

  .shop-info {
    .title {
      display: inline-block;
      margin-right: 4px;
      white-space: nowrap;
    }

    .qq {
      color: rgba(0, 0, 0, 0.87);
    }

    .mu-chip {
      float: right;
      font-size: 12px;
      line-height: 24px;
    }
  }
</style>
