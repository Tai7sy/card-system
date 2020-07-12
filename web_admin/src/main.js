import Vue from 'vue'
import router from '@/router'
import store from '@/store'
import axios from '~/utils/fetch'
import Icon from '~/utils/font-awesome'
import VueClipboards from 'vue-clipboards';
import './element-ui-loader.js'
import NProgress from 'nprogress'; // Progress 进度条
import 'nprogress/nprogress.css'; // Progress 进度条 样式
import 'normalize.css/normalize.css'; // normalize.css 样式格式化
import 'quill/dist/quill.snow.css'
import errLog from '@/store/errLog'; // error log组件
import '~/utils/helper'
import '~/utils/filters'
import '~/utils/trick'
import App from './App.vue' // 一定要晚于 前面的 自定义样式要最后加载

Vue.config.productionTip = false;
Vue.prototype.$http = axios;
Vue.use(VueClipboards);
Vue.component('icon', Icon);

window.Vue = Vue;
window.Router = require('vue-router');
window.Vuex = require('vuex');
window.$http = axios;
window.$router = router;
window.$store = store;
window.plugins = [];
window.NProgress = NProgress;


const whiteList = ['404', 'auth.login']; // 不重定向白名单
router.beforeEach((to, from, next) => {
  NProgress.start();
  if (store.state.user.token) {
    if (to.name === 'auth.login') {
      next({ name: '概览' });
    } else {
      if (!store.getters.userInfo || !store.getters.userInfo.id) {
        // 没有登录过的状态, 则获取一下登录信息
        store.dispatch('GetInfo').then(res => {
          store.dispatch('GenerateRoutes', res).then(() => {
            router.addRoutes(store.getters.addRouters);
            // console.log('router.addRoutes ok');
            next({ ...to }); // hack方法 确保addRoutes已完成
          })
        }).catch(e => {
          console.log('get userInfo failed, redirect to login page', e);
          store.commit('SET_TOKEN', null);
          router.push({ name: 'auth.login' });
        })
      } else {
        next();
      }
    }
  } else {
    if (whiteList.indexOf(to.name) !== -1) {
      next()
    } else {
      next({ name: 'auth.login', params: { to: to.path } });
      NProgress.done();
    }
  }
});

router.afterEach(e => {
  if (e.meta && e.meta.title) {
    document.title = e.meta.title;
  } else {
    document.title = e.name;
  }
  NProgress.done();
});

// 生产环境错误日志
if (process.env.NODE_ENV === 'production') {
  Vue.config.errorHandler = function (err, vm) {
    console.log(err, window.location.href);
    errLog.pushLog({
      err,
      url: window.location.href,
      vm
    })
  };
}

window.bus = new Vue();

new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
});

try {
  document.getElementById('progress').parentNode.removeChild(document.getElementById('progress'));
} catch (e) {
  console.warn(e);
}
