import fetch from '~/utils/fetch';
const ID_INNER_RETAIL = -1;
export { ID_INNER_RETAIL };
export function stat(params) {
  return fetch({
    url: '/pay/stat',
    method: 'get',
    params
  });
}
export function getList(params) {
  return fetch({
    url: 'pay/user',
    method: 'get',
    params
  });
}
export function fee(user_id, pay_id, fee) {
  return fetch({
    url: '/pay/user/fee',
    method: 'post',
    data: {
      user_id,
      pay_id,
      fee
    }
  });
}
export function defaultFee(user_id, pay_id) {
  return fetch({
    url: '/pay/user/fee/default',
    method: 'post',
    data: {
      user_id,
      pay_id
    }
  });
}
export function enable(user_id, pay_ids, enabled) {
  return fetch({
    url: '/pay/user/enable',
    method: 'post',
    data: {
      user_id,
      pay_ids,
      enabled
    }
  });
}
