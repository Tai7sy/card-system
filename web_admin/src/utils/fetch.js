import axios from 'axios';
import { Message, MessageBox } from 'element-ui';
import store from '../store';
import $ from 'jquery'
import { randomString } from '@/utils/helper'

axios.defaults.withCredentials = true;

window.$ = $;
window.jQuery = $;

let HOST = '/api/admin/';
if (process.env.NODE_ENV === 'development') {
  HOST = 'http://127.0.0.2/api/admin/'
}
window.API_HOST = HOST;
// 创建axios实例
const service = axios.create({
  baseURL: HOST, // api的base_url
  timeout: 15000, // 请求超时时间
  transformRequest: [data => {
    // Do whatever you want to transform the data
    if (data && data.constructor !== FormData) data = $.param(data);
    return data;
  }]
});

// request拦截器
service.interceptors.request.use(config => {
  if (store.getters.token) {
    config.headers['X-XSRF-TOKEN'] = randomString(32); // 假装有 csrf
    config.headers['X-Requested-With'] = 'XMLHttpRequest';
  }
  return config;
}, error => {
  // Do something with request error
  console.log(error); // for debug
  Promise.reject(error);
});

// response 拦截器
service.interceptors.response.use(
  response => {
    const res = response.data;
    if (!res.hasOwnProperty('message') && !res.hasOwnProperty('data')) {
      console.error('fetch.response.data:');
      console.error(res);
      Message({
        message: '系统繁忙，请稍后再试',
        type: 'error',
        duration: 3000,
        showClose: true
      });
    }
    return response.data
  },
  error => {
    let message = error.message;
    if (error.response) {
      if (error.response.status === 401) {
        const $router = window.$router;
        const $route = $router.app.$route;
        if ($route.name !== '登录') {
          console.warn('fetch 401, go login...');
          store.dispatch('Logout').then(() => {
            location.reload();// 为了重新实例化vue-router对象 避免bug
          });
        }
        return Promise.reject(error)
      }
      const ret = error.response.data;
      message = ret.message || ret.exception || message;
      if (ret.errors) {
        message += '</br>' + JSON.stringify(ret.errors)
      }
    } else {
      console.error('fetch.error:');
      console.error(error)
    }
    Message({
      message,
      type: 'error',
      duration: 3000,
      showClose: true,
      dangerouslyUseHTMLString: true
    });
    return Promise.reject(error);
  }
);

export default service;
