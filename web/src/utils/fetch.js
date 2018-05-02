import axios from 'axios';

import $ from 'jquery'

window.$ = $;
window.jQuery = $;
let HOST = 'api/'
if (process.env.NODE_ENV === 'development') {
  HOST = 'http://127.0.0.4/api/'
}
axios.defaults.withCredentials = true
// 创建axios实例
const service = axios.create({
  baseURL: HOST, // api的base_url
  timeout: 15000,                  // 请求超时时间
  transformRequest: [data => {
    // Do whatever you want to transform the data
    if (data) data = $.param(data);
    return data;
  }]
});

// request拦截器
service.interceptors.request.use(config => {
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
    const res = response.data;
    if (!res.hasOwnProperty('code')) {
      console.log('response.data:\n');
      console.log(res);
      return window.vm.$emit('alert', {
        title: '提示',
        msg: '系统繁忙，请稍后再试'
      })
    }

    if (res.code !== 0) {
      window.vm.$emit('alert', {
        title: '提示',
        msg: res.msg || '未知错误'
      })
      return Promise.reject(res.msg || 'undefined error');
    } else {
      return response.data;
    }
  },
  error => {
    console.log('err' + error);// for debug
    let msg = '未知错误'

    if (error.data && error.data.message) {
      msg = error.data.message
    } else if (error.response && error.response.data && error.response.data.message) {
      msg = error.response.data.message
    } else if (error.message) {
      msg = error.message
    }
    window.vm.$emit('alert', {
      title: '错误',
      msg: msg
    })
    return Promise.reject(error);
  }
);

export default service;
