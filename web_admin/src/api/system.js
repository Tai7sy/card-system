import fetch from '~/utils/fetch';


export function info(data) {
  return fetch({
    url: 'system/info',
    method: data ? 'post' : 'get',
    data
  });
}

export function theme(data) {
  return fetch({
    url: 'system/theme',
    method: data ? 'post' : 'get',
    data
  });
}

export function withdraw(data) {
  return fetch({
    url: 'system/withdraw',
    method: data ? 'post' : 'get',
    data
  });
}


export function domain(data) {
  return fetch({
    url: 'system/domain',
    method: data ? 'post' : 'get',
    data
  });
}

export function order(data) {
  return fetch({
    url: 'system/order',
    method: data ? 'post' : 'get',
    data
  });
}

export function api(data) {
  return fetch({
    url: 'system/api',
    method: data ? 'post' : 'get',
    data
  });
}

export function pay(data) {
  return fetch({
    url: 'system/pay',
    method: data ? 'post' : 'get',
    data
  });
}

export function message(data) {
  return fetch({
    url: 'system/message',
    method: data ? 'post' : 'get',
    data
  });
}

export function vcode(data) {
  return fetch({
    url: 'system/vcode',
    method: data ? 'post' : 'get',
    data
  });
}

export function advanced(data) {
  return fetch({
    url: 'system/advanced',
    method: data ? 'post' : 'get',
    data
  });
}

export function email(data) {
  return fetch({
    url: 'system/email',
    method: data ? 'post' : 'get',
    data
  });
}

export function sms(data) {
  return fetch({
    url: 'system/sms',
    method: data ? 'post' : 'get',
    data
  });
}

export function security(data) {
  return fetch({
    url: 'system/security',
    method: data ? 'post' : 'get',
    data
  });
}

export function storage(data) {
  return fetch({
    url: 'system/storage',
    method: data ? 'post' : 'get',
    data
  });
}

export function wechat(data) {
  return fetch({
    url: 'system/wechat',
    method: data ? 'post' : 'get',
    data
  });
}


export function filter_words(data) {
  return fetch({
    url: 'system/filter_words',
    method: data ? 'post' : 'get',
    data,
  });
}

export function emailTest(to) {
  return fetch({
    url: 'system/email/test',
    method: 'post',
    data: {to}
  });
}

export function wechatTest(open_id, action) {
  return fetch({
    url: 'system/wechat/test',
    method: 'post',
    data: {open_id, action}
  });
}

export function orderClean(day) {
  return fetch({
    url: 'system/order/clean',
    method: 'post',
    data: {day}
  });
}

export function homeEdit(type, data = null) {
  return fetch({
    url: 'system/home/' + type,
    method: data ? 'post' : 'get',
    data,
  });
}

export function enableTheme(id, enabled) {
  return fetch({
    url: 'system/theme/enable',
    method: 'post',
    data: {
      id,
      enabled
    }
  });
}

export function deleteOrders(date){
  return fetch({
    url: 'system/delete/orders',
    method: 'post',
    data: {
      date
    }
  });
}

export function deleteWithdraws(date){
  return fetch({
    url: 'system/delete/withdraws',
    method: 'post',
    data: {
      date
    }
  });
}

export function deleteFundRecords(date){
  return fetch({
    url: 'system/delete/fund_records',
    method: 'post',
    data: {
      date
    }
  });
}

export function deleteLogs(date){
  return fetch({
    url: 'system/delete/logs',
    method: 'post',
    data: {
      date
    }
  });
}