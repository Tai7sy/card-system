import fetch from '~/utils/fetch';

const COUPON_STATUS = {
  STATUS_NORMAL: 0,
  STATUS_USED: 2
};

const COUPON_TYPE = {
  TYPE_ONETIME: 0,
  TYPE_REPEAT: 1,
};

const COUPON_DISCOUNT_TYPE = {
  AMOUNT: 0,
  PERCENT: 1
};

export { COUPON_STATUS, COUPON_TYPE, COUPON_DISCOUNT_TYPE }

export function getList (params) {
  return fetch({
    url: 'coupon',
    method: 'get',
    params
  });
}

export function create (params) {
  return fetch({
    url: 'coupon/create',
    method: 'post',
    data: params
  });
}

export function edit (params) {
  return fetch({
    url: 'coupon/edit',
    method: 'post',
    data: params
  });
}


export function enable (ids, enabled) {
  return fetch({
    url: 'coupon/enable',
    method: 'post',
    data: { ids, enabled }
  });
}


export function deleteAction (ids) {
  return fetch({
    url: 'coupon/delete',
    method: 'post',
    data: { ids }
  });
}
