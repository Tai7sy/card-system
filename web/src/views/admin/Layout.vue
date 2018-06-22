<template>
  <div class="main">
    <div v-if="isInQQ" class="qq-mask">
      <p><img src="../../assets/images/qq-mask.png"></p>
      <a href="javascript:void(0)" @click="isInQQ=false">关闭提示</a>
    </div>
    <mu-appbar :zDepth="0" :title="title" class="my-appbar"
               :class="{'nav-hide': !open}">
      <mu-icon-button @click="toggleNav" icon="menu" slot="left"/>
      <mu-flat-button label="登出" @click="handleLogout" slot="left"/>
    </mu-appbar>
    <slider-bar @titleChange="handleTitleChange" @close="toggleNav" :open="open" :docked="docked" :version="version"/>

    <div class="my-content" :class="{'nav-hide': !open}">
      <transition name="slide">
        <router-view></router-view>
      </transition>
    </div>
    <my-alert></my-alert>

  </div>
</template>
<script>
  import sliderBar from '../../components/sliderBar.vue'
  import myAlert from '../../components/alert.vue'
  import myFunc from '../../utils/myfunc.js'
  import Vue from 'vue';

  export default {
    components: {
      'slider-bar': sliderBar,
      'my-alert': myAlert
    },
    data () {
      const desktop = myFunc.isDesktop()
      return {
        version: 1.1,
        accessPass: '',
        open: desktop,
        docked: desktop,
        desktop: desktop,
        title: '',
        isInQQ: false,
        shareDlg: {
          isShow: false
        },
        UpdateDlgIsShow: false
      }
    },
    mounted () {
      myFunc.cookie.set('node', this.proxyNode)
      this.detectBrowser()
      this.navResize()
      window.addEventListener('resize', this.navResize);
      Vue.prototype.$alert = this.alert;
    },
    methods: {
      alert (msg, title, callback, hasCancel) {
        window.vm.$emit('alert', {
          msg: msg,
          title: (title || '提示'),
          hasCancel: hasCancel,
          callback: callback
        })
      },
      toggleNav () {
        this.open = !this.open
      },
      navResize () {
        const desktop = myFunc.isDesktop()
        this.docked = desktop
        if (desktop === this.desktop) return
        if (!desktop && this.desktop && this.open) {
          this.open = false
        }
        if (desktop && !this.desktop && !this.open) {
          this.open = true
        }
        this.desktop = desktop
      },
      handleTitleChange (title) {
        this.title = title
      },
      detectBrowser () {
        if (/MicroMessenger/i.test(navigator.userAgent) || / QQ\//i.test(navigator.userAgent)) {
          this.isInQQ = true
        }
      },
      handleLogout () {
        myFunc.cookie.del('login');
        this.$router.push('/admin/login');
      }
    },
    destroyed () {
      window.removeEventListener('resize', this.navResize)
    }
  }
</script>

<style lang="less" type="text/less">
  .text-container { //隐藏首页的临时渲染内容
    display: none;
    position: absolute;
    left: -1200px
  }

  .qq-mask {
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    z-index: 1000;
    background-color: rgba(0, 0, 0, 0.8);
    filter: alpha(opacity=80);
    p {
      text-align: right;
      margin-top: 5%;
      padding-right: 5%;
      margin-bottom: 0;
    }
    img {
      max-width: 80%;
      height: auto;
    }
    a {
      color: white;
      font-size: 13px;
      float: right;
      margin-right: 10%;
    }
  }

  html, body {
    margin: 0;
    width: 100%;
  }

  .main {
    width: 100%;
  }

  @import "../../../node_modules/muse-ui/src/styles/import.less";
  .my-appbar {
    position: fixed;
    left: 256px;
    right: 0;
    top: 0;
    width: auto;
    transition: all .45s @easeOutFunction;
    &.nav-hide {
      left: 0;
    }
  }

  .my-content {
    height: 100%;
    width: 100%;
    padding-top: 56px;
    padding-left: 256px;
    transition: all .45s @easeOutFunction;
    &.nav-hide {
      padding-left: 0;
    }
  }

  .col {
    padding-top: 0;
  }

  .content-wrapper {
    padding: 36px;
    padding-bottom: 24px !important;
  }

  .col {
    padding-top: 0;
  }

  @media (min-width: 480px) {
    .my-content {
      padding-top: 64px;
    }
  }

  @media (max-width: 993px) {
    .my-appbar {
      left: 0;
    }

    .my-content {
      padding-left: 0;
    }

    .content-wrapper {
      padding: 18px;
    }

    .col {
      padding-top: 12px;
    }
  }

  @media (max-width: 600px) {
    .my-input {
      width: 100%;
    }
  }

</style>
