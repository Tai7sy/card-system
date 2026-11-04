<template>
  <div>
    <div v-if="IE9" style="text-align: center">
      <!-- if [ $ le ie9 ] (transition not supported) -->
      <p style="color: red">
        您正在使用不受支持的浏览器，请更新至最新浏览器。
      </p>
      <p>推荐浏览器：
        <a href="https://www.google.cn/intl/zh-CN_ALL/chrome/" target="_blank">Chrome</a>&nbsp;&nbsp;
        <a href="https://www.mozilla.org/zh-CN/firefox/" target="_blank">FireFox</a>&nbsp;&nbsp;
        <a href="http://browser.qq.com/" target="_blank">
          <del>QQ浏览器</del>
        </a>
      </p>
    </div>
    <layout/>

    <!-- 全局共享的 alert -->
    <alert ref="alert"/>
    <!-- 全局共享的 captcha -->
    <captcha ref="captcha" v-model="captcha.value" :driver="captcha.driver" mode="popup" :config="captcha.config"/>
  </div>
</template>
<script>
  import Vue from 'vue'


  import Layout from "@/views/Layout"
  import Alert from '@/components/alert'
  import Captcha from '~/components/Captcha'

  export default {
    components: { Layout, Alert, Captcha },
    data() {
      return {
        IE9: document.createElement('div').style.transition === undefined,
        config: window.config,
        captcha: {
          value: null,
          ...window.config.captcha
        },
      }
    },
    beforeMount() {
      Vue.prototype.$alert = this.alert;
      Vue.prototype.$captcha = {
        loading: false
      };
      window.$alert = this.alert;
    },
    mounted() {
      Vue.prototype.$captcha = this.$refs['captcha'];
    },
    methods: {
      alert(message, title = '提示') {
        let options = {};
        if (typeof message === 'object') {
          options = message;
        } else {
          options = { message, title };
        }
        return new Promise((resolve, reject) => {
          this.$refs.alert.show({
            ...options,
            successCallback: resolve,
            cancelCallback: reject
          })
        })
      },
    }
  }
</script>
