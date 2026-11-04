import axios from 'axios';
import { param, randomString } from '~/utils/helper'

axios.defaults.withCredentials = true;
const HOST = '/api/shop/';
window.API_HOST = location.host;



// 创建axios实例
const service = axios.create({
  baseURL: HOST, // api的base_url
  timeout: 15000,                  // 请求超时时间
  transformRequest: [data => {
    // Do whatever you want to transform the data
    if (data && data.constructor !== window['FormData']) data = param(data);
    return data;
  }]
});

// request拦截器
service.interceptors.request.use(config => {
  config.headers['X-XSRF-TOKEN'] = randomString(32); // 假装有 csrf
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
    return response.data
  },
  error => {
    let message = error.message;
    if (error.response) {
      const ret = error.response.data;
      if (ret.exception) {
        message = ret.exception;
        if (ret.message) {
          message += '<br>' + ret.message
        }
      }else if (ret.errors) {
        message = Object.values(ret.errors).map(e => e[0]).join('<br>')
      }else if (ret.message) {
        message = ret.message
      }
    } else {
      console.error('fetch.error:');
      console.error(error)
    }
    window.vm.$emit('alert', {
      title: '错误',
      message,
      data: (error.response && error.response.data) || null
    });
    return Promise.reject(error);
  }
);

export default service;
