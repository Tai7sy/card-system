<template>
  <div class="app-container">
    <el-tabs v-model="activeTab">

      <el-tab-pane label="网站" name="info">
        <set-info v-if="initialized.info"/>
      </el-tab-pane>

      <el-tab-pane label="主题" name="theme">
        <set-theme v-if="initialized.theme"/>
      </el-tab-pane>

      <el-tab-pane label="订单" name="order">
        <set-order v-if="initialized.order"/>
      </el-tab-pane>

      <el-tab-pane label="邮件" name="email">
        <set-email v-if="initialized.email"/>
      </el-tab-pane>

      <el-tab-pane label="短信" name="sms">
        <set-sms v-if="initialized.sms"/>
      </el-tab-pane>

      <el-tab-pane label="验证码" name="vcode">
        <set-vcode v-if="initialized.vcode"/>
      </el-tab-pane>

      <el-tab-pane label="文件储存" name="storage">
        <set-storage v-if="initialized.storage"/>
      </el-tab-pane>

      <el-tab-pane label="数据清理" name="clean">
        <set-clean v-if="initialized.clean"/>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
  import NProgress from 'nprogress'
  import setInfo from './_info'
  import setTheme from './_theme'
  import setEmail from './_email.vue'
  import setSms from './_sms.vue'
  import setOrder from './_order'
  import setVcode from './_vcode'
  import setStorage from './_storage'
  import setClean from './_clean'

  const const_init = {
    info: false,
    theme: false,
    email: false,
    sms: false,
    order: false,
    vcode: false,
    security: false,
    storage: false,
    clean: false,
    other: false,
  };
  export default {
    components: {
      setInfo,
      setTheme,
      setEmail,
      setSms,
      setOrder,
      setVcode,
      setStorage,
      setClean,
    },
    data () {
      const activeTab = this.$route.query.tab || 'info';
      const initialized = Object.assign({}, const_init);
      initialized[activeTab] = true;
      return {
        loading: false,
        activeTab,
        initialized
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
