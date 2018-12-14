import fetch from '../utils/fetch';


export function info (data) {
  return fetch({
    url: 'system/info',
    method: data ? 'post' : 'get',
    data
  });
}

export function theme (data) {
  return fetch({
    url: 'system/theme',
    method: data ? 'post' : 'get',
    data
  });
}


export function order (data) {
  return fetch({
    url: 'system/order',
    method: data ? 'post' : 'get',
    data
  });
}

export function vcode (data) {
  return fetch({
    url: 'system/vcode',
    method: data ? 'post' : 'get',
    data
  });
}

export function email (data) {
  return fetch({
    url: 'system/email',
    method: data ? 'post' : 'get',
    data
  });
}

export function security (data) {
  return fetch({
    url: 'system/security',
    method: data ? 'post' : 'get',
    data
  });
}

export function storage (data) {
  return fetch({
    url: 'system/storage',
    method: data ? 'post' : 'get',
    data
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

