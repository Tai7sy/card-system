import fetch from '../utils/fetch';

const couponStatus = {
  STATUS_NORMAL: 0,
  STATUS_USED: 2
};
export { couponStatus }

const couponType = {
  TYPE_ONETIME: 0,
  TYPE_REPEAT: 1,
};
export { couponType }

const couponDiscountType = {
  AMOUNT: 0,
  PERCENT: 1
};
export { couponDiscountType }

export function getList (params) {
  return fetch({
    url: 'coupon',
    method: 'post',
    data: params
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
