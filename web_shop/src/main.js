import Vue from 'vue'
import App from './App'
import axios from '@/utils/fetch'
import './muse-ui-loader'
import '~/utils/filters'
import '~/utils/helper'
import './assets/css/animation.scss';
import './assets/css/styles.scss';
import './assets/css/mu_reset.scss';
import './assets/css/material_icons.scss';
import './assets/iconfont/iconfont.css'
import '~/styles/material-color.scss';
import '@/assets/css/quill.core.min.css' // core quill
import FakeElInput from "@/components/ElInput";
import FakeElButton from "@/components/ElButton";
window.$http = axios;
Vue.prototype.$http = axios;
Vue.config.productionTip = false;
Vue.component('ElInput', FakeElInput)
Vue.component('ElButton', FakeElButton)

new Vue({
  el: '#app',
  render (h) {
    window.vm = this;
    return h(App)
  }
});

try {
  document.getElementById('progress').parentNode.removeChild(document.getElementById('progress'));
} catch (e) {
  console.warn(e);
}

