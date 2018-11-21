<template>
  <div class="app-container">
    <el-tabs v-model="activeTab">

      <el-tab-pane label="网站" name="info">
        <set-info v-if="initialized.info"/>
      </el-tab-pane>

      <el-tab-pane label="订单" name="order">
        <set-order v-if="initialized.order"/>
      </el-tab-pane>

      <el-tab-pane label="邮件" name="email">
        <set-email v-if="initialized.email"/>
      </el-tab-pane>

      <el-tab-pane label="验证码" name="vcode">
        <set-vcode v-if="initialized.vcode"/>
      </el-tab-pane>

      <el-tab-pane label="文件储存" name="storage">
        <set-storage v-if="initialized.storage"/>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import NProgress from 'nprogress'; // Progress 进度条
  import setInfo from './_info'
  import setEmail from './_email.vue'
  import setOrder from './_order'
  import setVcode from './_vcode'
  import setStorage from './_storage'

  const const_init = {
    info: false,
    email: false,
    order: false,
    vcode: false,
    security: false,
    storage: false,
    other: false,
  };
  export default {
    components: {
      setInfo,
      setEmail,
      setOrder,
      setVcode,
      setStorage,
    },
    data () {
      return {
        loading: false,
        activeTab: '',
        initialized: Object.assign({}, const_init)
      }
    },
    watch: {
      loading (val, old) {
        if (val) {
          if (!old && !NProgress.isStarted()) NProgress.start();
        } else {
          NProgress.done();
        }
      },
      activeTab (val) {
        if (NProgress.isStarted()) NProgress.done();
        if (val) {
          this.$route.query.tab = val;
          window.history.pushState({}, 0, this.$route.path + '?tab=' + val);
          this.initialized[val] = true;
        }
      }
    },
    mounted () {
      this.activeTab = this.$route.query.tab || 'info';
    },
    methods: {}
  }
</script>

<style lang="scss">
  .title {
    font-weight: normal;
    margin: 0 0 12px 0;
  }

  .item-container {
    margin-bottom: 8px !important;
    .tip {
      font-size: 12px;
    }
    .el-checkbox {
      display: block;
    }
    .el-checkbox + .el-checkbox {
      margin-left: 0;
    }
    .el-switch {
      span {
        color: #606266 !important;
        font-weight: 500 !important;;
      }
    }
    .el-radio-group {
      display: block !important;
    }
  }
</style>
