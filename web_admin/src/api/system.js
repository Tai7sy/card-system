import fetch from '../utils/fetch';


export function info (params) {
  return fetch({
    url: 'system/info',
    method: params ? 'post' : 'get',
    data: params
  });
}

export function order (params) {
  return fetch({
    url: 'system/order',
    method: params ? 'post' : 'get',
    data: params
  });
}

export function vcode (params) {
  return fetch({
    url: 'system/vcode',
    method: params ? 'post' : 'get',
    data: params
  });
}

export function email (params) {
  return fetch({
    url: 'system/email',
    method: params ? 'post' : 'get',
    data: params
  });
}

export function security (params) {
  return fetch({
    url: 'system/security',
    method: params ? 'post' : 'get',
    data: params
  });
}

export function storage (params) {
  return fetch({
    url: 'system/storage',
    method: params ? 'post' : 'get',
    data: params
  });
}


export function emailTest (to) {
  return fetch({
    url: 'system/email/test',
    method: 'post',
    data: { to }
  });
}

export function orderClean (day) {
  return fetch({
    url: 'system/order/clean',
    method: 'post',
    data: { day }
  });
}

