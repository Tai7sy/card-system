// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from './utils/fetch'
import './utils/filters'

import MuseUI from 'muse-ui'
import 'muse-ui/dist/muse-ui.css'
import './assets/css/theme.less';
import './assets/css/styles.css';
import './assets/css/mu_reset.css';
import './assets/css/material_icons.css';
import './assets/css/material-color.less';
import Promise from 'es6-promise'
Promise.polyfill()
Vue.use(MuseUI)
Vue.prototype.$http = axios;
Vue.config.productionTip = false
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  render (h) {
    window.vm = this;
    return h(App)
  }
})
