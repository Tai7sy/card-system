import axios from 'axios';
import { MessageBox } from 'element-ui';
import { getToken } from './auth';
import { param, randomString } from './helper'

axios.defaults.withCredentials = true;
window.API_URL = '/api/admin/';
window.API_HOST = location.host;



// 创建axios实例
const service = axios.create({
  baseURL: window.API_URL, // api的base_url
  timeout: 15000, // 请求超时时间
  transformRequest: [data => {
    // Do whatever you want to transform the data
    if (data && data.constructor !== FormData) data = param(data);
    return data;
  }]
});

// request拦截器
service.interceptors.request.use(config => {
  config.headers['X-XSRF-TOKEN'] = randomString(32); // 假装有 csrf
  const token = getToken();
  if (token) config.headers['Authorization'] = token;
  config.headers['X-Requested-With'] = 'XMLHttpRequest';
  return config;
}, error => {
  // Do something with request error
  console.log(error); // for debug
  Promise.reject(error);
});

// response 拦截器
service.interceptors.response.use(
  response => {
    if (response.headers.authorization) {
      window.$store.commit('SET_TOKEN', response.headers.authorization)
    }
    return response.data
  },
  error => {
    console.log(error)
    let message = error.message;
    if (error.response) {
      if (error.response.status === 401) {
        const $router = window.$router;
        const $route = $router.app.$route;
        if ($route.name !== '登录') {
          console.warn('fetch 401, go login...');
          window.$store.dispatch('Logout').then(() => {
            location.reload();// 为了重新实例化vue-router对象 避免bug
          });
        }
        return Promise.reject(error)
      }
      const ret = error.response.data;
      if (ret.exception) {
        message = ret.exception
        if (ret.message) {
          message += '<br>' + ret.message
        }
      } else if (ret.errors) {
        message = Object.values(ret.errors).map(e => e[0]).join('<br>')
      } else if (ret.message) {
        message = ret.message
      }
    } else {
      console.error('fetch.error:');
      console.error(error)
    }

    if(!(error.config && error.config.no_error)){
      MessageBox({
        message,
        title: '提示',
        type: 'error',
        dangerouslyUseHTMLString: true
      });
    }

    return Promise.reject({
      message,
      data: (error.response && error.response.data) || null
    });
  }
);

export default service;
