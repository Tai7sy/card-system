import fetch from '~/utils/fetch';

const PAYWAY_ENABLED = {
  DISABLED: {
    id: 0,
    name: '禁用',
    type: 'info'
  },
  PC: {
    id: 1,
    name: '电脑端',
    type: 'primary'
  },
  MOBILE: {
    id: 2,
    name: '手机端',
    type: 'primary'
  },
  PC_MOBILE: {
    id: 3, // 位运算 = 1|2 = 3
    name: '电脑+手机',
    type: 'primary'
  },
  getEnabled (id) {
    for (const key in this) {
      if (Object.prototype.hasOwnProperty.call(this, key) && this[key].id === id) {
        return this[key]
      }
    }
    return { id, name: '未知状态', type: 'error' };
  }
};

const TYPE = {
  SHOP: 1,
  API: 2,
};

export { PAYWAY_ENABLED, TYPE }

export function getList (params) {
  return fetch({
    url: 'payway',
    method: 'get',
    params
  });
}


export function edit (params) {
  return fetch({
    url: 'payway/edit',
    method: 'post',
    data: params
  });
}


export function sort (id, sort) {
  return fetch({
    url: 'payway/sort',
    method: 'post',
    data: {
      id, sort
    }
  });
}

export function enable (ids, enabled) {
  return fetch({
    url: 'payway/enable',
    method: 'post',
    data: { ids, enabled }
  });
}

export function deleteAction (ids) {
  return fetch({
    url: 'payway/delete',
    method: 'post',
    data: { ids }
  });
}
