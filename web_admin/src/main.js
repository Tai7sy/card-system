if (!global._babelPolyfill) {
  require('babel-polyfill');
}
// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import router from './router'
import store from './store'
import axios from './utils/fetch'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import IconSvg from 'vue-awesome/components/Icon.vue'
/* Register component */
import 'vue-awesome/icons' // or import all icons if you don't care about bundle size
import NProgress from 'nprogress'; // Progress 进度条
import VueClipboards from 'vue-clipboards';
import 'nprogress/nprogress.css'; // Progress 进度条 样式
import 'normalize.css/normalize.css'; // normalize.css 样式格式化
import 'quill/dist/quill.snow.css'
import errLog from './store/errLog'; // error log组件
import { getToken, removeToken } from './utils/auth'
import './utils/helper'
import './utils/filters'
import './utils/trick'
import './utils/gt'
import App from './App.vue' // 一定要晚于 前面的 自定义样式要最后加载


Vue.config.productionTip = false;
Vue.prototype.$http = axios;
Vue.use(ElementUI);
Vue.use(VueClipboards);
Vue.component('icon', IconSvg);

window.Vue = Vue;
window.Router = require('vue-router');
window.Vuex = require('vuex');
window.$http = axios;
window.plugins = [];
window.ElementUI = ElementUI;
window.NProgress = NProgress;


const whiteList = ['/admin/login', '/admin/register', '/admin/forget', '/admin/reset']; // 不重定向白名单
router.beforeEach((to, from, next) => {
  NProgress.start();
  if (getToken()) {
    if (to.path === '/admin/login') {
      next({ path: '/admin/dashboard' });
    } else {
      if (store.getters.role.length === 0) {
        store.dispatch('GetInfo').then(res => {
          store.dispatch('GenerateRoutes', res.data).then(() => {
            router.addRoutes(store.getters.addRouters);
            // console.log('router.addRoutes ok');
            next({ ...to }); // hack方法 确保addRoutes已完成
          })
        }).catch(e => {
          console.log('login get info error');
          console.log(e);
          removeToken();
          router.push({ path: '/admin/login' });
        })
      } else {
        next();
      }
    }
  } else {
    if (whiteList.indexOf(to.path) !== -1 || to.path.substr(0, 8) === '/admin/reset') {
      next()
    } else {
      next('/admin/login?to=' + to.path);
      NProgress.done();
    }
  }
});

router.afterEach(e => {
  document.title = e.name;
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
