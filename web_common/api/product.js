import fetch from '~/utils/fetch';

const DELIVERY = {
  AUTO: 0,
  MANUAL: 1,
}

export { DELIVERY }

export function getList (params) {
  return fetch({
    url: 'product',
    method: 'get',
    params
  });
}

export function category_edit (id, category_id) {
  return fetch({
    url: 'product/category',
    method: 'post',
    data: { id, category_id }
  });
}

export function edit (params) {
  return fetch({
    url: 'product/edit',
    method: 'post',
    data: params
  });
}


export function enable (ids, enabled) {
  return fetch({
    url: 'product/enable',
    method: 'post',
    data: { ids, enabled }
  });
}

export function retail_batch_open_enable (ids, enabled) {
  return fetch({
    url: 'product/retail_batch_open_enable',
    method: 'post',
    data: { ids, enabled }
  });
}

export function retail_public_open_enable (ids, enabled) {
  return fetch({
    url: 'product/retail_public_open_enable',
    method: 'post',
    data: { ids, enabled }
  });
}


export function retail_batch_add_product (data) {
  return fetch({
    url: 'product/retail_batch_add_product',
    method: 'post',
    data
  });
}

export function retail_set_sort (ids, sort) {
  return fetch({
    url: 'product/retail_set_sort',
    method: 'post',
    data: { ids, sort }
  });
}

export function retail_set_tag (ids, tag) {
  return fetch({
    url: 'product/retail_set_tag',
    method: 'post',
    data: { ids, tag }
  });
}


// user_id 用于管理端获取当前用户一键添加过的商品
export function search_shop_retail (params) {
  return fetch({
    url: 'product/search_shop_retail',
    method: 'get',
    params
  });
}

export function sort (id, sort) {
  return fetch({
    url: 'product/sort',
    method: 'post',
    data: { id, sort }
  });
}

// 用于手动发货设置库存, 一般情况请勿设置
export function setCount (id, count) {
  return fetch({
    url: 'product/set_count',
    method: 'post',
    data: { id, count }
  });
}


export function deleteAction (ids) {
  return fetch({
    url: 'product/delete',
    method: 'post',
    data: { ids }
  });
}

export function countSync (user_id) {
  return fetch({
    url: 'product/count/sync',
    method: 'post',
    data: { user_id }
  });
}


export function check_retail_code (code) {
  return fetch({
    url: 'product/retail/check',
    method: 'get',
    params: { code }
  });
}
