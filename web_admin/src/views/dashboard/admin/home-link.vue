<template>
  <el-card class="my-card home-link-card">
    <div class="home-link">
      <div class="header"><span class="header-title">常用功能</span></div>
      <div class="functions">
        <router-link class="function" to="/m/shop/info">店铺信息</router-link>
        <router-link class="function" to="/m/shop/link">店铺链接</router-link>

        <router-link class="function" to="/m/product/category">商品分类</router-link>
        <router-link class="function" to="/m/product/list">商品列表</router-link>

        <router-link class="function" to="/m/order/list">订单记录</router-link>
        <router-link class="function" to="/m/order/analysis">收益统计</router-link>

        <router-link class="function" to="/m/shop/rate">通道费率</router-link>
        <router-link class="function" to="/m/api/pay">API接入</router-link>

      </div>
    </div>
  </el-card>
</template>

<script>
  export default {
    name: 'HomeLink',
    props: {
      userInfo: {}
    },
    data () {
      return {}
    }
  }
</script>

<style lang="scss">
  .home-link-card {
    .el-card__body {
      box-sizing: border-box;
      max-height: 100%;
      overflow-x: hidden;
      overflow-y: auto;
    }
  }
</style>

<style lang="scss" scoped>

  .home-link {
    margin: 10px;

    & > .header {
      font-weight: bold;

      & > .header-title {
        font-size: 17px;
        color: #333;
        line-height: 25px;
      }
    }

    & > .functions {
      display: block;
      margin: 12px -6px 0;

      & > .function {
        margin: 12px 6px 0;
        display: inline-block;
        width: 120px;
        height: 36px;
        background: #f5f6fa;
        border-radius: 4px;
        font-size: 14px;
        color: #333;
        text-align: center;
        line-height: 36px;
        transition: color .3s ease;

        &:hover {
          color: #409EFF;
        }
      }
    }
  }
</style>
